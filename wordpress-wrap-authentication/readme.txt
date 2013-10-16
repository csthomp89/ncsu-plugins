=== WRAP Authentication ===
Tags: authentication
Requires at least: 2.5.1

Use WRAP as authentication for Wordpress.

== Description ==

The WRAP authentication plugin allows you to use WRAP as an authentication source for Wordpress.

Credit goes to Daniel Westermann-Clark (http://dev.webadmin.ufl.edu/~dwc/) for writing the initial plugin that this plugin was based off of, http-authentication. 

== Installation ==

1. Login as an existing user, such as admin.
2. Upload `wrap-authentication.php` to your plugins folder, usually `wp-content/plugins`.
3. Activate the plugin on the Plugins screen.
4. Add one or more users to WordPress, specifying the Unity ID for the Nickname field. Also be sure to set the role for each user.
5. Logout.
6. Put an .htaccess file in /wp-admin with the correct WRAP user requirement settings.
7. Try logging in as one of the users added in step 4.

Note: This version works with WordPress 2.5.1 and above.