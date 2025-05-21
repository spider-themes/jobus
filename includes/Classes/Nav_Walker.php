<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Nav_Walker extends \Walker_Nav_Menu {

	// Start of the <ul> element
	public function start_lvl( &$output, $depth = 0, $args = array() ): void {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class=\"sub-menu\">\n";
	}

	// End of the <ul> element
	public function end_lvl( &$output, $depth = 0, $args = array() ): void {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}


	// Start of the <li> element
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ): void {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		// Set classes for each <li>
		$classes   = empty( $data_object->classes ) ? array() : ( array ) $data_object->classes;
		$classes[] = 'menu-item-' . $data_object->ID;

		// Join classes and apply them to the <li>
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $data_object, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Create the <li> opening tag
		$output .= $indent . '<li' . $class_names . '>';

		// Build the <a> tag and its attributes
		$attributes = ! empty( $data_object->attr_title ) ? ' title="' . esc_attr( $data_object->attr_title ) . '"' : '';
		$attributes .= ! empty( $data_object->target ) ? ' target="' . esc_attr( $data_object->target ) . '"' : '';
		$attributes .= ! empty( $data_object->xfn ) ? ' rel="' . esc_attr( $data_object->xfn ) . '"' : '';
		$attributes .= ! empty( $data_object->url ) ? ' href="' . esc_attr( $data_object->url ) . '"' : '';

		// Add 'active' class to <a> tag if current menu item
		$active_class = in_array( 'current-menu-item', $classes ) || in_array( 'current_page_item', $classes ) ? ' active' : '';
		$attributes   .= ' class="d-flex w-100 align-items-center' . $active_class . '"';

		// Fetch custom meta fields for this menu item
		$regular_img  = get_post_meta( $data_object->ID, 'jobus_menu_regular_image', true );
		$active_img   = get_post_meta( $data_object->ID, 'jobus_menu_active_image', true );

		// Determine if this menu item is active
		$is_active = in_array( 'current-menu-item', $classes ) || in_array( 'current_page_item', $classes );

		// The menu item title
		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';

		// Output the correct image (active or regular)
		$image_url = '';
		if ( $is_active && !empty($active_img) && !empty($active_img['url']) ) {
			$image_url = $active_img['url'];
		} elseif ( !empty($regular_img) && !empty($regular_img['url']) ) {
			$image_url = $regular_img['url'];
		}
		if ( $image_url ) {
			$item_output .= '<img src="'.esc_url($image_url).'" data-src="' . esc_url($image_url) . '" alt="" class="lazy-img">';
		}

		$item_output .= '<span>' . $args->link_before . apply_filters( 'the_title', $data_object->title, $data_object->ID ) . $args->link_after . '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;

		// Append the output
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $data_object, $depth, $args );
	}


	// End of the <li> element
	public function end_el( &$output, $data_object, $depth = 0, $args = array() ): void {
		$output .= "</li>\n";
	}

}

