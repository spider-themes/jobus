<?php
if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
?>
<div class="category-section-five">
    <div class="card-wrapper-two d-flex flex-wrap jobus_cat_align">
        <?php 
        if ( ! empty( $categories ) && is_array( $categories ) ) :
            foreach( $categories as $cat ) :

                $meta_tax = get_term_meta( $cat->term_id, 'jobus_taxonomy_cat', true );
                $cat_link = get_term_link( $cat ); // More generic than get_category_link()

                // Ensure no errors in the term link
                if ( is_wp_error( $cat_link ) ) {
                    continue;
                }
                ?>

                <div class="card-style-seven bg-color text-center wow fadeInUp category-<?php echo esc_attr( $cat->term_id ); ?>">
                    <a href="<?php echo esc_url( $cat_link ); ?>" class="wrapper d-flex align-items-center">
                        <?php 
                        if ( ! empty( $meta_tax['cat_img']['url'] ) ) : 
                            ?>
                            <div class="icon d-flex align-items-center justify-content-center">
                                <img src="<?php echo esc_url($meta_tax['cat_img']['url']); ?>" alt="<?php echo isset($meta_tax['cat_img']['alt']) ? esc_attr($meta_tax['cat_img']['alt']) : ''; ?>" class="lazy-img">
                            </div>
                            <?php 
                        endif; 
                        ?>
                        <div class="title fw-500">
                            <?php echo esc_html( $cat->name ); ?>
                        </div>
                    </a>
                </div>
                <?php 
            endforeach;
        endif; 
        ?>
    </div>
</div>