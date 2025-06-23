<?php
/**
 * Perfume Recommendation Engine
 *
 * @package Perfume_Recommendation
 * @author Mohammad Nasser Haji Hashemabad
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Perfume_Recommendation_Engine {
    private $database;

    public function __construct() {
        $this->database = new Perfume_Database();
    }

    public function get_recommendations($characteristics) {
        // Get base recommendations from database
        $recommendations = $this->database->get_recommended_perfumes($characteristics);
        
        // If no exact matches found, try to find similar perfumes
        if (empty($recommendations)) {
            $recommendations = $this->get_similar_perfumes($characteristics);
        }
        
        return $recommendations;
    }

    private function get_similar_perfumes($characteristics) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'perfume_recommendation_perfumes';
        
        // Build query to find similar perfumes
        $query = $wpdb->prepare(
            "SELECT p.*, pr.* 
            FROM {$wpdb->posts} p 
            JOIN $table_name pr ON p.ID = pr.product_id 
            WHERE p.post_type = 'product' 
            AND p.post_status = 'publish'
            AND (
                pr.temperature = %s
                OR pr.age_range = %s
                OR pr.skin_tone = %s
                OR pr.personality = %s
            )
            LIMIT 6",
            $characteristics['temperature'],
            $characteristics['age_range'],
            $characteristics['skin_tone'],
            $characteristics['personality']
        );

        return $wpdb->get_results($query);
    }

    public function calculate_match_score($perfume, $characteristics) {
        $score = 0;
        
        // Temperature match
        if ($perfume->temperature === $characteristics['temperature']) {
            $score += 2;
        }
        
        // Age range match
        if ($perfume->age_range === $characteristics['age_range']) {
            $score += 2;
        }
        
        // Smoker friendly match
        if ($perfume->smoker_friendly == $characteristics['smoker_friendly']) {
            $score += 1;
        }
        
        // Skin tone match
        if ($perfume->skin_tone === $characteristics['skin_tone']) {
            $score += 2;
        }
        
        // Personality match
        if ($perfume->personality === $characteristics['personality']) {
            $score += 2;
        }
        
        return $score;
    }
} 