NCSU Campus Directory
=====================

Introduction
------------

This plugin allows you to include faculty/staff and their contact information from the campus directory.  

Requirements
------------

* Must have wp_head() in template for directory to be correctly formatted
* Your server needs to have php5-ldap installed

Settings Menu
-------------

The department (or organizational unit) can be specified within the "Settings" page under the "Person" menu.  If a person is not administratively housed within your department/unit, their Unity ID can also be provided on this page.  This will allow their name and contact information to be included in your website's directory.

The campus directory is updated every morning with the prior day's directory updates.  This plugin will pull that new information every morning.  Therefore, any updates made in the master campus directory may take 1-2 days to appear on your website.  This plugin only pulls information from the campus directory.  It does not change any information with the master campus directory.

A person's individual information can be overridden on the local website.  These changes can be made in the "Person" menu of the website's administrative menu.

Syntax
------

To include a complete listing of the department specificed in "Settings", simply type in the body of any post or page:
	[ncsu_directory]
	
The directory can further be filter by faculty or staff:
	[ncsu_directory type="faculty"]
	[ncsu_directory type="staff"]
	
You can also print an individual person's contact information by using their Unity ID:
	[ncsu_directory id="UnityID"]
	
Questions and Help
------------------

Questions and additional help can be provided by Scott Thompson (scott_thompson@ncsu.edu) in the College of Sciences.