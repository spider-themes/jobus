<?php
/**
 * Sort By - Show sort by options for job listings
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/loop/sortby.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Build the URL for list and grid views
$list_view_url = esc_url( add_query_arg( 'view', 'list', $archive_url ) );
$grid_view_url = esc_url( add_query_arg( 'view', 'grid', $archive_url ) );

?>
<div class="d-flex align-items-center">
    <div class="short-filter d-flex align-items-center">
		<?php
		$order    = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$order_by = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		$default  = empty( $order_by ) ? 'selected' : '';

		$selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
		$selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
		$selected_title_asc  = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
		$selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
		?>
        <div class="text-dark fw-500 me-2"><?php esc_html_e( 'Sort By:', 'jobus' ); ?></div>
        <form action="" method="get">

			<?php wp_nonce_field( 'jobus_sort_filter', 'jobus_nonce' ); ?>

            <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                <option <?php echo esc_attr( $default ); ?>>
					<?php esc_html_e( 'Default', 'jobus' ); ?>
                </option>
                <option value="orderby=date&order=desc" <?php echo esc_attr( $selected_new_to_old ) ?>>
					<?php esc_html_e( 'Newest to Oldest', 'jobus' ); ?>
                </option>
                <option value="orderby=date&order=asc" <?php echo esc_attr( $selected_old_to_new ) ?>>
					<?php esc_html_e( 'Oldest to Newest', 'jobus' ); ?>
                </option>
                <option value="orderby=title&order=asc" <?php echo esc_attr( $selected_title_asc ) ?>>
					<?php esc_html_e( 'Title Ascending ', 'jobus' ); ?>
                </option>
                <option value="orderby=title&order=desc" <?php echo esc_attr( $selected_title_desc ) ?>>
					<?php esc_html_e( 'Title Descending', 'jobus' ); ?>
                </option>
            </select>
        </form>
    </div>

	<?php
	if ( is_post_type_archive( $post_type ) ) {
        ?>
        <a href="<?php echo esc_url( $list_view_url ); ?>"
           class="style-changer-btn rounded-circle tran3s ms-2 list-btn <?php echo esc_attr( $current_view === 'grid' ) ? ' active' : ''; ?>"
           title="<?php esc_attr_e( 'Active List', 'jobus' ); ?>">
            <i class="bi bi-list"></i>
        </a>
        <a href="<?php echo esc_url( $grid_view_url ); ?>"
           class="style-changer-btn rounded-circle tran3s ms-2 grid-btn <?php echo esc_attr( $current_view === 'list' ) ? ' active' : ''; ?>"
           title="<?php esc_attr_e( 'Active Grid', 'jobus' ); ?>">
            <i class="bi bi-grid"></i>
        </a>
        <?php
	}
	?>

</div>