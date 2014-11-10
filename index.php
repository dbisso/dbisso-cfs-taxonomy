<?php
/*
Plugin Name: CFS Taxonomy Field Add-on
Plugin URI: http://danisadesigner.com
Description: A taxonomy field type for Custom Field Suite (add-on).
Version: 1.1.0
Author: Dan Bissonnet
Author URI: http://danisadesigner.com
License: GPL-2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: dbisso-cfs-taxonomy-field
Domain Path: /languages
*/

class DBissoCFSTaxonomyAddon {
	static function bootstrap() {
		add_filter( 'cfs_field_types', array( __CLASS__, 'cfs_field_types' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin_textdomain' ) );
	}

	static function cfs_field_types( $field_types ) {
		$field_types['taxonomy'] = dirname( __FILE__ ) . '/taxonomy.php';
		return $field_types;
	}

	static function load_plugin_textdomain() {
		load_plugin_textdomain(
			'dbisso-cfs-taxonomy-field',
			false,
			__FILE__ . '/languages/'
		);
	}
}

DBissoCFSTaxonomyAddon::bootstrap();