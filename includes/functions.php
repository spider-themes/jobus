<?php
/**
 * Translation ready
*/
add_action('wp_head', function(){
    esc_html__('Translation ready', 'jobly');
});