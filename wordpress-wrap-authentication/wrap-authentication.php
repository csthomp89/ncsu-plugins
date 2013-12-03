<?php
/*
Credit goes to Daniel Westermann-Clark (http://dev.webadmin.ufl.edu/~dwc/) for
writing the initial plugin that this plugin was based off of. 

Plugin Name: WRAP Authentication
Version: 1.0
Plugin URI: http://webapps.ncsu.edu
Description: Authenticate users using WRAP authentication.
Author: Outreach Technology
Author URI: http://webapps.ncsu.edu
*/

if (! class_exists('WRAPAuthenticationPlugin')) {
	class WRAPAuthenticationPlugin {
		function WRAPAuthenticationPlugin() {
			if (isset($_GET['activate']) and $_GET['activate'] == 'true') {
				add_action('init', array(&$this, 'initialize_options'));
			}
			add_action('admin_menu', array(&$this, 'add_options_page'));
			add_action('wp_authenticate', array(&$this, 'authenticate'), 10, 2);
			add_filter('check_password', array(&$this, 'skip_password_check'), 10, 4);
			add_action('wp_logout', array(&$this, 'logout'));
			add_action('lost_password', array(&$this, 'disable_function'));
			add_action('retrieve_password', array(&$this, 'disable_function'));
			add_action('password_reset', array(&$this, 'disable_function'));
			add_action('check_passwords', array(&$this, 'generate_password'), 10, 3);
			add_filter('show_password_fields', array(&$this, 'disable_password_fields'));
			add_action('user_register', 'add_wrap_user');
			add_action('delete_user', 'delete_wrap_user');
		}


		/*************************************************************
		 * Plugin hooks
		 *************************************************************/

		/*
		 * Add options for this plugin to the database.
		 */
		function initialize_options() {
			if (current_user_can('manage_options')) {
				add_option('wrap_authentication_auto_create_user', false, 'Should a new user be created automatically if not already in the WordPress database?');
			}
		}

		/*
		 * Add an options pane for this plugin.
		 */
		function add_options_page() {
			if (function_exists('add_options_page')) {
				add_options_page('WRAP Authentication', 'WRAP Authentication', 9, __FILE__, array(&$this, '_display_options_page'));
			}
		}

		/*
		 * If the REMOTE_USER evironment is set, use it as the username.
		 * This assumes that you have externally authenticated the user.
		 */
		function authenticate($username, $password) {
			
			$username = (getenv('WRAP_USERID') == '') ? getenv('REDIRECT_WRAP_USERID') : getenv('WRAP_USERID');
	
	        if ($username == '') {
	            $username = getenv('REDIRECT_WRAP_USERID');
	        }
	        
	        if ($username == '') {
	            setrawcookie('WRAP_REFERER', $this->_getUrl(), 0, '/', '.ncsu.edu');
	            header('location:https://webauth.ncsu.edu/wrap-bin/was16.cgi');
	            die();
	        }

			// Fake WordPress into authenticating by overriding the credentials
			$password = $this->_get_password();

			// Create new users automatically, if configured
			$user = get_userdatabylogin($username);
			if (! $user or $user->user_login != $username) {
				if ((bool) get_option('wrap_authentication_auto_create_user')) {
					$this->_create_user($username);
				}
				else {
					// Bail out to avoid showing the login form
					die("User $username does not exist in the WordPress database");
				}
			}
		}

	    /**
	     * Gets the current URL
	     *
	     * @return string
	     */
	    function _getURL()
	    {
	        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	
	        $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
	
	        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	
	        return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
	    }		
	    
		/*
		 * Skip the password check, since we've externally authenticated.
		 */
		function skip_password_check($check, $password, $hash, $user_id) {
			return true;
		}

		/*
		 * Logout the user by redirecting them to the logout URI.
		 */
		function logout() {
			
		    foreach (array_keys($_COOKIE) as $name) {
	            if (preg_match('/^WRAP.*/',$name)) {
	
	                // set the expiration date to one hour ago
	                setcookie($name, "", time() - 3600, "/", "ncsu.edu");
	            }
	        }
	        
	        wp_clearcookie();
            nocache_headers();

	        header('Location:' . get_option('siteurl'));
	        exit();
		}

		/*
		 * Generate a password for the user. This plugin does not
		 * require the user to enter this value, but we want to set it
		 * to something nonobvious.
		 */
		function generate_password($username, $password1, $password2) {
			$password1 = $password2 = $this->_get_password();
		}

		/*
		 * Used to disable certain display elements, e.g. password
		 * fields on profile screen.
		 */
		function disable_password_fields($show_password_fields) {
			return false;
		}

		/*
		 * Used to disable certain login functions, e.g. retrieving a
		 * user's password.
		 */
		function disable_function() {
			die('Disabled');
		}


		/*************************************************************
		 * Functions
		 *************************************************************/

		/*
		 * Generate a random password.
		 */
		function _get_password($length = 10) {
			return substr(md5(uniqid(microtime())), 0, $length);
		}

		/*
		 * Create a new WordPress account for the specified username.
		 */
		function _create_user($username) {
			$password = $this->_get_password();

			require_once(WPINC . DIRECTORY_SEPARATOR . 'registration.php');
			wp_create_user($username, $password, $username . '@ncsu.edu');
		}

		/*
		 * Display the options for this plugin.
		 */
		function _display_options_page() {
			$auto_create_user = (bool) get_option('wrap_authentication_auto_create_user');
?>
<div class="wrap">
  <h2>WRAP Authentication Options</h2>
  <form action="options.php" method="post">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="wrap_authentication_auto_create_user" />
    <?php if (function_exists('wp_nonce_field')): wp_nonce_field('update-options'); endif; ?>

    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="wrap_authentication_auto_create_user">Automatically create accounts?</label></th>
        <td>
          <input type="checkbox" name="wrap_authentication_auto_create_user" id="wrap_authentication_auto_create_user"<?php if ($auto_create_user) echo ' checked="checked"' ?> value="1" /><br />
          Should a new user be created automatically if not already in the WordPress database?<br />
          Created users will obtain the role defined under &quot;New User Default Role&quot; on the <a href="options-general.php">General Options</a> page.
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="Submit" value="Save Changes" />
    </p>
  </form>
</div>
<?php
		}
	}
}

// Load the plugin hooks, etc.
$wrap_authentication_plugin = new WRAPAuthenticationPlugin();

                /*
                 * Adding Unity ID to .htaccess file on user creation
                 */

                function add_wrap_user($user_id) {

                        $file = file_get_contents(ABSPATH . '.htaccess');

                        $before_length = strpos($file, "#ADD NEW USER HERE");

                        $before = substr($file, 0, $before_length);
                        $after = substr($file, $before_length);

                        $user_info = get_userdata($user_id);
                        $username = $user_info->user_login;
                        file_put_contents(ABSPATH . '.htaccess', $before . "  require user " . $username . "\n" . $after);
                }


                /*
                 * Removing Unity ID from .htaccess file on user deletion
                 */

                function delete_wrap_user($user_id) {
                        $file = file_get_contents(ABSPATH . '.htaccess');

                        $user_info = get_userdata($user_id);
                        $username = $user_info->user_login;

                        $before_length = strpos($file, "  require user " . $username);

                        $user_length = strlen("  require user " . $username . "\n");

                        $before = substr($file, 0, $before_length);
                        $after = substr($file, $before_length+$user_length);

                        file_put_contents(ABSPATH . '.htaccess', $before . $after);
                }



?>
