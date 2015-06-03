<?php

/**
 * A CFS field that allows the user to choose one or more terms from a given taxonomy
 */
class cfs_taxonomy extends cfs_field {
	function __construct() {
		$this->name = 'taxonomy';
		$this->label = __( 'Taxonomy', 'dbisso-cfs-taxonomy-field' );
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
				<label><?php _e( 'Taxonomy', 'dbisso-cfs-taxonomy-field' ); ?></label>
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
				<label><?php _e( 'Multiple Terms', 'dbisso-cfs-taxonomy-field' ); ?></label>
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

	/**
	 * Displays the HTML for our field.
	 *
	 * @param  $field object the current CFS field object
	 * @return void
	 */
	function html( $field ) {
		$taxonomy_name = $this->get_option( $field, 'taxonomy', 'category' );

		if ( empty( $taxonomy_name ) ) {
			return;
		}

		$this->multi_select( $field, $taxonomy_name );
		$this->single_select( $field, $taxonomy_name );
	}

	/**
	 * Outputs HTML for a drop-down select taxonomy input.
	 *
	 * @param  $field object the current CFS field object
	 * @param  $taxonomy_name string the name of the taxonomy to get terms from.
	 * @return void
	 */
	function single_select( $field, $taxonomy_name ) {
		if ( $this->get_option( $field, 'multiple', 0 ) ) {
			return;
		}

		wp_dropdown_categories(
			array(
				'selected'         => $field->value[0],
				'taxonomy'         => $taxonomy_name,
				'hide_empty'       => 0,
				'name'             => $field->input_name,
				'orderby'          => 'name',
				'hierarchical'     => 1,
				'show_option_none' => '&mdash; ' . __( 'Choose', 'dbisso-cfs-taxonomy-field' ) . ' &mdash;'
			)
		);
	}

	/**
	 * Outputs HTML for a multi-select checkbox input.
	 *
	 * @param  $field object the current CFS field object
	 * @param  $taxonomy_name string the name of the taxonomy to get terms from.
	 * @return void
	 */
	function multi_select( $field, $taxonomy_name ) {
		if ( ! $this->get_option( $field, 'multiple', 0 ) ) {
			return;
		}

		require_once __DIR__ . '/cfs_category_checklist_walker.php';

		$taxonomy = get_taxonomy( $taxonomy_name );
		$walker   = new cfs_category_checklist_walker;

		$walker->field_name = $field->input_name;
		echo '<ul>';
		wp_terms_checklist(
			null,
			array(
				'taxonomy'      => $taxonomy_name,
				'walker'        => $walker,
				'selected_cats' => (array) $field->value,
			)
		);
		echo '</ul>';
	}

	/**
	 * Serializes our field value before it is saved.
	 *
	 * @param  $value mixed the value to be saved to the database.
	 * @param  $field object the current CFS field object
	 * @return $value object a serialized value object.
	 */
	function pre_save( $value, $field = null ) {
		return serialize( $value );
	}

	/**
	 * Unserializes our field value before it is passed to the CFS API.
	 *
	 * @param  $value mixed the value to be passed to the API.
	 * @param  $field object the current CFS field object
	 * @return $value object a serialized value object.	 */
	function prepare_value( $value, $field = null ) {
		return unserialize( $value[0] );
	}

	/**
	 * Returns the value for the API.
	 *
	 * In this case, the term IDs are converted into and array of term objects.
	 *
	 * @return array An array of term objects
	 */
	function format_value_for_api( $value, $field = null ) {
		$output = null;

		// Store the taxonomy name so we don't need to find it for each term
		$this->taxonomy_name = $this->get_option( $field, 'taxonomy' );

		if ( ! empty( $value ) ) {
			$output = array_map( array( $this, 'get_term_for_api' ), $value );
		}

		return $output;
	}

	function get_term_for_api( $term_id ) {
		if ( (int) $term_id === -1 ) return false;
		return get_term( (int) $term_id, $this->taxonomy_name );
	}
}
