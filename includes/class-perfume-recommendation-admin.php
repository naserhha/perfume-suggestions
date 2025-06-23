<?php
class Perfume_Recommendation_Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            PERFUME_RECOMMENDATION_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            PERFUME_RECOMMENDATION_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            $this->version,
            true
        );
    }

    public function add_perfume_meta_box() {
        add_meta_box(
            'perfume_characteristics',
            'ویژگی‌های عطر',
            array($this, 'render_perfume_meta_box'),
            'product',
            'normal',
            'high'
        );
    }

    public function render_perfume_meta_box($post) {
        wp_nonce_field('perfume_meta_box', 'perfume_meta_box_nonce');

        $temperature = get_post_meta($post->ID, '_perfume_temperature', true);
        $age_range = get_post_meta($post->ID, '_perfume_age_range', true);
        $smoker_friendly = get_post_meta($post->ID, '_perfume_smoker_friendly', true);
        $skin_tone = get_post_meta($post->ID, '_perfume_skin_tone', true);
        $personality = get_post_meta($post->ID, '_perfume_personality', true);
        ?>
        <div class="perfume-meta-box">
            <p>فراموش نکنید که برای فعال‌سازی این محصول در سیستم پیشنهاد، ویژگی‌های عطر را تنظیم کنید.</p>
            
            <div class="form-group">
                <label for="perfume_temperature">طبع</label>
                <select name="perfume_temperature" id="perfume_temperature">
                    <option value="warm" <?php selected($temperature, 'warm'); ?>>گرم</option>
                    <option value="moderate" <?php selected($temperature, 'moderate'); ?>>معتدل</option>
                    <option value="cool" <?php selected($temperature, 'cool'); ?>>خنک</option>
                </select>
            </div>

            <div class="form-group">
                <label for="perfume_age_range">محدوده سنی</label>
                <select name="perfume_age_range" id="perfume_age_range">
                    <option value="15-25" <?php selected($age_range, '15-25'); ?>>15 تا 25 سال</option>
                    <option value="25-40" <?php selected($age_range, '25-40'); ?>>25 تا 40 سال</option>
                    <option value="40+" <?php selected($age_range, '40+'); ?>>40 سال به بالا</option>
                </select>
            </div>

            <div class="form-group">
                <label for="perfume_smoker_friendly">مناسب افراد سیگاری</label>
                <select name="perfume_smoker_friendly" id="perfume_smoker_friendly">
                    <option value="yes" <?php selected($smoker_friendly, 'yes'); ?>>بله</option>
                    <option value="no" <?php selected($smoker_friendly, 'no'); ?>>خیر</option>
                </select>
            </div>

            <div class="form-group">
                <label for="perfume_skin_tone">رنگ پوست</label>
                <select name="perfume_skin_tone" id="perfume_skin_tone">
                    <option value="light" <?php selected($skin_tone, 'light'); ?>>روشن</option>
                    <option value="medium" <?php selected($skin_tone, 'medium'); ?>>گندمی</option>
                    <option value="dark" <?php selected($skin_tone, 'dark'); ?>>تیره</option>
                </select>
            </div>

            <div class="form-group">
                <label for="perfume_personality">خصوصیات اخلاقی</label>
                <select name="perfume_personality" id="perfume_personality">
                    <option value="calm" <?php selected($personality, 'calm'); ?>>آرام</option>
                    <option value="temperamental" <?php selected($personality, 'temperamental'); ?>>تند مزاج و زود رنج</option>
                    <option value="balanced" <?php selected($personality, 'balanced'); ?>>متعادل و منطقی</option>
                </select>
            </div>
        </div>
        <?php
    }

    public function save_perfume_meta($post_id) {
        if (!isset($_POST['perfume_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['perfume_meta_box_nonce'], 'perfume_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'perfume_temperature',
            'perfume_age_range',
            'perfume_smoker_friendly',
            'perfume_skin_tone',
            'perfume_personality'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
} 