<?php
// Function to get related posts
function mra_get_related_posts($post_id) {
    $related_posts = get_post_meta($post_id, '_mra_related_posts', true);
    if ($related_posts) {
        $related_posts = array_filter(array_map('intval', explode(',', $related_posts)));
        return $related_posts;
    }
    return array();
}

// Function to display related posts
function mra_display_related_posts($post_id) {
    $related_posts = mra_get_related_posts($post_id);
    if ($related_posts) {
        $use_own_template = get_option('mra_use_own_template', false);
        $template_path = locate_template('includes/related-posts/mra-related-posts-template.php');

        if ($use_own_template && $template_path) {
            // Use custom template from theme subdirectory
            include $template_path;
        } elseif ($use_own_template) {
            // Show warning message in the admin area only
            if (is_admin()) {
                echo '<div class="notice notice-warning">';
                _e('The custom related posts template was not found in your theme directory. Please ensure it is located at <code>your-theme/includes/related-posts/mra-related-posts-template.php</code>.', 'manual-related-articles');
                echo '</div>';
            }
            // Use default template from plugin if custom template not found
            $plugin_template_path = plugin_dir_path(__FILE__) . 'related-posts/mra-related-posts-template.php';
            if (file_exists($plugin_template_path)) {
                include $plugin_template_path;
            } else {
                // Gracefully handle missing default template
                echo '<p>' . __('The related posts template is missing.', 'manual-related-articles') . '</p>';
            }
        } else {
            // Use default template from plugin
            $plugin_template_path = plugin_dir_path(__FILE__) . 'related-posts/mra-related-posts-template.php';
            if (file_exists($plugin_template_path)) {
                include $plugin_template_path;
            } else {
                // Gracefully handle missing default template
                echo '<p>' . __('The related posts template is missing.', 'manual-related-articles') . '</p>';
            }
        }
    }
}

// Shortcode to display related posts
function mra_related_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
    ), $atts, 'mra_related_posts');

    ob_start();
    mra_display_related_posts($atts['post_id']);
    return ob_get_clean();
}
add_shortcode('mra_related_posts', 'mra_related_posts_shortcode');
?>
