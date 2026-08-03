<?php
/**
 * Email Template Wrapper
 * 
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( get_bloginfo( 'name' ) ); ?> Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f6f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f6f6f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <?php if ( ! empty( $logo_url ) ) : ?>
                    <tr>
                        <td align="center" style="padding: 30px 40px; border-bottom: 1px solid #eeeeee;">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="max-height: 50px; display: block;">
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Top Color Bar Accent -->
                    <tr>
                        <td height="4" style="background-color: <?php echo esc_attr( $primary_color ); ?>; line-height: 4px; font-size: 4px;">&nbsp;</td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px; color: #333333; font-size: 16px; line-height: 1.6;">
                            <?php echo wp_kses_post( $message ); ?>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <?php if ( ! empty( $footer_text ) ) : ?>
                    <tr>
                        <td align="center" style="padding: 30px 40px; background-color: #fbfbfb; border-top: 1px solid #eeeeee; color: #888888; font-size: 14px;">
                            <?php echo wp_kses_post( $footer_text ); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="600">
                    <tr>
                        <td align="center" style="padding: 20px 0; color: #999999; font-size: 12px;">
                            &copy; <?php echo date('Y'); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'jobus' ); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
