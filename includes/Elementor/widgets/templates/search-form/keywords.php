<?php
if ( !empty($settings['keyword_label']) ) {
    echo '<li class="fw-500 text-white me-2">' . esc_html($settings[ 'keyword_label' ]) . '</li>';
}
if ( is_array( $settings['keywords'] ) ) {
    foreach ( $settings['keywords'] as $item ) {
        ?>
        <li><a href="#"> <?php echo esc_html($item['title']); ?> </a></li>
        <?php
    }
}
?>