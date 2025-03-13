<?php

  add_action('admin_menu' , 'login_attempts_process_form' , 4);

  function limit_login_attempts_func() { 
    global $login_attmepts_empty_err, $duration_time_empty_err;
    ?>
 
    <h2>Limit Login Attempts</h2>

    <form action="options.php" method="POST" id="login-attempts-form">

      <div class="field-wrap">

     <?php settings_fields('isecure_limit_option_group'); ?>

        <div class="toggle-btn">
          <div><p> Enable Limit Login Attempts</p></div>

            <?php $enable_login_attempts = get_option('Enable_login_attempts'); ?>

          <label class="switch">
            <input type="checkbox" role="switch" name="Enable_login_attempts" id="checkbox-attempt" class="login-checkbox-attempt" <?php if ($enable_login_attempts === 1) echo 'checked="checked"'; ?> value="1" <?php checked(1, $enable_login_attempts)?>>
            <span class="slider round"></span>
          </label>
        </div>
        <div id="data-to-toggle">
         <div class="login-attempts">
            <label for="login-attempts">Number of login attempts allowed:</label>
            <input type="number" name="login-attempts" id="login-attempts" value="<?php echo esc_html(get_option('failed_login_limit')); ?>">
            <small class="login_attempts-error"><?php ($login_attmepts_empty_err) ? "Enter number of login attempts" : "" ?></small>
         </div>
        <div class="lockout-duration">
          <label for="locout-duration-time">Lockout Duration:</label>
          <input type="number" name="login-duration-time" id="login-duration-time" value="<?php echo esc_html(get_option('lockout_duration')); ?>"><small>Enter time in minutes</small>
          <small class="duration-error"><?php ($duration_time_empty_err) ? "Enter a time for lockout duration" : "" ?></small>
        </div>
        </div>
        
      </div>

      <?php submit_button('Save Changes'); ?>
      <?php wp_nonce_field('isecure_login_settings_form', 'isecure_login_settings_nonce'); ?>

    </form>
<?php
}

 $login_attmepts_empty_err = false;
 $duration_time_empty_err = false;


  function login_attempts_process_form(){
    
    if (isset($_POST['isecure_login_settings_nonce']) && wp_verify_nonce($_POST['isecure_login_settings_nonce'], 'isecure_login_settings_form')) {

      register_setting('isecure_limit_option_group', 'isecure_limit_option_name');

      if (isset($_POST['action']) && current_user_can('manage_options')) {
       

        update_option('Enable_login_attempts', sanitize_text_field($_POST['Enable_login_attempts']));
      

        $enable_login_attempts = intval(get_option('Enable_login_attempts'));
        echo $enable_login_attempts;
        if($enable_login_attempts === 1){

          $login_attempt = $_POST['login-attempts'];
          
          $duratiion_time = intval($_POST['login-duration-time']);
          
          
          update_option('failed_login_limit', $login_attempt);
            update_option('lockout_duration', $duratiion_time);
         }

      }
    }
  }







if ( ! class_exists( 'Limit_Login_Attempts' ) ) {
  class Limit_Login_Attempts {

      public $failed_login_limit; //= 3;                 // Number of authentication attempts allowed
      public $lockout_duration;   //= 900;              // Lockout duration in seconds (30 minutes)
      public $transient_prefix   = 'attempted_login'; // Transient prefix
      public $duration_time;
      public function __construct() {

          $lockout_duration = intval(get_option('lockout_duration'));
          $this->duration_time = $lockout_duration * 60; 
          $this->lockout_duration = $this->duration_time;
          $this->failed_login_limit = get_option('failed_login_limit');
        //  echo "blablablablablabalablab  the value of failed login attempts is" . $this->failed_login_limit . $this->lockout_duration;

          add_filter( 'authenticate', array( $this, 'check_attempted_login' ), 30, 3 );
          add_action( 'wp_login_failed', array( $this, 'login_failed' ), 10, 1 );
      }

      public function check_attempted_login( $user, $username, $password ) {
          $ip = $this->get_user_ip();

          if ( get_transient( $this->transient_prefix . '_' . $ip ) ) {
              $datas = get_transient( $this->transient_prefix . '_' . $ip );

              if ( $datas['tried'] >= $this->failed_login_limit ) {
                  $until = get_option( '_transient_timeout_' . $this->transient_prefix . '_' . $ip );
                  // print_r($until);
                  // $until = get_option('lockout_duration');
                  $time = $this->when( $until );
                  $get_sec = preg_replace("/[^0-9]/", "", $time);
                  $get_sec_num = intval($get_sec);
                  echo "<br>";
                  // echo $get_sec_num;
                  $lockout_duration = intval(get_option('lockout_duration'));
                  $lockout_duration = $lockout_duration * 60;
                  echo $lockout_duration;
                 // Add a function to hide the login form
                 echo "
                 <script>
document.addEventListener('DOMContentLoaded', function() {
  var elementsToDisable = ['wp-submit', 'user_login', 'user_pass', 'rememberme'];
  disableFormElements(elementsToDisable);


function disableFormElements(elements) {
  elements.forEach(function(elementId) {
      document.getElementById(elementId).disabled = true;
  });

  setTimeout(function() {
       location.reload(); // Reload the current page
  }, " . ( $lockout_duration * 1000) . ");

}
});
</script> ";

                  return new WP_Error( 'too_many_tried', sprintf( __( '<strong>ERROR</strong>: You have reached the authentication limit. Please try again in %1$s.' ), $time ) );
              }

             
          }

          return $user;
      }

      public function login_failed( $username ) {
          $ip = $this->get_user_ip();

          if ( get_transient( $this->transient_prefix . '_' . $ip ) ) {
              $datas = get_transient( $this->transient_prefix . '_' . $ip );
              $datas['tried']++;

              if ( $datas['tried'] <= $this->failed_login_limit ) {
                  set_transient( $this->transient_prefix . '_' . $ip, $datas, $this->lockout_duration );
              }
          } else {
              $datas = array(
                  'tried' => 1
              );
              set_transient( $this->transient_prefix . '_' . $ip, $datas, $this->lockout_duration );
          }
      }

      private function when( $time ) {
          if ( ! $time ) {
              return;
          }

          $right_now = time();

          $diff = abs( $right_now - $time );

          $second = 1;
          $minute = $second * 60;
          $hour   = $minute * 60;
          $day    = $hour * 24;

          if ( $diff < $minute ) {
              return floor( $diff / $second ) . ' secondes';
          }

          if ( $diff < $minute * 2 ) {
              return "about 1 minute ago";
          }

          if ( $diff < $hour ) {
              return floor( $diff / $minute ) . ' minutes';
          }

          if ( $diff < $hour * 2 ) {
              return 'about 1 hour';
          }

          return floor( $diff / $hour ) . ' hours';
      }
      
      
      private function get_user_ip() {
          if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
              // Check IP from shared internet
              $ip = $_SERVER['HTTP_CLIENT_IP'];
          } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
              // Check IP from proxy
              $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
          } else {
              $ip = $_SERVER['REMOTE_ADDR'];
          }

          return $ip;
      }


  }

 
}

$enable_login_attempts = intval(get_option('Enable_login_attempts'));
if($enable_login_attempts === 1){
  $abc =  new Limit_Login_Attempts();
}

?>
