<?php
/**
 * Plugin Name: NC State Directory
 * Plugin URL: http://sciences.ncsu.edu/plugins
 * Description: Allows information about a person or department to be listed on a Wordpress page
 * Version: 1.0
 * Author: Scott Thompson, College of Sciences
 */

/*
 *
 * Adding/removing cron job
 *
 */

register_activation_hook(__FILE__, 'ncsu_directory_activation');
register_deactivation_hook(__FILE__, 'ncsu_directory_deactivation');
add_action('ncsu_directory_refresh', 'ncsu_directory_query');

function ncsu_directory_activation() {
	wp_schedule_event('1381668300','daily', 'ncsu_directory_query');
}

function ncsu_directory_deactivation() {
	wp_unschedule_event('1381668300','ncsu_directory_query');
}

/*
 *
 * Creating custom post type
 *
 */

include_once 'custom_fields.php';

add_action( 'init', 'create_person_post_type' );
function create_person_post_type() {
	register_post_type( 'ncsu_person',
		array(
			'labels' => array(
				'name' => __( 'Person' ),
				'singular_name' => __( 'Person' )
			),
		'public' => true,
		'has_archive' => true,
		)
	);
}

/*
 *
 * Adding shortcode
 *
 */

function ncsu_directory_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => 'department',
		'type' => 'all'
	), $atts));
	if($type=="staff") {
		return ncsu_get_staff();
	}
	if($type=="faculty") {
		return ncsu_get_faculty();
	}
	/* Individual research or administrative units can be added here */
	/*if($type=="tox") {
		return ncsu_get_focus_area("Toxicology and Environmental Health Sciences");
	} else if($type=="mol") {
		return ncsu_get_focus_area("Molecular, Cellular and Developmental Biology");
	} else if($type=="phy") {
		return ncsu_get_focus_area("Integrative Physiology, Neurobiology & Behavior");
	} else if($type=="eco") {
		return ncsu_get_focus_area("Ecology & Evolutionary Biology");
	} else if ($type=="po") {
		return ncsu_get_focus_area("Public Outreach");
	} else if ($type=="gen") {
		return ncsu_get_focus_area("Genetics, Genomics & Bioinformatics");
	} else if ($type=="edu") {
		return ncsu_get_focus_area("Education and Outreach");
	}*/
	if($id=="department") {
		return ncsu_get_department();
	}
	return ncsu_get_person($id);
}
add_shortcode( 'ncsu_directory', 'ncsu_directory_shortcode' );

/*
 *
 * Communicating with webpages
 *
 */

function ncsu_get_person($unity_id) {
	global $wpdb;
	$person = $wpdb->get_row("SELECT * FROM wp_ncsu_directory WHERE unity_id='$unity_id'", ARRAY_A);
	$existing_post = array(
		'name' => $unity_id,
		'post_type' => 'ncsu_person'
	);
	$person_type = get_posts($existing_post);
	$meta = get_post_meta($person_type[0]->ID);
	return ncsu_format_person($person, $meta);
}

// Uncomment this code if you would like to use focus/administrative areas
/*function ncsu_get_focus_area($fa) {
	global $wpdb;
	$primary_fa = array(
		'meta_key' => 'primary_focus_area',
		'meta_value' => $fa,
		'post_type' => 'ncsu_person',
		'posts_per_page' => -1
	);
	$fa_members = get_posts($primary_fa);
	$secondary_fa = array(
                'meta_key' => 'secondary_focus_area',
                'meta_value' => $fa,
                'post_type' => 'ncsu_person',
                'posts_per_page' => -1
        );
	$fa_members = array_merge($fa_members, get_posts($secondary_fa));
	foreach($fa_members as $person) {
		$unity_id=$person->post_name;
        	$person_db = $wpdb->get_results("SELECT * FROM wp_ncsu_directory WHERE unity_id='$unity_id' ORDER BY alpha_name ASC", ARRAY_A);
		$person_meta = get_post_meta($person->ID);
		// Prints section letter label based upon people's last names 
                $cur_last_name_letter = substr($person_db[0]['alpha_name'],0,1);
                if($cur_last_name_letter > $last_name_letter) {
                        $people .= '<p class="letter_section">&raquo; <u>' . $cur_last_name_letter . '</u></p>';
                        $last_name_letter = $cur_last_name_letter;
                }
                $people .= ncsu_format_person($person_db[0], $person_meta);
	}
	return $people;
}*/

