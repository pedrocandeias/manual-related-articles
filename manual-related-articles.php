<?php
/*
Plugin Name: Manual Related Articles
Description: Allows users to manually select related posts.
Version: 1.0
Author: Pedro Candeias
Text Domain: manual-related-articles
Domain Path: /languages
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin directory
define('MRA_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Load plugin text domain for translations
function mra_load_textdomain()
{
    load_plugin_textdomain('manual-related-articles', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'mra_load_textdomain');

// Include necessary files
require_once MRA_PLUGIN_DIR . 'includes/mra-admin.php';
require_once MRA_PLUGIN_DIR . 'includes/mra-related-posts-meta-box.php';
require_once MRA_PLUGIN_DIR . 'includes/mra-display.php';

// Enqueue admin styles and scripts
function mra_enqueue_admin_assets()
{
    if (is_admin()) {
        wp_enqueue_script('mra-admin-script', plugins_url('assets/js/mra-admin-script.js', __FILE__), array('jquery', 'jquery-ui-dialog'), null, true);
        wp_localize_script('mra-admin-script', 'mraAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('mra-admin-style', plugins_url('assets/css/mra-admin-style.css', __FILE__));
        wp_enqueue_style('wp-jquery-ui-dialog');
    }
}
add_action('admin_enqueue_scripts', 'mra_enqueue_admin_assets');
