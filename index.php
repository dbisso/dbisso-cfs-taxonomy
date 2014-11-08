<?php
/*
Plugin Name: CFS - Taxonomy Add-on
Plugin URI: http://danisadesigner.com
Description: A taxonomy field type for Custom Field Suite (add-on).
Version: 1.0.0
Author: Dan Bissonnet
Author URI: http://danisadesigner.com
License: GPL2
*/

$dbisso_cfs_taxonomy_addon = new dbisso_cfs_taxonomy_addon();

class dbisso_cfs_taxonomy_addon {
	function __construct() {
		add_filter( 'cfs_field_types', array( $this, 'cfs_field_types' ) );
	}

	function cfs_field_types( $field_types ) {
		$field_types['taxonomy'] = dirname( __FILE__ ) . '/taxonomy.php';
		return $field_types;
	}
}