<?php
/*
 * Plugin Name: Security Plugin
 * Author: Manan Azam
 * Description: This plugin prevents from editing and adding new themes and plugins
 * Version: 1.0
*/ 

if (!defined('ABSPATH')){
    die('U can not accses this file directly');
}


register_activation_hook(__FILE__ , 'isecure_on_activation');
register_deactivation_hook(__FILE__ , 'isecure_on_deactivation');
register_uninstall_hook(__FILE__ , 'isecure_on_uninstall');

function isecure_on_activation(){
    add_option('add_Title' , "Isecure");
}
function isecure_on_deactivation(){
    delete_option('add_Title');
}



add_action('admin_enqueue_scripts', 'isecure_enqueue_admin_scripts');

function isecure_enqueue_admin_scripts() {
    wp_enqueue_style('isecure-admin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('isecure-admin-script', plugin_dir_url(__FILE__) . 'assets/js/custom.js', array('jquery'), '1.0', true);
    wp_enqueue_script( 'jquery' );
}




   add_action('admin_menu', 'my_admin_menu');
   
   
   function my_admin_menu() {
       // Add a top-level menu page
       add_menu_page(
           'Isecure', 'Isecure', 'manage_options', 'isecure-admin', 'isecure_admin_page', 'dashicons-tickets',
           6
       );
   
       add_submenu_page(
           'isecure-admin', 'Isecure Settings', 'Settings', 'manage_options', 'isecure_settings', 'isecure_settings_func'
       );

       add_submenu_page(
        'isecure-admin', 'isecure limit login attempts', 'Limit login attempts', 'manage_options', 'isecure_limit_login_attempts', 'limit_login_attempts_func'
       );
   }
   
   register_activation_hook(__FILE__, function () {
    if (false === get_option('Disable_File_Edit')) {
        add_option('Disable_File_Edit', '');
    }

    if(false === get_option('Enable_login_attempts')){
        add_option('Enable_login_attempts' , '1');
    }

    if(false === get_option('failed_login_limit')) {
        add_option('failed_login_limit' , '');
    }

    if(false === get_option('lockout_duration')) {
        add_option('lockout_duration' , '');
    }

    if(false === get_option('disallow_deactivation_current_plugin')) {
        add_option('disallow_deactivation_current_plugin' , '');
    }

    if(false === get_option('hide_current_plugin')) {
        add_option('hide_current_plugin' , '');
    }

    if(false === get_option('block_extra_mime')) {
        add_option('block_extra_mime' , '');
    }
    if(false === get_option('isecure_wp_url')) {
        add_option('isecure_wp_url' , 'login');
    }

    if(false === get_option('enable_login_url')) {
        add_option('enable_login_url' , '1');
    }
   });
   
   register_deactivation_hook(__FILE__, function () {

        delete_option("Disable_File_Edit");
        delete_option("failed_login_limit");
        delete_option("lockout_duration");
        delete_option('Enable_login_attempts');
        delete_option('disallow_deactivation_current_plugin');
        delete_option('hide_current_plugin');
        delete_option('block_extra_mime');
        delete_option('isecure_wp_url');
        delete_option('enable_login_url');
   });
 
   
  include "file_mode.php";
  include "login_attempts.php";
 
   
   function isecure_admin_page() {
       echo '<div class="wrap">';
       echo '<h2>Security Settings</h2>';
       echo '<p>This is where you can configure the settings for your security plugin.</p>';
       echo '</div>';
       return true;
   }
   



 