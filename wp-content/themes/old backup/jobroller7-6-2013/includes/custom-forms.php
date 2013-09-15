<?php

add_action( 'init', 'jr_forms_register_post_type', 11 );
add_action( 'wp_ajax_app-render-job-form', 'jr_forms_ajax_render_form' );
add_action( 'jr_after_submit_job_form_category', 'jr_job_cat_custom_fields' );

add_filter( 'jr_submit_job_fields', 'jr_custom_fields', 10, 2 );

// make sure to include the custom fields for sanitizing later
function jr_custom_fields( $fields, $posted ) {

	if ( ! $job_term_cat = $posted['job_term_cat'] )
		return;

	foreach ( jr_get_fields_for_cat( $job_term_cat ) as $field ) {
		$fields[] = $field['name'];
	}
	return $fields;
}

function jr_job_cat_custom_fields( $job ) {
	appthemes_load_template( 'includes/job-form-custom-fields.php', array( 'job' => $job ) );
}

function jr_forms_register_post_type() {
	register_taxonomy_for_object_type( APP_TAX_CAT, APP_FORMS_PTYPE );
}

function jr_forms_ajax_render_form() {
	if ( ! $cat = $_POST['job_category'] )
		die;

	jr_render_job_form( $cat );
	die;
}

function jr_render_job_form( $cat, $job_id = 0 ) {

	foreach ( jr_get_fields_for_cat( $cat ) as $field ) {

		if ( ! isset($field['extra']['class']) )
			$field['extra']['class'] = '';

 		if ( ! in_array( $field['type'], array( 'checkbox', 'radio' ) ) ) {
			$field['extra']['class'] .= ' text';
		}

		$html = jr_wrap_custom_fields( $field, $job_id );

		echo apply_filters( 'jr_render_form_field', $html, $field, $job_id, $cat );
	}

}

function jr_wrap_custom_fields( $field, $job_id ) {
	$label_tmp = $field['desc'];
	$field['desc'] = '';

	if ( isset($field['extra']['class']) && strpos( $field['extra']['class'], 'required' ) !== FALSE ) {
		$label_tmp .= ' *';
	}
	$label = html( 'label', $label_tmp );

	if ( empty( $_POST[$field['name']] ) )
		$field_html = scbForms::input_from_meta( $field, $job_id );
	else
		$field_html = scbForms::input_with_value( $field, $_POST[$field['name']] );

	// hack to allow checkboxes and radio buttons to be set as required
	if ( in_array( $field['type'], array('checkbox', 'radio') ) && isset($field['extra']['class']) ) {
		$field_html = str_replace( '<input', '<input class="' . $field['extra']['class'] . '"', $field_html );
	}

	$field_html = str_replace( '<label>', '', $field_html );
	$field_html = str_replace( '</label>', '', $field_html );
	$field['desc'] = $label_tmp;

	return html( 'p', $label . $field_html );
}

function jr_get_fields_for_cat( $cat ) {
	$form = get_posts(
		array(
			'fields' => 'ids',
			'post_type' => APP_FORMS_PTYPE,
			'tax_query' => array(
				array(
					'taxonomy' => APP_TAX_CAT,
					'terms' => $cat,
					'field' => 'term_id',
					'include_children' => false
				)
			),
			'post_status' => 'publish',
			'numberposts' => 1
		)
	);

	if ( empty( $form ) )
		return array();

	return APP_Form_Builder::get_fields( $form[0] );
}
