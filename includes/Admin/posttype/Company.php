<?php
namespace Jobly\Admin\posttype;

if (!defined('ABSPATH')) {
    exit;// Exit if accessed directly
}

class Company
{

    private static $instance = null;

    public function __construct ()
    {

        // Register the posttype
        add_action('init', [ $this, 'register_post_types' ]);

    }

    public static function init ()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}