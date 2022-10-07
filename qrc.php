<?php 
/**
 * Plugin Name:       WP Post QR Code Generator
 * Plugin URI:        https://github.com/codingbysaikat/WordCounter.git
 * Description:       WordPress post counter plugin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:           saikat mondal
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


function qcr_loaded_textdomin(){
    load_plugin_textdomain('qcr',false,dirname(__FILE__.'languages'));

}
add_action('plugin_loaded','qcr_loaded_textdomin');


function qrc_generator($content){
    $current_post_id = get_the_ID();
    $current_post_type = get_post_type($current_post_id);
    $current_post_title = get_the_title($current_post_id);
    $current_permalink = urlencode(get_permalink($current_post_id));
    $link = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s',$current_permalink);
    if(!is_page()){
        $content.= sprintf("<div><img src='%s' alt='%s'></div>",$link,$current_post_title);
    }
    
    return $content;


}
add_filter('the_content','qrc_generator');