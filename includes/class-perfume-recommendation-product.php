<?php
class Perfume_Recommendation_Product {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // اضافه کردن ویژگی‌های سفارشی به محصولات
        add_action('init', array($this, 'register_product_attributes'));
        
        // اضافه کردن فیلدهای جستجو به محصولات
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_search_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_search_fields'));
        
        // اضافه کردن فیلتر جستجو
        add_filter('posts_where', array($this, 'custom_search_query'), 10, 2);
    }

    public function register_product_attributes() {
        // ثبت ویژگی‌های سفارشی برای محصولات
        $attributes = array(
            'perfume_temperature' => 'طبع عطر',
            'perfume_age_range' => 'محدوده سنی',
            'perfume_smoker_friendly' => 'مناسب افراد سیگاری',
            'perfume_skin_tone' => 'رنگ پوست',
            'perfume_personality' => 'خصوصیات اخلاقی',
            'perfume_notes' => 'نت‌های عطر',
            'perfume_occasion' => 'مناسب برای',
            'perfume_season' => 'فصل مناسب',
            'perfume_gender' => 'جنسیت',
            'perfume_longevity' => 'ماندگاری',
            'perfume_sillage' => 'پخش بو'
        );

        foreach ($attributes as $attribute_name => $attribute_label) {
            register_taxonomy(
                $attribute_name,
                'product',
                array(
                    'label' => $attribute_label,
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => $attribute_name),
                    'show_admin_column' => true,
                    'labels' => array(
                        'name' => $attribute_label,
                        'singular_name' => $attribute_label,
                        'menu_name' => $attribute_label,
                        'all_items' => 'همه ' . $attribute_label,
                        'edit_item' => 'ویرایش ' . $attribute_label,
                        'view_item' => 'مشاهده ' . $attribute_label,
                        'update_item' => 'بروزرسانی ' . $attribute_label,
                        'add_new_item' => 'افزودن ' . $attribute_label . ' جدید',
                        'new_item_name' => 'نام ' . $attribute_label . ' جدید',
                        'parent_item' => 'والد ' . $attribute_label,
                        'parent_item_colon' => 'والد ' . $attribute_label . ':',
                        'search_items' => 'جستجوی ' . $attribute_label,
                        'popular_items' => $attribute_label . 'های پرطرفدار',
                        'separate_items_with_commas' => 'جدا کردن ' . $attribute_label . 'ها با کاما',
                        'add_or_remove_items' => 'افزودن یا حذف ' . $attribute_label,
                        'choose_from_most_used' => 'انتخاب از میان پرکاربردترین ' . $attribute_label . 'ها',
                        'not_found' => 'هیچ ' . $attribute_label . 'ی یافت نشد'
                    )
                )
            );
        }
    }

    public function add_search_fields() {
        global $woocommerce, $post;
        
        echo '<div class="options_group">';
        echo '<h4>اطلاعات جستجوی عطر</h4>';
        
        // فیلدهای جستجوی پیشرفته
        woocommerce_wp_textarea_input(array(
            'id' => '_perfume_search_keywords',
            'label' => 'کلمات کلیدی جستجو',
            'description' => 'کلمات کلیدی مرتبط با این عطر را وارد کنید (با کاما جدا کنید)'
        ));
        
        woocommerce_wp_textarea_input(array(
            'id' => '_perfume_search_description',
            'label' => 'توضیحات جستجو',
            'description' => 'توضیحات تکمیلی برای بهبود جستجو'
        ));
        
        echo '</div>';
    }

    public function save_search_fields($post_id) {
        $fields = array(
            '_perfume_search_keywords',
            '_perfume_search_description'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
            }
        }
    }

    public function custom_search_query($where, $wp_query) {
        global $wpdb;

        if (!is_admin() && $wp_query->is_main_query() && $wp_query->is_search()) {
            $search_term = $wp_query->query_vars['s'];
            
            // جستجو در متادیتای محصولات
            $where .= " OR (
                {$wpdb->posts}.ID IN (
                    SELECT post_id 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key IN ('_perfume_search_keywords', '_perfume_search_description')
                    AND meta_value LIKE '%" . esc_sql($search_term) . "%'
                )
            )";
            
            // جستجو در ویژگی‌های محصول
            $where .= " OR (
                {$wpdb->posts}.ID IN (
                    SELECT object_id 
                    FROM {$wpdb->term_relationships} tr
                    INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                    WHERE tt.taxonomy LIKE 'perfume_%'
                    AND t.name LIKE '%" . esc_sql($search_term) . "%'
                )
            )";
        }

        return $where;
    }

    public function get_recommended_products($criteria) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_perfume_temperature',
                    'value' => $criteria['temperature'],
                    'compare' => '='
                ),
                array(
                    'key' => '_perfume_age_range',
                    'value' => $criteria['age_range'],
                    'compare' => '='
                ),
                array(
                    'key' => '_perfume_smoker_friendly',
                    'value' => $criteria['smoker_friendly'],
                    'compare' => '='
                ),
                array(
                    'key' => '_perfume_skin_tone',
                    'value' => $criteria['skin_tone'],
                    'compare' => '='
                ),
                array(
                    'key' => '_perfume_personality',
                    'value' => $criteria['personality'],
                    'compare' => '='
                )
            )
        );

        $products = new WP_Query($args);
        return $products->posts;
    }
} 