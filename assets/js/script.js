jQuery(document).ready(function($) {
    const form = $('.perfume-recommendation-form');
    const resultsContainer = $('.perfume-recommendation-results');
    const loadingSpinner = $('.perfume-recommendation-loading');

    form.on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'get_perfume_recommendations',
            nonce: perfume_recommendation.nonce,
            temperature: $('#temperature').val(),
            age_range: $('#age_range').val(),
            smoker_friendly: $('#smoker_friendly').val(),
            skin_tone: $('#skin_tone').val(),
            personality: $('#personality').val()
        };

        loadingSpinner.show();
        resultsContainer.hide();

        $.post(perfume_recommendation.ajax_url, formData, function(response) {
            loadingSpinner.hide();
            
            if (response.success && response.data.length > 0) {
                let html = '<div class="perfume-recommendation-grid">';
                
                response.data.forEach(function(product) {
                    html += `
                        <div class="perfume-recommendation-item">
                            <div class="perfume-recommendation-image">
                                <img src="${product.image}" alt="${product.title}">
                            </div>
                            <div class="perfume-recommendation-content">
                                <h3>${product.title}</h3>
                                <div class="perfume-recommendation-price">${product.price}</div>
                                <div class="perfume-recommendation-description">${product.description}</div>
                                <div class="perfume-recommendation-actions">
                                    <a href="${product.link}" class="button">مشاهده جزئیات</a>
                                    <a href="${product.add_to_cart_url}" class="button">افزودن به سبد خرید</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                resultsContainer.html(html).show();
            } else {
                resultsContainer.html('<p class="perfume-recommendation-no-results">هیچ محصولی یافت نشد.</p>').show();
            }
        }).fail(function() {
            loadingSpinner.hide();
            resultsContainer.html('<p class="perfume-recommendation-error">خطا در دریافت نتایج. لطفا دوباره تلاش کنید.</p>').show();
        });
    });
}); 