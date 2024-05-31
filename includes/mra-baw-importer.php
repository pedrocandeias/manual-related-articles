<?php 
/**
 * Plugin Name: Manual Related Articles
 * Description: Import related posts from BAW Manual Related Posts
 * Version: 1.0
 * Author: PEC
 * Author URI: https://www.pedrocandeias.net  
 */


// Function to handle the import process in batches
function mra_import_baw_related_posts_batch() {
    global $wpdb;

    $batch_size = 100; // Number of posts to process at a time
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

    // Get a batch of posts
    $posts = get_posts(array(
        'numberposts' => $batch_size,
        'offset' => $offset,
        'post_type' => 'any',
        'post_status' => 'any'
    ));

    if (empty($posts)) {
        // Clean up after import
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_bawmrp_%'");
        $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_yyarpp'");

        wp_send_json_success(array('completed' => true));
    }

    foreach ($posts as $post) {
        $related_posts = mra_bawmrp_get_all_related_posts($post);
        $related_post_ids = wp_list_pluck($related_posts, 'ID');

        if (!empty($related_post_ids)) {
            update_post_meta($post->ID, '_mra_related_posts', implode(',', $related_post_ids));
        }
    }

    wp_send_json_success(array('offset' => $offset + $batch_size));
}
add_action('wp_ajax_mra_import_baw_related_posts_batch', 'mra_import_baw_related_posts_batch');


function mra_bawmrp_get_related_posts($post_id) {
    global $wpdb;
    $related_posts = $wpdb->get_col($wpdb->prepare("
        SELECT meta_value 
        FROM $wpdb->postmeta 
        WHERE post_id = %d 
        AND meta_key = '_yyarpp'
    ", $post_id));
    
    return array_map('intval', $related_posts);
}

function mra_bawmrp_get_all_related_posts($post) {
    $related_post_ids = mra_bawmrp_get_related_posts($post->ID);
    if (!empty($related_post_ids)) {
        $related_posts = get_posts(array(
            'include' => $related_post_ids,
            'post_type' => 'any',
            'post_status' => 'any'
        ));
        return $related_posts;
    }
    return array();
}
