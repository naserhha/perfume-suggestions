<?php

class Perfume_Database {
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table for perfumes
        $table_perfumes = $wpdb->prefix . 'perfume_recommendation_perfumes';
        $sql_perfumes = "CREATE TABLE IF NOT EXISTS $table_perfumes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            temperature varchar(20) NOT NULL,
            age_range varchar(20) NOT NULL,
            smoker_friendly tinyint(1) NOT NULL,
            skin_tone varchar(20) NOT NULL,
            personality varchar(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY product_id (product_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_perfumes);
    }

    public function add_perfume($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'perfume_recommendation_perfumes';
        
        return $wpdb->insert(
            $table_name,
            array(
                'product_id' => $data['product_id'],
                'temperature' => $data['temperature'],
                'age_range' => $data['age_range'],
                'smoker_friendly' => $data['smoker_friendly'],
                'skin_tone' => $data['skin_tone'],
                'personality' => $data['personality']
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s')
        );
    }

    public function get_recommended_perfumes($characteristics) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'perfume_recommendation_perfumes';
        
        $query = $wpdb->prepare(
            "SELECT p.*, pr.* 
            FROM {$wpdb->posts} p 
            JOIN $table_name pr ON p.ID = pr.product_id 
            WHERE p.post_type = 'product' 
            AND p.post_status = 'publish'
            AND pr.temperature = %s
            AND pr.age_range = %s
            AND pr.smoker_friendly = %d
            AND pr.skin_tone = %s
            AND pr.personality = %s
            LIMIT 6",
            $characteristics['temperature'],
            $characteristics['age_range'],
            $characteristics['smoker_friendly'],
            $characteristics['skin_tone'],
            $characteristics['personality']
        );

        return $wpdb->get_results($query);
    }
} 