function ncsu_get_department() {
	global $wpdb;
	$department = $wpdb->get_results("SELECT * FROM wp_ncsu_directory ORDER BY alpha_name ASC", ARRAY_A);
	$people .= 'Jump to:';
	for($i=A; $i!='Z'; $i++) {
		$people .= ' <a href="#' . $i . '">' . $i . '</a> |';
	}
	$people .= ' <a href="#Z">Z</a>';
	foreach($department as $person) {
		$existing_post = array(
			'name' => $person['unity_id'],
			'post_type' => 'ncsu_person'
		);
		$person_type = get_posts($existing_post);
		$meta = get_post_meta($person_type[0]->ID);
		/* Prints section letter label based upon people's last names */
		$cur_last_name_letter = substr($person['alpha_name'],0,1);
		if($cur_last_name_letter > $last_name_letter) {
			$people .= '<p class="letter_section">&raquo; <a name="' . $cur_last_name_letter . '"><u>' . $cur_last_name_letter . '</u></a></p>';
			$last_name_letter = $cur_last_name_letter;
		}
		$people .= ncsu_format_person($person, $meta);
	}
	return $people;
}

function ncsu_get_staff() {
	global $wpdb;
	$department = $wpdb->get_results("SELECT * FROM wp_ncsu_directory WHERE role='staff' ORDER BY alpha_name ASC", ARRAY_A);
	        $people .= 'Jump to:';
        for($i=A; $i!='Z'; $i++) {
                $people .= ' <a href="#' . $i . '">' . $i . '</a> |';
        }
        $people .= ' <a href="#Z">Z</a>';
	foreach($department as $person) {
		$existing_post = array(
			'name' => $person['unity_id'],
			'post_type' => 'ncsu_person'
		);
		$person_type = get_posts($existing_post);
		$meta = get_post_meta($person_type[0]->ID);
		/* Prints section letter label based upon people's last names */
		$cur_last_name_letter = substr($person['alpha_name'],0,1);
		if($cur_last_name_letter > $last_name_letter) {
			$people .= '<p class="letter_section">&raquo; <a name="' . $cur_last_name_letter . '"><u>' . $cur_last_name_letter . '</u></a></p>';
			$last_name_letter = $cur_last_name_letter;
		}
		$people .= ncsu_format_person($person, $meta);
	}
	return $people;
}

function ncsu_get_faculty() {
	global $wpdb;
	$department = $wpdb->get_results("SELECT * FROM wp_ncsu_directory WHERE role='faculty' ORDER BY alpha_name ASC", ARRAY_A);
	$people .= 'Jump to:';
        for($i=A; $i!='Z'; $i++) {
                $people .= ' <a href="#' . $i . '">' . $i . '</a> |';
        }
        $people .= ' <a href="#Z">Z</a>';
	foreach($department as $person) {
		$existing_post = array(
			'name' => $person['unity_id'],
			'post_type' => 'ncsu_person'
		);
		$person_type = get_posts($existing_post);
		$meta = get_post_meta($person_type[0]->ID);
		/* Prints section letter label based upon people's last names */
		$cur_last_name_letter = substr($person['alpha_name'],0,1);
		if($cur_last_name_letter > $last_name_letter) {
			$people .= '<p class="letter_section">&raquo; <a name="' . $cur_last_name_letter . '"><u>' . $cur_last_name_letter . '</u></a></p>';
			$last_name_letter = $cur_last_name_letter;
		}
		$people .= ncsu_format_person($person, $meta);
	}
	return $people;
}

