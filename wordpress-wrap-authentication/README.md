WRAP Authentication
===================

Use NCSU WRAP as authentication for Wordpress.

Requirements
------------

* Wordpress 2.5.1 or newer
* .htaccess file must be writeable by the webserver

Description
-----------

The WRAP authentication plugin allows you to use WRAP as an authentication source for Wordpress.

Credit goes to Daniel Westermann-Clark (http://dev.webadmin.ufl.edu/~dwc/) for writing the initial plugin that this plugin was based off of, http-authentication. 

Installation
------------

1. Login as an existing user, such as admin.
2. Add the .htaccess to your web root, or add the following code to the beginning of your existing .htaccess file.  Your .htaccess file must be writeable by the webserver.

	#BEGIN WRAP
	<Files wp-login.php>
	  AuthType WRAP
	  require affiliation ncsu.edu
	  #ADD NEW USER HERE
	</Files>
	#END WRAP

3. Upload "wordpress-wrap-authentication" folder to your plugins folder, usually `wp-content/plugins`.
4. Activate the plugin on the Plugins screen.
5. Add one or more users to WordPress, specifying the Unity ID as the username. Also be sure to set the role for each user.

Contact
-------

The NC State implementation of this plugin was originally created by Outreach Technology in OIT.  That plugin was adapted by Scott Thompson, College of Sciences (scott_thompson@ncsu.edu) to automatically add/remove Unity IDs from the .htaccess file.