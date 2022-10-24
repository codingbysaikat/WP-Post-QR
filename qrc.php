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
 * Text Domain:       qrcg
 * Domain Path:       /languages
 */

// function qrcg_activation_to_run(){
// }

// register_activation_hook( __FILE__, 'qrcg_activation_to_run' );

// function qrcg_deactivation_to_run(){  
// }
// register_deactivation_hook( __FILE__, 'qrcg_deactivation_to_run' );

$qrcg_container = array(
    __('Afganistan','qrcg'),
    __('bangladesh','qrcg'),
    __('India','pqrc','qrcg'),
    __('pakistan','pqrc','qrcg'),
    __('Napel','pqrc','qrcg'),
    __('Sri Lanka','qrcg'),
    __('Bhutan','qrcg'),
    __('Maldives','qrcg'),

);
function qrcg_loaded_textdomin(){
    load_plugin_textdomain( 'qrcg', false, dirname(__FILE__ . 'languages') );
}
add_action( 'plugin_loaded', 'qrcg_loaded_textdomin' );

function qrcg_countrys_init(){
    global $qrcg_container;
    $qrcg_container = apply_filters('qrcg_allcountrys',$qrcg_container);

}
add_action('init','qrcg_countrys_init');

function qrcg_admin_scripts($screen){
    
    if('options-general.php' == $screen){  
        wp_register_script( 'jquery2', 'https://code.jquery.com/jquery-3.2.1.slim.min.js',null,'3.2.1',true );
        wp_enqueue_style('qrcg-mini-toggle-style',plugin_dir_url(__FILE__).'/assets/css/minitoggle.css');
        wp_enqueue_script('qrcg-main-js',plugin_dir_url(__FILE__).'/assets/js/minitoggle.js', array('jquery2'), '1.0', true);
        wp_enqueue_script('qrcg-mini-toggle-js',plugin_dir_url(__FILE__).'/assets/js/qrcg-main.js', array('jquery2'), time(), true);

    }

}
add_action('admin_enqueue_scripts','qrcg_admin_scripts');
function qrcg_generator($content){
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_type = get_post_type($current_post_id);
    $excluded_post_type = apply_filters('qrcg_post-type', array(),'post');
    $current_permalink = urlencode(get_permalink($current_post_id));
    $height = get_option('qrcg_height');
    $height = $height ? $height : '250';
    $width = get_option('qrcg_width');
    $width = $width ? $width : '250';

    $dimension = apply_filters('qrcg_code_height', "{$width}x{$height}");
    $link = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s', $dimension, $current_permalink);
    if (!is_page()) {
        if (in_array($current_post_type, $excluded_post_type)) {
            $content .= sprintf("<div><img src='%s' alt='%s'></div>", $link, $current_post_title);
        }
    }
    return $content;
}
add_filter('the_content', 'qrcg_generator');

function qrcg_setting_init(){
    add_settings_section('qrcg_section', __('WP Post QR Code Generator setings', 'qrcg'),'qrcg_section_callback','general');
    add_settings_field('qrcg_height', __('QR code Height', 'qrcg'), 'qrcg_height_set', 'general','qrcg_section');
    add_settings_field('qrcg_width', __('QR Code width', 'qrcg'), 'qrcg_width_set', 'general','qrcg_section');
    add_settings_field('qrcg_select',__('Select a Country','qrcg'),'qrcg_select_flied','general','qrcg_section');
    add_settings_field('qrcg_checkbox',__('Please Check','qrcg'),'qrcg_checkbox_flied','general','qrcg_section');
    add_settings_field('qrcg_minitoggle',__('Please Check Mark','qrcg'),'qrcg_minitoggle_flied','general','qrcg_section');


    register_setting('general', 'qrcg_height', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'qrcg_width', array('sanitize_callback' => 'esc_attr'));
    register_setting('general','qrcg_select',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','qrcg_checkbox');
    register_setting('general','qrcg_minitoggle');
};
add_action('admin_init', 'qrcg_setting_init');
 
function qrcg_section_callback(){
    echo "<p>".__('Settings for Posts To QR Plugin','qrcg')."</p>";
};

function qrcg_height_set(){
    $height = get_option('qrcg_height');

    printf("<input type='text' id='%s' name = '%s' value ='%s'>", 'qrcg_height', 'qrcg_height', $height);
};
function qrcg_width_set(){
    $width = get_option('qrcg_width');
    printf("<input type='text' id='%s' name='%s' value='%s'>", 'qrcg_width', 'qrcg_width', $width);
};
function qrcg_select_flied(){
    $option = get_option('qrcg_select');
    printf("<select id='%s' name='%s' value='%s'>",'qrcg_select','qrcg_select',$option);
    global $qrcg_container;
    foreach($qrcg_container as $item){
        $select = '';
        if($option == $item){
            $select = 'selected';
        }
        printf("<option value='%s' %s > %s </option>", $item,$select,$item);

    }

    echo '</select>';

}
function qrcg_checkbox_flied(){
    global $qrcg_container;
    foreach($qrcg_container as $country){
            $select = '';
            $option = get_option('qrcg_checkbox');
            if(is_array($option) && in_array($country, $option)){
                $select = 'checked';

            }
            
            printf( '<input type="checkbox" name="qrcg_checkbox[]" value="%s" %s /> %s <br/>', $country, $select, $country );
       

    }
    
}

function qrcg_minitoggle_flied(){
    $option = get_option('qrcg_minitoggle');
    echo '<div class="toggle"></div>';
    echo "<input type='hidden' name='qrcg_minitoggle' id='qrcg-toggle' value ='".$option."'/>";
}
