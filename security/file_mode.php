<?php
add_action('admin_menu', 'isecure_process_form' , 4);

function isecure_settings_func() {
       ?>
       <h2>Isecure Settings</h2>
       <?php //settings_error(); ?>
       <form action="options.php" method="POST" id="file_mod">
           <?php
             settings_fields('isecure_option_group');
  
             $disable_file_edit = get_option('Disable_File_Edit');
             $disallow_activation = get_option('disallow_deactivation_current_plugin');
             $hide_plugin = get_option('hide_current_plugin');
             $block_mime = get_option('block_extra_mime');
             $redirect_url = get_option('isecure_wp_url');
             $enable_redirect_url = intval(get_option('enable_login_url'));
           ?>
   
           <div class="form-check form-switch">
               <input class="form-check-input" name="Disable_File_Edit" type="checkbox" role="switch" id="flexSwitchCheckDefault" value="1" <?php checked(1, $disable_file_edit); ?>>
               <label class="form-check-label" for="flexSwitchCheckDefault">Disable File Edit mode and prevent uploading new themes and plugins.</label>
           </div>
           <div class="form-check form-deactivarion">
               <input type="checkbox" name="disallow_deactivation" id="disallow_deactivation" value="1" <?php checked(1, $disallow_activation); ?>>
               <label class="form-check-label" for="disallow_deactivation">Disallow deactivation of current plugin</label>
           </div>
           <div class="form-check form-hide_plugin">
               <input type="checkbox" name="hide_plugin" id="hide_plugin" value="1" <?php checked(1, $hide_plugin); ?>>
               <label class="form-check-label" for="hide_plugin">Hide plugin from plugin directory</label>
           </div>
           <div class="form-check block_extra_mime">
               <input type="checkbox" name="block_mime" id="block_mime" value="1" <?php checked(1, $block_mime); ?>>
               <label class="form-check-label" for="block_mime">Block extra mime type</label>
           </div>
          

          <div class="toggle-btn">
          <div id="testing"><p> Change the login url:</p></div>

        

          <label class="switch">
            <input type="checkbox" role="switch" name="change-login-url" id="change-login-url" class="change-login-url" value="1" <?php if ($enable_redirect_url === 1) echo 'checked="checked"'; ?> <?php checked(1, $enable_redirect_url)?>>
            <span class="slider round"></span>
          </label>
        </div>
           <div class="form-check" id="redirect_url">
            <div class="redirect_url_inner">
            <label for="isecure_redirect">Login url</label> 
            <div class="home-url">
                <code><?php echo home_url('/'); ?></code>
            <input type="text" id="isecure_redirect" name="isecure_redirect" value="<?php echo $redirect_url; ?>"></div>
            </div>
           </div>
   
           <?php submit_button('Save Changes'); ?>
           <?php wp_nonce_field('isecure_settings_form', 'isecure_settings_nonce'); ?>
       </form>
   <?php
   }

   function isecure_process_form() {
    
    if (isset($_POST['isecure_settings_nonce']) && wp_verify_nonce($_POST['isecure_settings_nonce'], 'isecure_settings_form')) {
        register_setting('isecure_option_group', 'isecure_option_name');
        if (isset($_POST['action']) && current_user_can('manage_options')) {
            update_option('Disable_File_Edit', sanitize_text_field($_POST['Disable_File_Edit']));
            update_option('disallow_deactivation_current_plugin' , sanitize_text_field($_POST['disallow_deactivation']));
            update_option('hide_current_plugin' , sanitize_text_field($_POST['hide_plugin']));
            update_option('block_extra_mime' , sanitize_text_field($_POST['block_mime']));
            update_option('isecure_wp_url' , sanitize_text_field($_POST['isecure_redirect']));
            update_option('enable_login_url' , sanitize_text_field($_POST['change-login-url']));
        }
    }
}

$disable_file_edit = intval(get_option('Disable_File_Edit'));

if($disable_file_edit === 1) {
    disable_file_edit();
}

function disable_file_edit() {

        if (!defined('DISALLOW_FILE_MODS')) {
            define('DISALLOW_FILE_MODS', true);
        }

    // if ( !defined('DISALLOW_FILE_EDIT')  ) {
    //         define('DISALLOW_FILE_EDIT', true);
    //     } 

  
}

// disbale deactivation of that plugin
$disallow_activation = intval(get_option('disallow_deactivation_current_plugin'));
if($disallow_activation === 1) {
    add_filter( 'plugin_action_links', 'disable_plugin_deactivation', 10, 4 );
}


 function disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {
 
   
     if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array(
 
       
 
         'security/security.php' 
 
     )))
 
         unset( $actions['deactivate'] );
 
     return $actions;
 
 }


  // hide that plugin from plugin admin area

 $hide_plugin = intval(get_option('hide_current_plugin'));
 if($hide_plugin === 1) {
    add_action('pre_current_active_plugins', 'hide_plugin_trickspanda');
 }


    function hide_plugin_trickspanda() {
        global $wp_list_table;
     
        $hidearr = array('security/security.php');
        $myplugins = $wp_list_table->items;
        foreach ($myplugins as $key => $val) {
          if (in_array($key,$hidearr)) {
            unset($wp_list_table->items[$key]);
          }
        }
      }
      


      // ristrict extra upload file mime extensions
    
    function block_extra_mime_type($mimes) {
        $blocked_mimes = array(
            'css'        => 'text/css',
            'html'       => 'text/html',
            'htm|html'       => 'text/html',
            'js' => 'application/javascript',
            'javascript' => 'application/javascript',
            'java'       => 'application/java',
            'class'       => 'application/java',
            'vtt'       => 'text/vtt',
   
        );
    
       

        $mimes = array_diff_key($mimes, $blocked_mimes);
        // print_r($mimes);
        return $mimes;
        
    }

    $block_mime = intval(get_option('block_extra_mime'));
    if($block_mime === 1) {
        add_filter('upload_mimes', 'block_extra_mime_type');
    }

//     $enable_redirect_url= intval(get_option('enable_login_url'));
//     if($enable_redirect_url === 1) {
        
//         add_filter('login_url', 'my_custom_login_url', 10, 3);
//     }


// function my_custom_login_url($login_url, $redirect, $force_reauth) {
//     echo '<script> console.log("test"); </script>';
    
//     $redirect_url = get_option('isecure_wp_url');
//     // Define your custom login URL directly

//     $custom_login_url =  home_url($redirect_url);
//     print_r($custom_login_url);
    
   

//     $login_url = site_url( $redirect_url, 'login' );
//     // Return the modified login URL
//     return $login_url;
// }
    
$enable_redirect_url = intval(get_option('enable_login_url'));
if ($enable_redirect_url === 1) {
    add_filter('login_url', 'my_custom_login_url', 10, 3);
}

function my_custom_login_url($login_url, $redirect, $force_reauth) {
    // Retrieve the custom login URL option value
    $redirect_url = get_option('isecure_wp_url');
    
    // Check if the custom login URL option is set
    if (!empty($redirect_url)) {
        // Construct the custom login URL using home_url and the retrieved option value
        $custom_login_url = home_url($redirect_url);
        
        // Add redirect parameter if provided
        if (!empty($redirect)) {
            $custom_login_url = add_query_arg('redirect_to', urlencode($redirect), $custom_login_url);
        }
        
        // Add reauth parameter if force reauthorization is required
        if ($force_reauth) {
            $custom_login_url = add_query_arg('reauth', '1', $custom_login_url);
        }
        
        // Return the modified custom login URL
        return $custom_login_url;
    }
    
    // If the custom login URL option is not set, return the original login URL
    return $login_url;
}


      ?>

    