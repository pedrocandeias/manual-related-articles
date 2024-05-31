# Manual Related Articles

Manual Related Articles is a WordPress plugin that allows you to manually select related posts for each post. This plugin provides an easy-to-use interface in the post editor and offers flexible options for displaying related posts on your site.

## Features

- Manually select related posts from the post editor screen.
- Display related posts using a function or a shortcode.
- Option to use a custom template from your theme folder.
- Import related posts from the BAW Manual Related Posts plugin.
- Reset all manually related posts.

## Installation

1. Download the plugin and upload it to your WordPress site's `wp-content/plugins` directory, or install it directly from the WordPress plugin repository.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

### Selecting Related Posts

1. Edit a post in the WordPress admin.
2. Use the "Related Posts" meta box to search and select related posts.
3. Save the post.

### Displaying Related Posts

You can display related posts using either a function or a shortcode.

#### Using the Function

Add the following code to your theme's template files (e.g., `single.php`):

```php
if (function_exists('mra_display_related_posts')) {
    mra_display_related_posts(get_the_ID());
}
```

## Using the Shortcode

Add the following shortcode to your post content or template files:

```php
echo do_shortcode('[mra_related_posts post_id="' . get_the_ID() . '"]');
```

## Custom Template

### To customize the related posts template:

    - Copy the file includes/related-posts/mra-related-posts-template.php from the plugin directory.
    - Paste it into includes/related-posts/mra-related-posts-template.php in your theme directory.
    - Modify the template as needed.

### Import Related Posts from BAW Plugin

    - Go to the "Manual Related Articles" settings page in the WordPress admin.
    - Click the "Start Import" button to import related posts from the BAW Manual Related Posts plugin.

## Reset Related Posts

    - Go to the "Manual Related Articles" settings page in the WordPress admin.
    - Click the "Reset Related Posts" button to reset all manually related posts.

## Development
### Local Development

To set up the plugin for local development:

    - Clone the repository: git clone https://github.com/pedrocandeias/manual-related-articles.git
    - Navigate to the plugin directory: cd manual-related-articles
    - Install dependencies: npm install (if you have any npm dependencies)
    - Start developing!

## Contributing

Contributions are welcome! Please submit pull requests for any features, bug fixes, or enhancements.
License

This plugin is licensed under the MIT License.

### Author

    - Pedro Candeias - https://pedrocandeias.net