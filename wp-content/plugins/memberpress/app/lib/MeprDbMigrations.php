<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprDbMigrations {
  private $migrations;

  public static function run($from_version, $to_version) {
    global $wpdb;

    $mig = new MeprDbMigrations();
    $migration_versions = $mig->get_migration_versions($from_version, $to_version);

    if(empty($migration_versions)) { return; }

    foreach($migration_versions as $migration_version) {
      $config = $mig->get_migration($migration_version);

      foreach($config['migrations'] as $callbacks) {
        // Store current migration config in the database so the
        // progress AJAX endpoint can see what's going on
        set_transient('mepr_current_migration', $callbacks, MeprUtils::hours(4));
        call_user_func(array($mig, $callbacks['migration']));
        delete_transient('mepr_current_migration');
      }
    }
  }

  public static function show_upgrade_ui($from_version, $to_version) {
    $mig = new MeprDbMigrations();
    $migration_versions = $mig->get_migration_versions($from_version, $to_version);

    if(empty($migration_versions)) { return; }

    foreach($migration_versions as $migration_version) {
      $config = $mig->get_migration($migration_version);
      if(call_user_func(array($mig, $config['show_ui']))) {
        return true;
      }
    }

    return false;
  }

  public function __construct() {
    // ensure migration versions are sequential when adding new migration callbacks
    $this->migrations = array(
      '1.3.0' => array(
        'show_ui' => 'show_ui_001_002',
        'migrations' => array(
          array(
            'migration' => 'create_and_migrate_subscriptions_table_001',
            'check'     => 'check_create_and_migrate_subscriptions_table_001',
            'message'   => __('Updating Subscriptions', 'memberpress'),
          ),
          array(
            'migration' => 'create_and_migrate_members_table_002',
            'check'     => 'check_create_and_migrate_members_table_002',
            'message'   => __('Optimizing Member Data', 'memberpress'),
          ),
        ),
      ),
    );
  }

  public function get_migration_versions($from_version, $to_version) {
    $mig_versions = array_keys($this->migrations);

    $versions = array();
    foreach($mig_versions as $mig_version) {
      if(version_compare($from_version, $mig_version, '<')) {
         //version_compare($to_version, $mig_version, '>='))
        $versions[] = $mig_version;
      }
    }

    return $versions;
  }

  public function get_migration($version) {
    return $this->migrations[$version];
  }

/** SHOW UI **/
  public function show_ui_001_002() {
    global $wpdb;
    $mepr_db = new MeprDb();

    $q = "
      SELECT COUNT(*)
        FROM {$wpdb->posts}
       WHERE post_type='mepr-subscriptions'
    ";

    if($mepr_db->table_exists($mepr_db->subscriptions)) {
      $q .= "
        AND ID NOT IN (
          SELECT id
            FROM {$mepr_db->subscriptions}
        )
      ";
    }

    $subs_left = $wpdb->get_var($q);

    $q = "
      SELECT COUNT(*)
        FROM {$wpdb->users}
    ";

    if($mepr_db->table_exists($mepr_db->members)) {
      $q .= "
        WHERE ID NOT IN (
         SELECT user_id
           FROM {$mepr_db->members}
        )
      ";
    }

    $members_left = $wpdb->get_var($q);

    $already_migrating = get_transient('mepr_migrating');

    return (
      !empty($already_migrating) ||
      ($subs_left >= 100) ||
      ($members_left >= 100)
    );
  }

/** CHECKS **/
  public function check_create_and_migrate_subscriptions_table_001() {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type=%s", 'mepr-subscriptions');
    $total = $wpdb->get_var($q); //Need to account for 0's below

    $q = "SELECT COUNT(*) FROM {$mepr_db->subscriptions}";
    $completed = $wpdb->get_var($q);

    $progress = 100;
    if($total > 0) {
      $progress = (int)(($completed / $total) * 100);
      $progress = min($progress, 100);
    }

    return compact('completed','total','progress');
  }

  public function check_create_and_migrate_members_table_002() {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = "SELECT COUNT(*) FROM {$wpdb->users}";
    $total = $wpdb->get_var($q); //Should never get a 0 here

    $q = "SELECT COUNT(*) FROM {$mepr_db->members}";
    $completed = $wpdb->get_var($q);

    $progress = (int)(($completed / $total) * 100);
    $progress = min($progress, 100);

    return compact('completed','total','progress');
  }

/** MIGRATIONS **/
  public function create_and_migrate_subscriptions_table_001() {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    MeprSubscription::upgrade_table(null,true);

    $max_sub_id = $wpdb->get_var("SELECT max(ID) FROM {$wpdb->posts} WHERE post_type='mepr-subscriptions'");

    if(!empty($max_sub_id)) {
      $max_sub_id = (int)$max_sub_id + 1; // Just in case
      $wpdb->query("ALTER TABLE {$mepr_db->subscriptions} AUTO_INCREMENT={$max_sub_id}");
    }
  }

  public function create_and_migrate_members_table_002() {
    MeprUser::update_all_member_data(true);
  }

  //public function create_and_migrate_reports_tables_003() {
  //  // TODO: This will happen in a future release
  //}

  //public function delete_old_subscriptions_004() {
  //  //// Do we really want to do this destructive action? Maybe not yet :) ...
  //  //$res = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id IN (SELECT p.ID FROM {$wpdb->posts} AS p WHERE p.post_type='mepr-subscriptions')");
  //  //if(MeprUtils::is_wp_error($res)) {
  //  //  set_transient('mepr_migration_error',$res->get_error_message(),(60*60));
  //  //  $wpdb->query('ROLLBACK');
  //  //  return false;
  //  //}
  //  //$wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type='mepr-subscriptions'");
  //  //if(MeprUtils::is_wp_error($res)) {
  //  //  set_transient('mepr_migration_error',$res->get_error_message(),(60*60));
  //  //  $wpdb->query('ROLLBACK');
  //  //  return false;
  //  //}
  //}
}