function ncsu_format_person($p, $m) {
	$person .= '<div class="ncsu_person">';
	$image = wp_get_attachment_image($m['image'][0]);
	if($image==null) {
		$person .= '<img src="/wp-content/plugins/ncsu-directory/person_placeholder.png" alt="No Picture" />';
	} else {
		$person .= $image;
	}
	$person .= '<div class="person_info">';
	if($m["name"][0]!="") {
		$person .= '<p class="person_name"><strong>' . $m["name"][0] . '</strong></p>';
	} else {
		$person .= '<p class="person_name"><strong>' . $p["name"] . '</strong></p>';
	}
	if($m["title"][0]!="") {
		$person .= '<p class="title"><strong>' . $m["title"][0] . '</strong></p>';
	} else {
		$person .= '<p class="title"><strong>' . $p["title"] . '</strong></p>';
	}
	if($m["description"][0]!="") {
		$person .= '<p class="description">' . $m["description"][0] . '</p>';
	}
	if($m["address"][0]!="") {
		$address = explode('$', $m["address"][0]);
		$address = explode(',', $address[0]);
		$person .= '<p class="address">' . $address[0] . '</p>';
	} else {
		$address = explode('$', $p["address"]);
		$address = explode(',', $address[0]);
		$person .= '<p class="address">' . $address[0] . '</p>';
	}
	if($m['website'][0]!='') {
		$person .= '<p class="website"><a href="' . $m["website"][0] . '">' . $m["website"][0] . '</a></p>';
	} else {
		if($p['website']!='') {
			$person .= '<p class="website"><a href="' . $p["website"] . '">' . $p["website"] . '</a></p>';
		}
	}
	if($m['email'][0]!="") {
		$person .= '<p class="email"><a href="mailto:' . $m["email"][0] . '">' . $m["email"][0] . '</a></p>';
	} else {
		$person .= '<p class="email"><a href="mailto:' . $p["email"] . '">' . $p["email"] . '</a></p>';
	}
	if($m['phone'][0]!="") {
		$person .= '<p class="phone"><a href="tel:' . $m["phone"][0] . '">' . $m["phone"][0] . '</a></p>';
	} else {
		$person .= '<p class="phone"><a href="tel:' . $p["phone"] . '">' . $p["phone"] . '</a></p>';
	}
	if($m["primary_focus_area"][0]!="null" && $m["primary_focus_area"][0]!="") {
		$person .= '<p class="focus-area"><strong>Primary Focus Area:</strong> ' . $m["primary_focus_area"][0] . '</p>';
	}
	if ($m["secondary_focus_area"][0]!="null" && $m["secondary_focus_area"][0]!="") {
		$person .= '<p class="focus-area"><strong>Secondary Focus Area:</strong> ' . $m["secondary_focus_area"][0] . '</p>';	
	}
	$person .= '</div>';
	$person .= '</div>';
	return $person;
}

function ncsu_update_directory() {
	ncsu_directory_db_install();
	ncsu_directory_query();
}

function ncsu_return_person_info() {
	
}

function ncsu_add_person() {
	
}

/*
 *
 * Querying the campus directory
 *
 */

