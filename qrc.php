<?php

/**
 * Plugin Name:       WP Post QR Code Generator
 * Plugin URI:        https://github.com/codingbysaikat/WordCounter.git
 * Description:       WordPress post counter plugin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            saikat mondal
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       qcr
 * Domain Path:       /languages
 */

// function qcr_activation_to_run(){
// }

// register_activation_hook( __FILE__, 'qcr_activation_to_run' );

// function qcr_deactivation_to_run(){  
// }
// register_deactivation_hook( __FILE__, 'qcr_deactivation_to_run' );


function qcr_loaded_textdomin()
{
    load_plugin_textdomain( 'qcr', false, dirname(__FILE__ . 'languages') );
}
add_action( 'plugin_loaded', 'qcr_loaded_textdomin' );


function qrc_generator($content)
{
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_type = get_post_type($current_post_id);
    $excluded_post_type = apply_filters('qrc_post-type', array());
    $current_permalink = urlencode(get_permalink($current_post_id));
    $height = get_option('qrc_height');
    $height = $height ? $height : '250';
    $width = get_option('qrc_width');
    $width = $width ? $width : '250';

    $dimension = apply_filters('qrc_code_height', "{$width}x{$height}");
    $link = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s', $dimension, $current_permalink);
    if (!is_page()) {
        if (in_array($current_post_type, $excluded_post_type)) {
            $content .= sprintf("<div><img src='%s' alt='%s'></div>", $link, $current_post_title);
        }
    }
    return $content;
}
add_filter('the_content', 'qrc_generator');

function qrc_setting_init()
{
    add_settings_field('qrc_height', __('QR code Height', 'qrc'), 'qrc_height_set', 'general');
    add_settings_field('qrc_width', __('QR Code width', 'qrc'), 'qrc_width_set', 'general');

    register_setting('general', 'qrc_height', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'qrc_width', array('sanitize_callback' => 'esc_attr'));
}
function qrc_height_set()
{
    $height = get_option('qrc_height');

    printf("<input type='text' id='%s' name = '%s' value ='%s'>", 'qrc_height', 'qrc_height', $height);
}
function qrc_width_set()
{
    $width = get_option('qrc_width');
    printf("<input type='text' id='%s' name='%s' value='%s'>", 'qrc_width', 'qrc_width', $width);
}
add_action('admin_init', 'qrc_setting_init');
 