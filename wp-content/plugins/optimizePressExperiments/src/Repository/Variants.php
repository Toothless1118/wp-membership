<?php

/**
 * Repository class for Variants DB table.
 */
class OptimizePressStats_Repository_Variants
{
    /**
     * Return variants for $experimentId.
     * @param  integer $experimentId
     * @param  boolean $includeOriginal
     * @return array|null
     */
    public function getVariantsForExperiment($experimentId, $includeOriginal = true)
    {
        global $wpdb;

        if ($includeOriginal) {
            $query = $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` WHERE experiment_id = %d", $experimentId
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` WHERE experiment_id = %d AND page_id <> original_page_id", $experimentId
            );
        }

        return $wpdb->get_results($query, OBJECT);
    }

    /**
     * Return IDs of all pages that are already in some kind of an experiment.
     * @return array
     */
    public function getAllTakenPageIds($experimentId = 0)
    {
        global $wpdb;

        if (empty($experimentId)) {
            return $wpdb->get_col("SELECT `page_id` FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants`");
        } else {
            return $wpdb->get_col($wpdb->prepare("SELECT `page_id` FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` WHERE `experiment_id` <> %d", $experimentId));
        }
    }

    /**
     * Return active variant for given page ID.
     * @param  integer $pageId
     * @return array|null
     */
    public function getVariantForPage($pageId)
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT `v`.* FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` AS `v` LEFT JOIN `{$wpdb->prefix}optimizepress_stats_experiments` AS `e` ON `v`.`experiment_id` = `e`.`id` WHERE `v`.`page_id` = %d AND `e`.`status` = 2 AND (`e`.`start_date` <= CURDATE() OR `e`.`start_date` IS NULL) AND (`e`.`end_date` >= CURDATE() OR `e`.`end_date` IS NULL)", $pageId
            ), OBJECT
        );
    }

    /**
     * Create variant. Return created variant ID.
     * @param  integer $experimentId
     * @param  integer $pageId
     * @param  integer $originalPageId
     * @param  string $name
     * @return integer
     */
    public function createVariant($experimentId, $pageId, $originalPageId, $name)
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare(
            "INSERT INTO `{$wpdb->prefix}optimizepress_stats_experiment_variants` (`experiment_id`, `page_id`, `original_page_id`, `name`) VALUES ('%d', '%d', '%d', '%s')",
            $experimentId,
            $pageId,
            $originalPageId,
            $name
        ));

        return $wpdb->insert_id;
    }

    /**
     * Delete all variants for given experiment ID.
     * @param  integer $experimentId
     * @return boolean
     */
    public function deleteVariantsForExperiment($experimentId)
    {
        global $wpdb;

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM `{$wpdb->prefix}optimizepress_stats_experiment_variants` WHERE `experiment_id` = '%d'",
            $experimentId
        ));
    }
}