function ncsu_directory_query() {
	$ds = @ldap_connect("ldap.ncsu.edu");	# Open connection to ldap.ncsu.edu
	if (!$ds) {
		die("Unable to connect to ldap.ncsu.edu.");
	}

	$department = get_option('department');

	$res = @ldap_bind($ds);			# Anonymous bind
	$srch = @ldap_search($ds,
		"ou=employees,ou=people,dc=ncsu,dc=edu",	# Search in employees tree (directory info)
		"(|(ou=" . $department . ")(ncsuAffilitation=" . $department . ")(ncsuCurriculumCode=" . $department . "))", # Search on Department
		array('uid'), # We want Unity ID
		0,				# We want values and types (see docs)
		0				# We want all results
	);

	if ($srch) {
		$results = @ldap_get_entries($ds, $srch);    # Retrieve all results
		$temp_ids = get_option('unity_ids');
		$manual_ids = explode(',', $temp_ids);
		foreach ($manual_ids as $id) {
			$obj_id = array('uid' => array(trim($id)));
			array_push($results, $obj_id);
		}
		foreach ($results as $i) {
			if($i['uid'][0]!="") {
				$dn = "uid=" . $i['uid'][0] . ",ou=employees,ou=people,dc=ncsu,dc=edu";
				$filter="(objectclass=*)"; // this command requires some filter
				$justthese = array('displayName', 'ncsuAltDisplayName', 'ncsuPreferredDepartment', 'mail', 'ncsuWebSite', 'registeredAddress', 'telephoneNumber', 'title', 'uid', 'ncsuPrimaryRole');
				$sr=ldap_read($ds, $dn, $filter, $justthese);
				$entry = ldap_get_entries($ds, $sr);
				$i = $entry[0];
				ncsu_directory_db_insert_data($i['displayname'][0], $i['ncsualtdisplayname'][0], $i['ncsupreferreddepartment'][0], $i['mail'][0], $i['ncsuwebsite'][0], $i['registeredaddress'][0], $i['telephonenumber'][0], $i['title'][0], $i['uid'][0], $i['ncsuprimaryrole'][0]);
				$existing_post = array(
					'name' => $i['uid'][0],
					'post_type' => 'ncsu_person'
				);
				if(!get_posts($existing_post)) {
					$post = array(
						'post_title' => $i['displayname'][0],
						'post_name' => $i['uid'][0],
						'post_type' => 'ncsu_person',
						'post_status' => 'publish'
					);
					wp_insert_post($post);
				}
			}
		}
		$args = array(
			'post_type' => 'ncsu_person',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$current_people = get_posts($args);
		foreach($current_people as $person) {
			global $wpdb;
			$unity_id = $person->post_name;
			$result = $wpdb->get_row("SELECT unity_id FROM wp_ncsu_directory WHERE unity_id='$unity_id'");
			if($result==null) {
				wp_trash_post($person->ID);
			}
		}
	}
	else {
		print "<B>Directory lookup failed: ".ldap_error($ds)."</B><BR>\n";
	}

	@ldap_close($ds);			# Close off my connection
}

/*
 *
 * Creating the database table
 *
 */

global $jal_db_version;
$jal_db_version = "1.0";

register_activation_hook( __FILE__, 'ncsu_directory_db_install' );
register_activation_hook( __FILE__, 'ncsu_directory_query' );

function ncsu_directory_db_install() {
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "ncsu_directory";
   
   $wpdb->query("DROP TABLE IF EXISTS $table_name");
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name tinytext NOT NULL,
  alpha_name tinytext NOT NULL,
  department text NOT NULL,
  email text NOT NULL,
  website text DEFAULT '',
  address text NOT NULL,
  phone text NOT NULL,
  title text NOT NULL,
  unity_id text NOT NULL,
  role text NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "jal_db_version", $jal_db_version );
}

function ncsu_directory_db_insert_data($name, $alpha_name, $department, $email, $website, $address, $phone, $title, $unity_id, $role) {
   global $wpdb;
   
   $table_name = $wpdb->prefix . "ncsu_directory";

   $rows_affected = $wpdb->replace( $table_name, array( 'name' => $name, 'alpha_name' => $alpha_name, 'department' => $department, 'email' => $email, 'website' => $website, 'address' => $address, 'phone' => $phone, 'title' => $title, 'unity_id' => $unity_id, 'role' => $role ) );
}

/*
 *
 * Creating settings/options page
 *
 */

add_action( 'admin_menu', 'ncsu_plugin_menu' );

function ncsu_plugin_menu() {
	$hook_suffix = add_submenu_page( 'edit.php?post_type=ncsu_person', 'NCSU Directory Options', 'Settings', 'activate_plugins', 'ncsu_person_options', 'ncsu_plugin_options' );
	add_action('admin_init', 'ncsu_register_settings');
	add_action('load-' . $hook_suffix, 'ncsu_update_directory');
}

function ncsu_register_settings() {
	register_setting('ncsu_directory_settings', 'department');
	register_setting('ncsu_directory_settings', 'unity_ids');
}

function ncsu_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	screen_icon();
	echo '<h2>NC State Directory Settings</h2>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'ncsu_directory_settings' );
	//do_settings( 'ncsu_directory_settings' );
	echo '<p>Department:</p>';
	echo '<input type="text" name="department" value="' . get_option('department') . '" /><br /><br />';
	echo '<p>Additional Unity IDs:</p>';
	echo '<textarea rows=5 cols=40 name="unity_ids">' . get_option('unity_ids') . '</textarea>';
	submit_button();
	echo '</form>';
	echo '</div>';
}

/*
 *
 * Adding plugin styles
 *
 */

add_action( 'wp_enqueue_scripts', 'add_ncsu_directory_stylesheet' );
add_action('init', 'prefix_add_ncsu_directory_stylesheet');

function add_ncsu_directory_stylesheet() {
    wp_register_style( 'ncsu-directory-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'ncsu-directory-style' );
}

?>
