<?php
// Add meta box
function mra_add_meta_box()
{
    $post_types = get_option('mra_post_types', array());
    foreach ($post_types as $post_type) {
        add_meta_box(
            'mra_related_posts',         // ID
            __('Artigos relacionados', 'manual-related-articles'), // Title
            'mra_meta_box_callback',     // Callback
            $post_type,                  // Post type
            'side',                      // Context
            'default'                    // Priority
        );
    }
}
add_action('add_meta_boxes', 'mra_add_meta_box');

function mra_meta_box_callback($post)
{
    wp_nonce_field('mra_meta_box', 'mra_meta_box_nonce');

    $related_posts = get_post_meta($post->ID, '_mra_related_posts', true);
    $related_posts = $related_posts ? explode(',', $related_posts) : array();
    $related_posts = array_filter($related_posts); // Remove empty values
    ?>
    <button type="button" id="mra_open_modal" class="button button-secondary button-large"><?php _e('Adicionar artigo relacionado', 'manual-related-articles'); ?></button>
    <ul id="mra_related_posts_list">
        <?php
        foreach ($related_posts as $related_post_id) {
            $related_post = get_post($related_post_id);
            if ($related_post) {
                echo '<li data-post-id="' . esc_attr($related_post->ID) . '"><a href="#" class="mra-remove-post"><span aria-hidden="true">x</span></a><a class="mra-remove-post-item" href="' . get_permalink($related_post->ID) . '" target="_blank">' . esc_html($related_post->post_title) . '</a></li>';
            }
        }
        ?>
    </ul>
    <input  type="hidden" id="mra_related_posts" name="mra_related_posts" value="<?php echo esc_attr(implode(',', $related_posts)); ?>" />
    <button type="button" id="mra_clear_list" class="button delete"><?php _e('Limpar lista', 'manual-related-articles'); ?></button>

    <div id="mra_modal" style="display:none;" title="<?php _e('Encontrar artigos relacionados', 'manual-related-articles'); ?>">
        <input type="text" id="mra_search" placeholder="<?php _e('Pesquisar...', 'manual-related-articles'); ?>" />
        <button type="button" id="mra_search_button" class="button button-primary"><?php _e('Pesquisar', 'manual-related-articles'); ?></button>
        <ul id="mra_search_results"></ul>
    </div>
    <?php
}

function mra_save_meta_box_data($post_id)
{
    if (!isset($_POST['mra_meta_box_nonce']) || !wp_verify_nonce($_POST['mra_meta_box_nonce'], 'mra_meta_box')) {
        return;
    }

    error_log('mra_save_meta_box_data called');
    error_log('Post related posts: ' . print_r($_POST['mra_related_posts'], true));

    if (isset($_POST['mra_related_posts']) && !empty($_POST['mra_related_posts'])) {
        $related_posts = array_filter(array_map('sanitize_text_field', explode(',', $_POST['mra_related_posts'])));
        error_log('Saving related posts: ' . implode(',', $related_posts));
        update_post_meta($post_id, '_mra_related_posts', implode(',', $related_posts));
    } else {
        delete_post_meta($post_id, '_mra_related_posts');
    }
}
add_action('save_post', 'mra_save_meta_box_data');

function mra_search_posts()
{
    $search = sanitize_text_field($_GET['search']);
    $args = array(
        's' => $search,
        'post_status' => 'publish',
        'post_type' => get_post_types(array('public' => true)),
        'posts_per_page' => 10
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<li><label><input type="checkbox" class="mra-add-post" data-post-id="' . get_the_ID() . '" data-post-title="' . get_the_title() . '"> ' . get_the_title() . '</label></li>';
        }
    } else {
        echo '<li>' . __('Nenhum artigo encontrado.', 'manual-related-articles') . '</li>';
    }
    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_mra_search_posts', 'mra_search_posts');
