<?php
/**
 * Shortcodes
 *
 * @package jobus
 * @author spiderdevs
 */

namespace jobus\includes\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Shortcode
 */
class Shortcode
{

    /**
     *
     */
    public static function init(): void
    {


        add_shortcode('jobus_user_dashboard', [__CLASS__, 'user_dashboard']);

    }


    /**
     * Employer Dashboard
     *
     * @param $atts
     * @return void
     */
    public static function user_dashboard($atts) {


    }

}