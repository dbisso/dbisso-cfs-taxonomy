<?php
/*
Plugin Name: CFS Taxonomy Field Add-on
Plugin URI: http://danisadesigner.com
Description: A taxonomy field type for Custom Field Suite (add-on).
Version: 1.0.0
Author: Dan Bissonnet
Author URI: http://danisadesigner.com
License: GPL2
*/

class DBissoCFSTaxonomyAddon {
	static function bootstrap() {
		add_filter( 'cfs_field_types', array( __CLASS__, 'cfs_field_types' ) );
	}

	static function cfs_field_types( $field_types ) {
		$field_types['taxonomy'] = dirname( __FILE__ ) . '/taxonomy.php';
		return $field_types;
	}
}

DBissoCFSTaxonomyAddon::bootstrap();