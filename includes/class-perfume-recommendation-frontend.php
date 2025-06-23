<?php
class Perfume_Recommendation_Frontend {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            PERFUME_RECOMMENDATION_PLUGIN_URL . 'assets/css/style.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            PERFUME_RECOMMENDATION_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script($this->plugin_name, 'perfume_recommendation', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('perfume_recommendation_nonce'),
            'loading_text' => 'در حال بارگذاری...',
            'error_text' => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
            'no_results_text' => 'هیچ عطری با مشخصات شما یافت نشد.',
            'view_details_text' => 'مشاهده جزئیات',
            'add_to_cart_text' => 'افزودن به سبد خرید'
        ));
    }

    public function render_recommendation_form() {
        ob_start();
        ?>
        <div class="perfume-recommendation-form">
            <h2>پیشنهاد عطر</h2>
            <form id="perfume-recommendation-form">
                <div class="form-group">
                    <label for="temperature">طبع عطر</label>
                    <select name="temperature" id="temperature" required>
                        <option value="warm">گرم</option>
                        <option value="moderate">معتدل</option>
                        <option value="cool">خنک</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="age_range">سن</label>
                    <select name="age_range" id="age_range" required>
                        <option value="15-25">15 تا 25 سال</option>
                        <option value="25-40">25 تا 40 سال</option>
                        <option value="40+">40 سال به بالا</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="smoker_friendly">مناسب افراد سیگاری</label>
                    <select name="smoker_friendly" id="smoker_friendly" required>
                        <option value="yes">بله</option>
                        <option value="no">خیر</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="skin_tone">رنگ پوست</label>
                    <select name="skin_tone" id="skin_tone" required>
                        <option value="light">روشن</option>
                        <option value="medium">گندمی</option>
                        <option value="dark">تیره</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="personality">خصوصیات اخلاقی</label>
                    <select name="personality" id="personality" required>
                        <option value="calm">آرام</option>
                        <option value="temperamental">تند مزاج و زود رنج</option>
                        <option value="balanced">متعادل و منطقی</option>
                    </select>
                </div>

                <button type="submit" class="button">دریافت پیشنهادات</button>
            </form>

            <div id="recommendation-results" style="display: none;">
                <h3>عطرهای پیشنهادی</h3>
                <div class="recommendation-list"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
} 