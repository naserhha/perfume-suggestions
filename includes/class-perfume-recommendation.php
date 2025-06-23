<?php

class Perfume_Recommendation {
    private $form;
    private $database;

    public function __construct() {
        $this->form = new Perfume_Form();
        $this->database = new Perfume_Database();
    }

    public function init() {
        // Add menu item to WooCommerce products
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add meta box to product edit screen
        add_action('add_meta_boxes', array($this, 'add_perfume_meta_box'));
        
        // Save perfume characteristics
        add_action('save_post', array($this, 'save_perfume_characteristics'));
        
        // Add shortcode documentation
        add_action('admin_notices', array($this, 'admin_notice'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=product',
            __('Perfume Recommendations', 'perfume-recommendation'),
            __('Perfume Recommendations', 'perfume-recommendation'),
            'manage_options',
            'perfume-recommendations',
            array($this, 'render_admin_page')
        );
    }

    public function add_perfume_meta_box() {
        add_meta_box(
            'perfume-characteristics',
            __('Perfume Characteristics', 'perfume-recommendation'),
            array($this, 'render_meta_box'),
            'product',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('perfume_characteristics_nonce', 'perfume_characteristics_nonce');
        
        $characteristics = get_post_meta($post->ID, '_perfume_characteristics', true);
        if (!is_array($characteristics)) {
            $characteristics = array();
        }
        
        $defaults = array(
            'temperature' => '',
            'age_range' => '',
            'smoker_friendly' => '',
            'skin_tone' => '',
            'personality' => ''
        );
        
        $characteristics = wp_parse_args($characteristics, $defaults);
        ?>
        <div class="perfume-characteristics">
            <p>
                <label for="temperature"><?php _e('Temperature', 'perfume-recommendation'); ?></label>
                <select name="perfume_characteristics[temperature]" id="temperature">
                    <option value="warm" <?php selected($characteristics['temperature'], 'warm'); ?>><?php _e('Warm', 'perfume-recommendation'); ?></option>
                    <option value="moderate" <?php selected($characteristics['temperature'], 'moderate'); ?>><?php _e('Moderate', 'perfume-recommendation'); ?></option>
                    <option value="cool" <?php selected($characteristics['temperature'], 'cool'); ?>><?php _e('Cool', 'perfume-recommendation'); ?></option>
                </select>
            </p>

            <p>
                <label for="age_range"><?php _e('Age Range', 'perfume-recommendation'); ?></label>
                <select name="perfume_characteristics[age_range]" id="age_range">
                    <option value="15-25" <?php selected($characteristics['age_range'], '15-25'); ?>><?php _e('15-25 years', 'perfume-recommendation'); ?></option>
                    <option value="25-40" <?php selected($characteristics['age_range'], '25-40'); ?>><?php _e('25-40 years', 'perfume-recommendation'); ?></option>
                    <option value="40+" <?php selected($characteristics['age_range'], '40+'); ?>><?php _e('40+ years', 'perfume-recommendation'); ?></option>
                </select>
            </p>

            <p>
                <label for="smoker_friendly"><?php _e('Smoker Friendly', 'perfume-recommendation'); ?></label>
                <select name="perfume_characteristics[smoker_friendly]" id="smoker_friendly">
                    <option value="1" <?php selected($characteristics['smoker_friendly'], '1'); ?>><?php _e('Yes', 'perfume-recommendation'); ?></option>
                    <option value="0" <?php selected($characteristics['smoker_friendly'], '0'); ?>><?php _e('No', 'perfume-recommendation'); ?></option>
                </select>
            </p>

            <p>
                <label for="skin_tone"><?php _e('Skin Tone', 'perfume-recommendation'); ?></label>
                <select name="perfume_characteristics[skin_tone]" id="skin_tone">
                    <option value="light" <?php selected($characteristics['skin_tone'], 'light'); ?>><?php _e('Light', 'perfume-recommendation'); ?></option>
                    <option value="medium" <?php selected($characteristics['skin_tone'], 'medium'); ?>><?php _e('Medium', 'perfume-recommendation'); ?></option>
                    <option value="dark" <?php selected($characteristics['skin_tone'], 'dark'); ?>><?php _e('Dark', 'perfume-recommendation'); ?></option>
                </select>
            </p>

            <p>
                <label for="personality"><?php _e('Personality', 'perfume-recommendation'); ?></label>
                <select name="perfume_characteristics[personality]" id="personality">
                    <option value="calm" <?php selected($characteristics['personality'], 'calm'); ?>><?php _e('Calm', 'perfume-recommendation'); ?></option>
                    <option value="temperamental" <?php selected($characteristics['personality'], 'temperamental'); ?>><?php _e('Temperamental', 'perfume-recommendation'); ?></option>
                    <option value="balanced" <?php selected($characteristics['personality'], 'balanced'); ?>><?php _e('Balanced and Logical', 'perfume-recommendation'); ?></option>
                </select>
            </p>
        </div>
        <?php
    }

    public function save_perfume_characteristics($post_id) {
        if (!isset($_POST['perfume_characteristics_nonce']) || 
            !wp_verify_nonce($_POST['perfume_characteristics_nonce'], 'perfume_characteristics_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['perfume_characteristics'])) {
            $characteristics = array_map('sanitize_text_field', $_POST['perfume_characteristics']);
            update_post_meta($post_id, '_perfume_characteristics', $characteristics);
            
            // Update the database table
            $this->database->add_perfume(array_merge(
                array('product_id' => $post_id),
                $characteristics
            ));
        }
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Perfume Recommendation System', 'perfume-recommendation'); ?></h1>
            
            <div class="card">
                <h2><?php _e('How to Use', 'perfume-recommendation'); ?></h2>
                <p><?php _e('To add the perfume recommendation form to any page or post, use the following shortcode:', 'perfume-recommendation'); ?></p>
                <code>[perfume_recommendation_form]</code>
                
                <h3><?php _e('Setting up Perfumes', 'perfume-recommendation'); ?></h3>
                <p><?php _e('To set up a perfume for recommendations:', 'perfume-recommendation'); ?></p>
                <ol>
                    <li><?php _e('Create a new product in WooCommerce', 'perfume-recommendation'); ?></li>
                    <li><?php _e('Fill in the Perfume Characteristics meta box with the appropriate values', 'perfume-recommendation'); ?></li>
                    <li><?php _e('Publish the product', 'perfume-recommendation'); ?></li>
                </ol>
            </div>
        </div>
        <?php
    }

    public function admin_notice() {
        $screen = get_current_screen();
        if ($screen->id === 'product') {
            ?>
            <div class="notice notice-info">
                <p><?php _e('Don\'t forget to set the perfume characteristics for this product to enable it in the recommendation system.', 'perfume-recommendation'); ?></p>
            </div>
            <?php
        }
    }
} 