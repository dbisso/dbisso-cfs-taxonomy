<?php
/**
 * Walker class that allows us to set a custom name attribute for the checkbox
 */
class cfs_category_checklist_walker extends Walker_Category_Checklist {
    public $field_name = '';

    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract($args);
        if ( empty($taxonomy) )
            $taxonomy = 'category';

        $name = $this->field_name;

        $class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
        $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
    }
}