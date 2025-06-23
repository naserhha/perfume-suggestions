<?php

class Perfume_Form {
    public function __construct() {
        add_shortcode('perfume_recommendation_form', array($this, 'render_form'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_submit_perfume_form', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_submit_perfume_form', array($this, 'handle_form_submission'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'perfume-recommendation-style',
            PERFUME_RECOMMENDATION_PLUGIN_URL . 'assets/css/style.css',
            array(),
            PERFUME_RECOMMENDATION_VERSION
        );

        wp_enqueue_script(
            'perfume-recommendation-script',
            PERFUME_RECOMMENDATION_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            PERFUME_RECOMMENDATION_VERSION,
            true
        );

        wp_localize_script('perfume-recommendation-script', 'perfumeAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('perfume_recommendation_nonce')
        ));
    }

    public function render_form() {
        ob_start();
        ?>
        <div class="perfume-recommendation-form">
            <form id="perfume-recommendation-form" method="post">
                <div class="form-group">
                    <label for="temperature"><?php _e('Temperature Preference', 'perfume-recommendation'); ?></label>
                    <select name="temperature" id="temperature" required>
                        <option value="warm"><?php _e('Warm', 'perfume-recommendation'); ?></option>
                        <option value="moderate"><?php _e('Moderate', 'perfume-recommendation'); ?></option>
                        <option value="cool"><?php _e('Cool', 'perfume-recommendation'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="age_range"><?php _e('Age Range', 'perfume-recommendation'); ?></label>
                    <select name="age_range" id="age_range" required>
                        <option value="15-25"><?php _e('15-25 years', 'perfume-recommendation'); ?></option>
                        <option value="25-40"><?php _e('25-40 years', 'perfume-recommendation'); ?></option>
                        <option value="40+"><?php _e('40+ years', 'perfume-recommendation'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="smoker_friendly"><?php _e('Smoker Friendly', 'perfume-recommendation'); ?></label>
                    <select name="smoker_friendly" id="smoker_friendly" required>
                        <option value="1"><?php _e('Yes', 'perfume-recommendation'); ?></option>
                        <option value="0"><?php _e('No', 'perfume-recommendation'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="skin_tone"><?php _e('Skin Tone', 'perfume-recommendation'); ?></label>
                    <select name="skin_tone" id="skin_tone" required>
                        <option value="light"><?php _e('Light', 'perfume-recommendation'); ?></option>
                        <option value="medium"><?php _e('Medium', 'perfume-recommendation'); ?></option>
                        <option value="dark"><?php _e('Dark', 'perfume-recommendation'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="personality"><?php _e('Personality', 'perfume-recommendation'); ?></label>
                    <select name="personality" id="personality" required>
                        <option value="calm"><?php _e('Calm', 'perfume-recommendation'); ?></option>
                        <option value="temperamental"><?php _e('Temperamental', 'perfume-recommendation'); ?></option>
                        <option value="balanced"><?php _e('Balanced and Logical', 'perfume-recommendation'); ?></option>
                    </select>
                </div>

                <button type="submit" class="button"><?php _e('Get Recommendations', 'perfume-recommendation'); ?></button>
            </form>

            <div id="perfume-recommendations" class="recommendations-container" style="display: none;">
                <!-- Recommendations will be loaded here -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_form_submission() {
        check_ajax_referer('perfume_recommendation_nonce', 'nonce');

        $characteristics = array(
            'temperature' => sanitize_text_field($_POST['temperature']),
            'age_range' => sanitize_text_field($_POST['age_range']),
            'smoker_friendly' => intval($_POST['smoker_friendly']),
            'skin_tone' => sanitize_text_field($_POST['skin_tone']),
            'personality' => sanitize_text_field($_POST['personality'])
        );

        $database = new Perfume_Database();
        $recommendations = $database->get_recommended_perfumes($characteristics);

        if (!empty($recommendations)) {
            $html = '<div class="recommendations-grid">';
            foreach ($recommendations as $perfume) {
                $product = wc_get_product($perfume->product_id);
                if ($product) {
                    $html .= $this->render_perfume_card($product);
                }
            }
            $html .= '</div>';
            wp_send_json_success($html);
        } else {
            wp_send_json_error(__('No perfumes found matching your preferences.', 'perfume-recommendation'));
        }
    }

    private function render_perfume_card($product) {
        ob_start();
        ?>
        <div class="perfume-card">
            <div class="perfume-image">
                <?php echo $product->get_image(); ?>
            </div>
            <div class="perfume-details">
                <h3><?php echo esc_html($product->get_name()); ?></h3>
                <p class="price"><?php echo $product->get_price_html(); ?></p>
                <div class="perfume-actions">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="button"><?php _e('View Details', 'perfume-recommendation'); ?></a>
                    <?php woocommerce_template_loop_add_to_cart(); ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
} 