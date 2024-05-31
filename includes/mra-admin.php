<?php
// Add admin menu item
function mra_add_admin_menu() {
    add_management_page(
        __('Manual Related Articles', 'manual-related-articles'),  // Page title
        __('Manual Related Articles', 'manual-related-articles'),  // Menu title
        'manage_options',           // Capability
        'manual-related-articles',  // Menu slug
        'mra_admin_page'            // Callback function
    );
}
add_action('admin_menu', 'mra_add_admin_menu');

// Admin page callback function
function mra_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Manual Related Articles', 'manual-related-articles'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mra_settings_group');
            do_settings_sections('manual-related-articles');
            submit_button();
            ?>
        </form>
        <h2><?php _e('Template Customization', 'manual-related-articles'); ?></h2>
        <p><?php _e('To customize the related posts template, copy the file <code>includes/related-posts/mra-related-posts-template.php</code> from the plugin directory to <code>includes/related-posts/mra-related-posts-template.php</code> in your theme directory and modify it as needed.', 'manual-related-articles'); ?></p>
        <h2><?php _e('Displaying Related Articles', 'manual-related-articles'); ?></h2>
        <p><?php _e('You can display related articles using the following methods:', 'manual-related-articles'); ?></p>
        <h3><?php _e('1. Using the Function', 'manual-related-articles'); ?></h3>
        <p><?php _e('You can call the <code>mra_display_related_posts</code> function directly in your theme files to display related articles. For example, add the following code to your <code>single.php</code> file:', 'manual-related-articles'); ?></p>
        <pre><code>
if (function_exists('mra_display_related_posts')) {
    mra_display_related_posts(get_the_ID());
}
        </code></pre>
        <h3><?php _e('2. Using the Shortcode', 'manual-related-articles'); ?></h3>
        <p><?php _e('You can use the <code>[mra_related_posts]</code> shortcode to display related articles in your posts or pages. For example, add the following code to your <code>single.php</code> file:', 'manual-related-articles'); ?></p>
        <pre><code>
if (shortcode_exists('mra_related_posts')) {
    echo do_shortcode('[mra_related_posts post_id="' . get_the_ID() . '"]');
}
        </code></pre>
        <h2><?php _e('Import Related Posts from BAW Plugin', 'manual-related-articles'); ?></h2>
        <button id="mra_start_import" class="button button-primary"><?php _e('Start Import', 'manual-related-articles'); ?></button>
        <div id="mra_import_status"></div>
        <h2><?php _e('Reset Manual Related Posts', 'manual-related-articles'); ?></h2>
        <p><?php _e('This will reset all manual related posts. This action cannot be undone.', 'manual-related-articles'); ?></p>
        <p><strong><?php _e('Are you sure you want to reset all manual related posts?', 'manual-related-articles'); ?></strong></p>
        <button id="mra_reset_related_posts" class="button button-secondary"><?php _e('Reset Related Posts', 'manual-related-articles'); ?></button>
        <div id="mra_reset_status"></div>
    </div>
    <?php
}

// Include BAW plugin functions
include_once 'mra-baw-importer.php'; // Adjust path as needed

// Function to reset all manual related posts
function mra_reset_related_posts() {
    global $wpdb;
    
    // Add logging to debug
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('mra_reset_related_posts called');
    }

    $result = $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_mra_related_posts'");
    
    if ($result !== false) {
        wp_send_json_success();
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Failed to delete related posts: ' . $wpdb->last_error);
        }
        wp_send_json_error();
    }
}
add_action('wp_ajax_mra_reset_related_posts', 'mra_reset_related_posts');


// Register settings
function mra_register_settings() {
    register_setting('mra_settings_group', 'mra_post_types', array(
        'type' => 'array',
        'sanitize_callback' => 'mra_sanitize_post_types',
        'default' => array(),
    ));
    register_setting('mra_settings_group', 'mra_max_related_posts', array(
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'default' => 5,
    ));
    register_setting('mra_settings_group', 'mra_use_own_template', array(
        'type' => 'boolean',
        'sanitize_callback' => 'mra_sanitize_checkbox',
        'default' => false,
    ));

    add_settings_section('mra_main_section', __('Settings', 'manual-related-articles'), 'mra_main_section_callback', 'manual-related-articles');

    add_settings_field(
        'mra_post_types',
        __('Post Types', 'manual-related-articles'),
        'mra_post_types_callback',
        'manual-related-articles',
        'mra_main_section'
    );

    add_settings_field(
        'mra_max_related_posts',
        __('Maximum Related Posts', 'manual-related-articles'),
        'mra_max_related_posts_callback',
        'manual-related-articles',
        'mra_main_section'
    );

    add_settings_field(
        'mra_use_own_template',
        __('Use Template from Theme Folder', 'manual-related-articles'),
        'mra_use_own_template_callback',
        'manual-related-articles',
        'mra_main_section'
    );
}
add_action('admin_init', 'mra_register_settings');

function mra_main_section_callback() {
    echo '<p>' . __('Main settings for the Manual Related Articles plugin.', 'manual-related-articles') . '</p>';
}

function mra_post_types_callback() {
    $post_types = get_post_types(array('public' => true), 'names');
    $selected_post_types = get_option('mra_post_types', array());

    // Ensure $selected_post_types is always an array
    if (!is_array($selected_post_types)) {
        $selected_post_types = array();
    }

    foreach ($post_types as $post_type) {
        $checked = in_array($post_type, $selected_post_types) ? 'checked' : '';
        echo '<label><input type="checkbox" name="mra_post_types[]" value="' . esc_attr($post_type) . '" ' . $checked . '> ' . esc_html($post_type) . '</label><br>';
    }
}

function mra_max_related_posts_callback() {
    $max_related_posts = get_option('mra_max_related_posts', 5);
    echo '<input type="number" name="mra_max_related_posts" value="' . esc_attr($max_related_posts) . '" min="1" />';
}

function mra_use_own_template_callback() {
    $use_own_template = get_option('mra_use_own_template', false);
    $checked = $use_own_template ? 'checked' : '';
    echo '<label><input type="checkbox" name="mra_use_own_template" value="1" ' . $checked . '> ' . __('Enable', 'manual-related-articles') . '</label>';
}

function mra_sanitize_post_types($input) {
    return is_array($input) ? array_map('sanitize_text_field', $input) : array();
}

function mra_sanitize_checkbox($input) {
    return $input ? 1 : 0;
}
