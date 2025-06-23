<?php
class Perfume_Recommendation_Ajax {
    public function __construct() {
        add_action('wp_ajax_get_perfume_recommendations', array($this, 'get_recommendations'));
        add_action('wp_ajax_nopriv_get_perfume_recommendations', array($this, 'get_recommendations'));
    }

    public function get_recommendations() {
        check_ajax_referer('perfume_recommendation_nonce', 'nonce');

        $criteria = array(
            'temperature' => sanitize_text_field($_POST['temperature']),
            'age_range' => sanitize_text_field($_POST['age_range']),
            'smoker_friendly' => sanitize_text_field($_POST['smoker_friendly']),
            'skin_tone' => sanitize_text_field($_POST['skin_tone']),
            'personality' => sanitize_text_field($_POST['personality'])
        );

        $product = new Perfume_Recommendation_Product('perfume-recommendation', PERFUME_RECOMMENDATION_VERSION);
        $recommended_products = $product->get_recommended_products($criteria);

        $results = array();
        foreach ($recommended_products as $product) {
            $product_obj = wc_get_product($product->ID);
            if (!$product_obj) continue;

            $results[] = array(
                'id' => $product->ID,
                'title' => $product->post_title,
                'description' => $this->get_product_description($product_obj),
                'link' => get_permalink($product->ID),
                'add_to_cart_url' => $product_obj->add_to_cart_url(),
                'price' => $product_obj->get_price_html(),
                'image' => get_the_post_thumbnail_url($product->ID, 'medium'),
                'attributes' => $this->get_product_attributes($product_obj)
            );
        }

        wp_send_json_success($results);
    }

    private function get_product_description($product) {
        $description = '';
        
        // اضافه کردن نت‌های عطر
        $notes = get_the_terms($product->get_id(), 'perfume_notes');
        if ($notes && !is_wp_error($notes)) {
            $description .= '<strong>نت‌های عطر:</strong> ';
            $description .= implode('، ', wp_list_pluck($notes, 'name')) . '<br>';
        }
        
        // اضافه کردن مناسب برای
        $occasions = get_the_terms($product->get_id(), 'perfume_occasion');
        if ($occasions && !is_wp_error($occasions)) {
            $description .= '<strong>مناسب برای:</strong> ';
            $description .= implode('، ', wp_list_pluck($occasions, 'name')) . '<br>';
        }
        
        // اضافه کردن فصل مناسب
        $seasons = get_the_terms($product->get_id(), 'perfume_season');
        if ($seasons && !is_wp_error($seasons)) {
            $description .= '<strong>فصل مناسب:</strong> ';
            $description .= implode('، ', wp_list_pluck($seasons, 'name')) . '<br>';
        }
        
        // اضافه کردن جنسیت
        $genders = get_the_terms($product->get_id(), 'perfume_gender');
        if ($genders && !is_wp_error($genders)) {
            $description .= '<strong>جنسیت:</strong> ';
            $description .= implode('، ', wp_list_pluck($genders, 'name')) . '<br>';
        }
        
        // اضافه کردن ماندگاری
        $longevity = get_the_terms($product->get_id(), 'perfume_longevity');
        if ($longevity && !is_wp_error($longevity)) {
            $description .= '<strong>ماندگاری:</strong> ';
            $description .= implode('، ', wp_list_pluck($longevity, 'name')) . '<br>';
        }
        
        // اضافه کردن پخش بو
        $sillage = get_the_terms($product->get_id(), 'perfume_sillage');
        if ($sillage && !is_wp_error($sillage)) {
            $description .= '<strong>پخش بو:</strong> ';
            $description .= implode('، ', wp_list_pluck($sillage, 'name')) . '<br>';
        }
        
        return $description;
    }

    private function get_product_attributes($product) {
        $attributes = array();
        
        // ویژگی‌های اصلی
        $attributes['temperature'] = get_post_meta($product->get_id(), '_perfume_temperature', true);
        $attributes['age_range'] = get_post_meta($product->get_id(), '_perfume_age_range', true);
        $attributes['smoker_friendly'] = get_post_meta($product->get_id(), '_perfume_smoker_friendly', true);
        $attributes['skin_tone'] = get_post_meta($product->get_id(), '_perfume_skin_tone', true);
        $attributes['personality'] = get_post_meta($product->get_id(), '_perfume_personality', true);
        
        return $attributes;
    }
} 