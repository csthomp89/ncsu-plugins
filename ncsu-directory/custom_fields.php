<?php
	
/**
 *  Install Add-ons
 *  
 *  The following code will include all 4 premium Add-Ons in your theme.
 *  Please do not attempt to include a file which does not exist. This will produce an error.
 *  
 *  The following code assumes you have a folder 'add-ons' inside your theme.
 *
 *  IMPORTANT
 *  Add-ons may be included in a premium theme/plugin as outlined in the terms and conditions.
 *  For more information, please read:
 *  - http://www.advancedcustomfields.com/terms-conditions/
 *  - http://www.advancedcustomfields.com/resources/getting-started/including-lite-mode-in-a-plugin-theme/
 */ 

// Add-ons 
// include_once('add-ons/acf-repeater/acf-repeater.php');
// include_once('add-ons/acf-gallery/acf-gallery.php');
// include_once('add-ons/acf-flexible-content/acf-flexible-content.php');
// include_once( 'add-ons/acf-options-page/acf-options-page.php' );


/**
 *  Register Field Groups
 *
 *  The register_field_group function accepts 1 array which holds the relevant data to register a field group
 *  You may edit the array as you see fit. However, this may result in errors if the array is not compatible with ACF
 */

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_person',
		'title' => 'Person',
		'fields' => array (
			array (
				'key' => 'field_52531bd99fccb',
				'label' => 'Image',
				'name' => 'image',
				'type' => 'image',
				'save_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'uploadedTo',
			),
			array (
				'key' => 'field_52570b1fee3cd',
				'label' => 'Description',
				'name' => 'description',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_52531acc229f7',
				'label' => 'Primary Focus Area',
				'name' => 'primary_focus_area',
				'type' => 'select',
				'choices' => array (
					'Toxicology and Environmental Health Sciences' => 'Toxicology and Environmental Health Sciences',
					'Molecular, Cellular and Developmental Biology' => 'Molecular, Cellular and Developmental Biology',
					'Integrative Physiology, Neurobiology & Behavior' => 'Integrative Physiology, Neurobiology & Behavior',
					'Ecology & Evolutionary Biology' => 'Ecology & Evolutionary Biology',
					'Public Outreach' => 'Public Outreach',
					'Genetics, Genomics & Bioinformatics' => 'Genetics, Genomics & Bioinformatics',
					'Education and Outreach' => 'Education and Outreach',
				),
				'default_value' => '',
				'allow_null' => 1,
				'multiple' => 0,
			),
			array (
				'key' => 'field_52531b79d5f86',
				'label' => 'Secondary Focus Area',
				'name' => 'secondary_focus_area',
				'type' => 'select',
				'choices' => array (
					'Toxicology and Environmental Health Sciences' => 'Toxicology and Environmental Health Sciences',
					'Molecular, Cellular and Developmental Biology' => 'Molecular, Cellular and Developmental Biology',
					'Integrative Physiology, Neurobiology & Behavior' => 'Integrative Physiology, Neurobiology & Behavior',
					'Ecology & Evolutionary Biology' => 'Ecology & Evolutionary Biology',
					'Public Outreach' => 'Public Outreach',
					'Genetics, Genomics & Bioinformatics' => 'Genetics, Genomics & Bioinformatics',
					'Education and Outreach' => 'Education and Outreach',
				),
				'default_value' => '',
				'allow_null' => 1,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5254613127250',
				'label' => 'Customize Data',
				'name' => 'customize_data',
				'type' => 'true_false',
				'instructions' => 'Allows you to override some or all of the values provided by the campus directory.	It is recommended that you update your information in the campus directory instead of by overriding it on the site.',
				'message' => '',
				'default_value' => 0,
			),
			array (
				'key' => 'field_5254619c57410',
				'label' => 'Name',
				'name' => 'name',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_5254613127250',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_525461b157411',
				'label' => 'Title',
				'name' => 'title',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_5254613127250',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_525461c857412',
				'label' => 'Address',
				'name' => 'address',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_5254613127250',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_525461e557413',
				'label' => 'Website',
				'name' => 'website',
				'type' => 'text',
				'instructions' => 'Make sure that the URL is prepended with "http://"',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_5254613127250',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5254621e57414',
				'label' => 'Phone',
				'name' => 'phone',
				'type' => 'text',
				'instructions' => 'Please include area code',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_5254613127250',
							'operator' => '==',
							'value' => '1',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ncsu_person',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

	
?>