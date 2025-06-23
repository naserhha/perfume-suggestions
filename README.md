# Perfume Recommendation System for WooCommerce

A WordPress plugin that adds a perfume recommendation system to your WooCommerce store. The system recommends perfumes based on user characteristics such as temperature preference, age range, smoker status, skin tone, and personality.

## Features

- User-friendly form to collect user preferences
- Smart recommendation system based on multiple characteristics
- Integration with WooCommerce products
- Responsive design
- Easy to use shortcode
- Admin interface for managing perfume characteristics
- Multi-language support
- Customizable styling

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Installation

1. Download the plugin files
2. Upload the plugin folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Make sure WooCommerce is installed and activated
5. Go to Products > Perfume Recommendations to access the plugin settings

## Usage

### Setting Up Perfumes

1. Go to Products > Add New in your WordPress admin
2. Create a new product as you normally would
3. Fill in the "Perfume Characteristics" meta box with the appropriate values:
   - Temperature (Warm, Moderate, Cool)
   - Age Range (15-25, 25-40, 40+)
   - Smoker Friendly (Yes/No)
   - Skin Tone (Light, Medium, Dark)
   - Personality (Calm, Temperamental, Balanced and Logical)
4. Publish the product

### Adding the Recommendation Form

Add the following shortcode to any page or post where you want the recommendation form to appear:

```
[perfume_recommendation_form]
```

### How It Works

1. Users fill out the recommendation form with their preferences
2. The system matches their preferences with perfumes in your store
3. Up to 6 matching perfumes are displayed
4. Users can view details and add perfumes to their cart

## Customization

### CSS Customization

The plugin includes CSS styles that can be overridden in your theme's stylesheet. The main classes are:

- `.perfume-recommendation-form`
- `.recommendations-grid`
- `.perfume-card`

### Translation

The plugin is translation-ready. You can translate it using:
1. Poedit or similar translation tools
2. WordPress translation files
3. Translation plugins like Loco Translate

## Database Structure

The plugin creates a custom table in your WordPress database:
- `wp_perfume_recommendation_perfumes`: Stores perfume characteristics and product relationships

## Security

- All form submissions are validated and sanitized
- Nonce verification for all admin actions
- Proper capability checks for admin functions
- SQL prepared statements for database queries

## Performance

- Efficient database queries
- Caching support
- Optimized asset loading
- Responsive image handling

## Support

For support, please:
1. Check the documentation
2. Create an issue in the plugin's repository
3. Contact the plugin author

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This plugin is licensed under the GPL v2 or later.

## Author

**Mohammad Nasser Haji Hashemabad**

- Website: [mohammadnasser.com](https://mohammadnasser.com/)
- LinkedIn: [nasserhaji](https://ir.linkedin.com/in/nasserhaji)
- GitHub: [nasserhaji](https://github.com/nasserhaji)

## Changelog

### 1.0.0
- Initial release
- Basic recommendation system
- WooCommerce integration
- Admin interface
- Form shortcode
- Responsive design

## Credits

- Developed by Mohammad Nasser Haji Hashemabad
- Built with WordPress and WooCommerce
- Uses modern web technologies and best practices 