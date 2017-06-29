<?php

/**
 * Repository class for Experiments DB table
 */
class OptimizePressStats_Repository_Experiments
{
    /**
     * Return experiment for $pageId with $status.
     * @param  integer $pageId
     * @param  integer $status
     * @return object|null
     */
    public function getExperimentForPage($pageId, $status = null)
    {
        global $wpdb;

        if (null === $status) {
            $query = $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiments` WHERE page_id = %d AND (start_date <= CURDATE() OR start_date IS NULL) AND (end_date >= CURDATE() OR end_date IS NULL)", $pageId
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiments` WHERE page_id = %d AND status = %d AND (start_date <= CURDATE() OR start_date IS NULL) AND (end_date >= CURDATE() OR end_date IS NULL)", $pageId, $status
            );
        }

        return $wpdb->get_row($query, OBJECT);
    }

    /**
     * Return experiment for variation (or original) page ID.
     * @param  integer  $pageId
     * @param  integer $status
     * @return object|null
     */
    public function getExperimentForVariationPage($pageId, $status = null)
    {
        global $wpdb;

        if (null === $status) {
            $query = $wpdb->prepare(
                "SELECT `e`.* FROM `{$wpdb->prefix}optimizepress_stats_experiments` AS `e` LEFT JOIN `{$wpdb->prefix}optimizepress_stats_experiment_variants` AS `v` ON `v`.`experiment_id` = `e`.`id` WHERE `v`.`page_id` = %d AND (`e`.`start_date` <= CURDATE() OR `e`.`start_date` IS NULL) AND (`e`.`end_date` >= CURDATE() OR `e`.`end_date` IS NULL)", $pageId
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT `e`.* FROM `{$wpdb->prefix}optimizepress_stats_experiments` AS `e` LEFT JOIN `{$wpdb->prefix}optimizepress_stats_experiment_variants` AS `v` ON `v`.`experiment_id` = `e`.`id` WHERE `v`.`page_id` = %d AND `e`.`status` = %d AND (`e`.`start_date` <= CURDATE() OR `e`.`start_date` IS NULL) AND (`e`.`end_date` >= CURDATE() OR `e`.`end_date` IS NULL)", $pageId, $status
            );
        }

        return $wpdb->get_row($query, OBJECT);
    }

    /**
     * Return "visit" type experiment for give goal page ID.
     * @param  integer $goalPageId
     * @param  integer $status
     * @return object|null
     */
    public function getExperimentForGoalPage($goalPageId, $status = 2)
    {
        global $wpdb;

        $experiment = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiments` WHERE goal_page_id = %d AND goal_type = 'visit' AND status = %d AND (start_date <= CURDATE() OR start_date IS NULL) AND (end_date >= CURDATE() OR end_date IS NULL)", $goalPageId, $status
            ), OBJECT
        );

        return $experiment;
    }

    /**
     * Return all experiments.
     * @return array
     */
    public function getExperiments()
    {
        global $wpdb;

        $experiments = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiments`", OBJECT);

        return $experiments;
    }

    /**
     * Return experiment for given ID.
     * @param  integer $experimentId
     * @return object
     */
    public function getExperiment($experimentId)
    {
        global $wpdb;

        $experiment = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiments` WHERE id = %d", $experimentId
            ), OBJECT
        );

        return $experiment;
    }

    /**
     * Create new experiment DB entry and return created experiment ID.
     * @param  integer  $pageId
     * @param  string  $name
     * @param  string  $description
     * @param  string  $goalType
     * @param  integer $goalPageId
     * @param  integer $status
     * @param  string  $startDate expects date in Y-m-d format
     * @param  string  $endDate expects date in Y-m-d format
     * @return integer
     */
    public function createExperiment($pageId, $name, $description, $goalType = 'optin', $goalPageId = 0, $status = 0, $startDate = null, $endDate = null)
    {
        global $wpdb;

        if (empty($startDate)) {
            $startDate = date('Y-m-d');
        }

        if (empty($endDate)) {
            $endDate = 'NULL';
        }

        $query = $wpdb->prepare(
            "INSERT INTO `{$wpdb->prefix}optimizepress_stats_experiments` (`page_id`, `name`, `description`, `goal_type`, `goal_page_id`, `status`, `start_date`, `end_date`) VALUES ('%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s')",
            $pageId,
            $name,
            $description,
            $goalType,
            $goalPageId,
            $status,
            $startDate,
            $endDate
        );

        // Replacing "'NULL'"" string with MySQL NULL literal
        $wpdb->query(str_replace("'NULL'", "NULL", $query));

        return $wpdb->insert_id;
    }

    /**
     * Update experiment DB entry.
     * @param  integer  $experimentId
     * @param  integer  $pageId
     * @param  string  $name
     * @param  string  $description
     * @param  string  $goalType
     * @param  integer $goalPageId
     * @param  integer $status
     * @param  string  $startDate expects date in Y-m-d format
     * @param  string  $endDate expects date in Y-m-d format
     * @return boolean
     */
    public function updateExperiment($experimentId, $pageId, $name, $description, $goalType = 'optin', $goalPageId = 0, $status = 0, $startDate = null, $endDate = null)
    {
        global $wpdb;

        if (empty($startDate)) {
            $startDate = date('Y-m-d');
        }

        if (empty($endDate)) {
            $endDate = 'NULL';
        }

        $query = $wpdb->prepare(
            "UPDATE `{$wpdb->prefix}optimizepress_stats_experiments` SET `page_id` = '%d', `name` = '%s', `description` = '%s', `goal_type` = '%s', `goal_page_id` = '%d', `status` = '%d', `start_date` = '%s', `end_date` = '%s' WHERE `id` = '%d'",
            $pageId,
            $name,
            $description,
            $goalType,
            $goalPageId,
            $status,
            $startDate,
            $endDate,
            $experimentId
        );

        // Replacing "'NULL'"" string with MySQL NULL literal
        return $wpdb->query(str_replace("'NULL'", "NULL", $query));
    }

    /**
     * Swtich experiments status. 0 for stopped, 1 for paused and 2 for running.
     * @param  integer $experimentId
     * @param  integer $status
     * @return boolean
     */
    public function switchExperimentStatus($experimentId, $status)
    {
        global $wpdb;

        return $wpdb->query($wpdb->prepare(
            "UPDATE `{$wpdb->prefix}optimizepress_stats_experiments` SET `status` = '%d' WHERE `id` = '%d'",
            $status,
            $experimentId
        ));
    }

    /**
     * Return IDs of all pages that are already goal page in some kind of an experiment.
     * @return array
     */
    public function getAllTakenGoalPostIds($experimentId = 0)
    {
        global $wpdb;

        if (empty($experimentId)) {
            return $wpdb->get_col("SELECT `goal_page_id` FROM `{$wpdb->prefix}optimizepress_stats_experiments` WHERE `goal_page_id` <> 0 AND `goal_type` = 'visit'");
        } else {
            return $wpdb->get_col($wpdb->prepare("SELECT `goal_page_id` FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` WHERE `id` <> %d AND `goal_page_id` <> 0 AND `goal_type` = 'visit'", $experimentId));
        }
    }

    /**
     * Delete experiment.
     * @param  integer $experimentId
     * @return void
     */
    public function deleteExperiment($experimentId)
    {
        global $wpdb;

        // Delete experiment
        $wpdb->query($wpdb->prepare(
            "DELETE FROM `{$wpdb->prefix}optimizepress_stats_experiments` WHERE `id` = '%d'",
            $experimentId
        ));

        // Delete its variants
        $wpdb->query($wpdb->prepare(
            "DELETE FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` WHERE `experiment_id` = '%d'",
            $experimentId
        ));

        // Finally, delete last chosen variant index for experiment
        delete_option('op_experiment_es_index_' . $experimentId);
    }
}