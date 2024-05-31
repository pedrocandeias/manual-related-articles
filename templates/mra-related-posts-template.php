<?php
if (isset($related_posts) && !empty($related_posts)) {
    ?>
    <div class="custom-mra-related-posts">
        <h3><?php _e('Related Posts', 'manual-related-articles'); ?></h3>
        <ul>
            <?php foreach ($related_posts as $related_post_id) {
                $related_post = get_post($related_post_id);
                if ($related_post) {
                    ?>
                    <li><a href="<?php echo get_permalink($related_post->ID); ?>"><?php echo get_the_title($related_post->ID); ?></a></li>
                    <?php
                }
            } ?>
        </ul>
    </div>
    <?php
}
?>
