<?php

/**
 * A CFS field that allows the user to choose one or more terms from a given taxonomy
 */
class cfs_taxonomy extends cfs_field {
	function __construct() {
		$this->name = 'taxonomy';
		$this->label = __( 'Taxonomy', 'cfs' );
	}

	function options_html( $key, $field ) {
		$choices = array();
		$taxonomies = array_map( 'get_taxonomy', get_taxonomies() );

		foreach ( $taxonomies as $name => $taxonomy ) {
			$choices[ $name ] = $taxonomy->labels->name;
		}
		?>
		<tr class="field_option field_option_<?php esc_attr_e( $this->name ) ?>">
			<td class="label">
				<label><?php _e( 'Taxonomy', 'cfs' ); ?></label>
			</td>
			<td>
				<?php
					CFS()->create_field(array(
						'type' => 'select',
						'input_name' => "cfs[fields][$key][options][taxonomy]",
						'options' => array(
							'choices' => $choices,
							'force_single' => true,
						),
						'value' => $this->get_option( $field, 'taxonomy', 'default' ),
					));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php esc_attr_e( $this->name ) ?>">
			<td class="label">
				<label><?php _e( 'Multiple Terms', 'cfs' ); ?></label>
			</td>
			<td>
				<?php
					CFS()->create_field(array(
						'type' => 'true_false',
						'input_name' => "cfs[fields][$key][options][multiple]",
						'value' => $this->get_option( $field, 'multiple', 0 ),
					));
				?>
			</td>
		</tr>
	<?php
	}

	function html( $field ) {
		// Load our custom walker
		require_once __DIR__ . '/cfs_category_checklist_walker.php';

		$taxonomy_name = $this->get_option( $field, 'taxonomy', 'category' );
		$multiple      = $this->get_option( $field, 'multiple', 0 );
		$taxonomy      = get_taxonomy( $taxonomy_name );

		if ( ! empty($taxonomy_name) && $multiple ) {
			$walker = new cfs_category_checklist_walker;
			$walker->field_name = $field->input_name;

			wp_terms_checklist(
				null,
				array(
					'taxonomy' => $taxonomy_name,
					'walker' => $walker,
					'selected_cats' => $field->value,
				)
			);
		} else {
			wp_dropdown_categories(
				array(
					'selected'         => $field->value[0],
					'taxonomy'         => $taxonomy_name,
					'hide_empty'       => 0,
					'name'             => $field->input_name,
					'orderby'          => 'name',
					'hierarchical'     => 1,
					'show_option_none' => '&mdash; ' . __( 'Choose', 'cfs_taxonomy_field' ) . ' &mdash;'
				)
			);
		}


	}

	function pre_save( $value, $field ) {
		return serialize( $value );
	}

	function prepare_value( $value, $field ) {
		return unserialize( $value[0] );
	}

	/**
	 * Returns the value for the API.
	 *
	 * In this case, the term IDs are converted into and array of term objects.
	 * @return array        An array of term objects
	 */
	function format_value_for_api( $value, $field ) {
		$output = null;

		// Store the taxonomy name so we don't need to find it for each term
		$this->taxonomy_name = $this->get_option( $field, 'taxonomy' );

		if ( ! empty($value) ) {
			$output = array_map( array( $this, 'get_term_for_api' ), $value );
		}

		return $output;
	}

	function get_term_for_api( $term_id ) {
		if ( (int) $term_id === -1 ) return false;
		return get_term( (int) $term_id, $this->taxonomy_name );
	}
}