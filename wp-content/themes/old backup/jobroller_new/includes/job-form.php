<?php

add_action( 'wp_loaded', 'jr_handle_job_submit_form' );
add_action( 'wp_loaded', 'jr_handle_job_confirmation' );

add_action( 'jr_listing_validate_fields', 'jr_validate_listing_category', 10 );
add_action( 'jr_listing_validate_fields', 'jr_validate_listing_fields', 11 );

add_action( 'jr_handle_listing_fields', 'jr_maybe_strip_tags', 10, 3 );
add_action( 'jr_handle_listing_fields', 'jr_format_contact_fields', 11, 3 );

add_filter( 'jr_handle_update_listing', 'jr_validate_update_listing' );

// handle free jobs - update the job status after user confirmation
function jr_handle_job_confirmation() {

	if ( empty($_POST['job_confirm']) )
		return; 

	if ( ! $job_id = intval($_POST['ID']) ) {
		$errors = jr_get_listing_error_obj();
		$errors->add( 'submit_error', __( '<strong>ERROR</strong>: Cannot update job status. Job ID not found.', APP_TD ) );
		return;
	}
	jr_update_post_status( $job_id );

	_jr_set_job_duration( $job_id );

	do_action( 'jr_activate_job', $job_id );

	wp_redirect( get_permalink( $job_id ) );
	exit();
}

// handle the main job submit form
function jr_handle_job_submit_form() {

	if ( ! isset($_POST['job_submit']) )
		return;

	$actions = array( 'edit-job', 'new-job', 'relist-job' );
	if ( empty($_POST['action']) || !in_array( $_POST['action'], $actions ) )
			return;

	if ( !current_user_can( 'can_submit_job' ) )
		return;

	$job = jr_handle_update_job_listing();
	if ( ! $job ) {
		// there are errors, return to current page
		return;
	}

	if ( 'edit-job' == $_POST['action'] ) {

		// maybe update job status
		if( _jr_edited_job_requires_moderation( $job ) ) {
			jr_update_post_status( $job->ID, 'pending' );

			// send notification email
			jr_edited_job_pending( $job->ID );
		}

		wp_redirect( add_query_arg( 'update_success', '1', get_permalink( $job->ID ) ) );
		exit();
	}

	$args = array( 
		'job_id' => $job->ID, 
		'step ' => jr_get_next_step()
	);

	if ( !empty($_POST['relist']) ) {
		$args['job_relist'] = $job->ID;
	}

	if ( !empty($_POST['order_id']) ) {
		$args['order_id'] = intval($_POST['order_id']);
	}

	// redirect to next step
	wp_redirect( add_query_arg( $args, jr_get_listing_create_url() ) );
	exit();

}

// creates/updates the job listing and all it's meta and terms
function jr_handle_update_job_listing() {

	$job_cat = jr_get_listing_tax( 'job_term_cat', APP_TAX_CAT );
	$job_type = jr_get_listing_tax( 'job_term_type', APP_TAX_TYPE );
	$job_salary = jr_get_listing_tax( 'job_term_salary', APP_TAX_SALARY );

	$args = wp_array_slice_assoc( $_POST, array( 'ID', 'post_title', 'post_content', 'tax_input' ) );

	$args['post_content'] = jr_maybe_strip_tags( $args['post_content'], 'post_content' );
	$args['post_type'] = APP_POST_TYPE;

	$errors = apply_filters( 'jr_listing_validate_fields', jr_get_listing_error_obj() );
	if( $errors->get_error_codes() ){
		return false;
	}

	if ( isset($_POST['ID']) ) {
		$job_id = intval($_POST['ID']);
		$job = get_post( $job_id );
	}

	if ( empty($job) ) {
		$action = 'insert';
	} elseif( isset($_POST['relist']) ) {
		$action = 'relist';
	} else {
		$action = 'update';
	}

	// do_action hook
	jr_before_insert_job( $action );

	if ( empty($job) ) {
		$job_id = wp_insert_post( $args );
	} else {

		if ( 'expired' == $job->post_status ) 
			$args['post_status'] = 'draft';

		$job_id = wp_update_post( $args );
	}

	### TERMS

	wp_set_object_terms( $job_id, (int) $job_type, APP_TAX_TYPE );
	wp_set_object_terms( $job_id, (int) $job_cat, APP_TAX_CAT );
	wp_set_object_terms( $job_id, (int) $job_salary, APP_TAX_SALARY );

	$tags = jr_get_listing_tags( $args['tax_input'][APP_TAX_TAG] );
	wp_set_object_terms( $job_id, $tags, APP_TAX_TAG );

	### META

	foreach ( jr_get_job_listing_fields() as $field => $meta_name ) {
		$field_value = apply_filters('jr_handle_listing_fields', _jr_get_initial_field_value( $field ), $field, $job_id );
		update_post_meta( $job_id, $meta_name, $field_value );
	}

	jr_set_coordinates( $job_id );

	### CUSTOM FIELDS

	jr_update_form_builder( $job_cat, $job_id );

	jr_handle_company_logo( $job_id );

	jr_handle_files( $job_id, $job_cat );

	// do_action hook
	jr_after_insert_job( $job_id, $action );

	return apply_filters( 'jr_handle_update_listing', get_post( $job_id ) );
}


// set the job listing geo coordinates meta
function jr_set_coordinates( $job_id ) {

	$data = array();
	foreach ( jr_get_geo_fields() as $field => $meta_name ) {
		$data[$field] = _jr_get_initial_field_value( $field );
	}

	if ( empty($data['jr_address']) )
		return;

	if ( ! empty($data['jr_geo_latitude'])&& ! empty($data['jr_geo_longitude']) ) {

		$latitude = jr_clean_coordinate( $data['jr_geo_latitude'] );
		$longitude = jr_clean_coordinate( $data['jr_geo_longitude'] );

		update_post_meta( $job_id, '_jr_address', $data['jr_address'] );

		update_post_meta( $job_id, '_jr_geo_latitude', $data['jr_geo_latitude'] );
		update_post_meta( $job_id, '_jr_geo_longitude', $data['jr_geo_longitude'] );

		if ( $latitude && $longitude ) {
	
			// If we don't have address data, do a look-up
			if ( $data['jr_geo_short_address'] && $data['jr_geo_country'] && $data['jr_geo_short_address'] && $data['jr_geo_short_address_country'] ) {
				update_post_meta( $job_id, 'geo_address', $data['jr_geo_short_address'] );
				update_post_meta( $job_id, 'geo_country', $data['jr_geo_country'] );
				update_post_meta( $job_id, 'geo_short_address', $data['jr_geo_short_address'] );
				update_post_meta( $job_id, 'geo_short_address_country', $data['jr_geo_short_address_country'] );
			} else {
				$address = jr_reverse_geocode( $latitude, $longitude );
				update_post_meta( $job_id, 'geo_address', $address['address'] );
				update_post_meta( $job_id, 'geo_country', $address['country'] );
				update_post_meta( $job_id, 'geo_short_address', $address['short_address'] );
				update_post_meta( $job_id, 'geo_short_address_country', $address['short_address_country'] );
			};

		}

	};
}

// skip strips tags for fields where HTML is allowed
function jr_maybe_strip_tags( $field_value, $field, $job_id = 0 ) {

	if ( ( 'apply' == $field || 'post_content' == $field ) && 'yes' == get_option('jr_html_allowed') ) {
			return $field_value;
	}
	return strip_tags( $field_value );
}

// add special formatting to specific fields
function jr_format_contact_fields( $field_value, $field, $job_id ){

	if( 'website' == $field ) {
		$field_value = str_ireplace('http://', '', $field_value);
	}
	return $field_value;
}

// default values when post data does not exist
function _jr_get_initial_field_value( $field ) {
	return isset( $_POST[$field] ) ? stripslashes( $_POST[$field] ) : '';
}

// retrieve the available fields (meta) for a job listing - array( 'field_name' => 'meta_name' )
function jr_get_job_listing_fields() {
	$fields = array(
		'your_name' => '_Company',
		'website' 	=> '_CompanyURL',
		'apply'		=> '_how_to_apply',
	);
	return apply_filters( 'jr_job_fields', $fields, $_POST );
}

// retrieve the available geolocation fields (meta) for a job listing - array( 'field_name' => 'meta_name' )
function jr_get_geo_fields() {
	$fields = array(
		'jr_address' 					=> '_jr_address',
		'jr_geo_latitude' 				=> '_jr_geo_latitude',
		'jr_geo_longitude' 				=> '_jr_geo_longitude',
		'jr_geo_country'				=> 'geo_country',
		'jr_geo_short_address' 			=> 'geo_short_address',
		'jr_geo_short_address_country' 	=> 'geo_short_address_country',
	);
	return $fields;
}

// retrieve the tags for a job listing
function jr_get_listing_tags( $tags_string ) {
	$trim_strings = explode( ',', $tags_string );
	return array_map( 'trim', $trim_strings );
}

// retrieve the term id for a specific taxonomy
function jr_get_listing_tax( $name, $taxonomy ) {

	if ( isset( $_REQUEST[$name] ) && $_REQUEST[$name] != -1 ) {
		$listing_tax = get_term( $_REQUEST[$name], $taxonomy );
		$term_id = is_wp_error( $listing_tax ) ? false : $listing_tax->term_id;
	} else {
		$term_id = false;
	}

	return $term_id;
}

// validate the job listing fields
function jr_validate_listing_fields( $errors ) {
	
	$fields = wp_array_slice_assoc( $_POST, array( 'post_title', 'post_content', 'tax_input' ) );
	$fields = apply_filters( 'jr_job_required_fields', $fields );
	foreach ( $fields as $key => $name ) {
		if ( empty($_POST[$key]) ) {
			$errors->add( 'submit_error', __('<strong>ERROR</strong>: Please fill in all required fields.', APP_TD) );
		}
	}
	return $errors;

}

// validate the job listing category
function jr_validate_listing_category( $errors ){

	if ( 'yes' != get_option('jr_submit_cat_required') )
		return $errors;

	$listing_cat = jr_get_listing_tax( 'job_term_cat', APP_TAX_CAT );
	if ( !$listing_cat ) 
		$errors->add( 'wrong-cat', __( 'No category was submitted.', APP_TD ) );
	
	return $errors;

}


// validates the listing data and returns the post if there are no errors. In case of errors, returns false
function jr_validate_update_listing( $listing ) {

	$errors = jr_get_listing_error_obj();
	if ( $errors->get_error_codes() ) {
		return false;
	}
	return $listing;
}

// update the custom form fields
function jr_update_form_builder( $job_cat, $job_id ) {
	$fields = jr_get_fields_for_cat( $job_cat );

	$to_update = scbForms::validate_post_data( $fields );

	scbForms::update_meta( $fields, $to_update, $job_id );
}

// retrieve the job listing tags
function the_job_listing_tags_to_edit( $listing_id ) {
	$tags = get_the_terms( $listing_id, APP_TAX_TAG );

	if ( empty( $tags ) )
		return;

	echo esc_attr( implode( ', ', wp_list_pluck( $tags, 'name' ) ) );
}

// retrieve the job required fields
function jr_job_required_fields() {

	// Check required fields
	$required = array(
		'job_title' 	=> __('Job title', APP_TD),
		'job_term_type' => __('Job type', APP_TD),
		'details' 		=> __('Job description', APP_TD),
	);

	return apply_filters( 'jr_job_required_fields', $required );
}

function _jr_needs_purchase( $job = '' ){
	return ( jr_charge_job_listings() );
}

function jr_get_default_job_to_edit() {

	$all_meta_fields = array_merge( jr_get_job_listing_fields(), jr_get_geo_fields() );

	if ( $job_id = get_query_var('job_id') ) {
		$job = get_post($job_id);

		$job_cat_tax =  jr_get_the_job_tax( $job->ID, APP_TAX_CAT );
		
		if ( $job_cat_tax ) $job->category = $job_cat_tax->term_id;
		$job->type = jr_get_the_job_tax( $job->ID, APP_TAX_TYPE )->term_id;
		if ( $job->salary = jr_get_the_job_tax( $job->ID, APP_TAX_SALARY ) ) {
			$job->salary = jr_get_the_job_tax( $job->ID, APP_TAX_SALARY )->term_id;
		}

		foreach ( $all_meta_fields as $field => $meta_name ) {
			$job->$field = get_post_meta( $job->ID, $meta_name, true );
		}

	} else {

		require ABSPATH . '/wp-admin/includes/post.php';
		$job = get_default_post_to_edit( APP_POST_TYPE );

		$job->category = jr_get_listing_tax( 'job_term_cat', APP_TAX_CAT );
		$job->type = jr_get_listing_tax( 'job_term_type', APP_TAX_TYPE );
		$job->salary = jr_get_listing_tax( 'job_term_salary', APP_TAX_SALARY );

		foreach ( array( 'post_title', 'post_content' ) as $field ) {
			$job->$field = _jr_get_initial_field_value( $field );
		}

		foreach ( $all_meta_fields as $field => $meta_name ) {
			$job->$field = _jr_get_initial_field_value( $field );
		}
	}

	return $job;

}

function jr_get_listing_cat_id() {
	static $cat_id;

	if ( is_null( $cat_id ) ) {
		if ( isset( $_REQUEST['_'.APP_TAX_CAT] ) && $_REQUEST['_'.APP_TAX_CAT] != -1 ) {
			$listing_cat = get_term( $_REQUEST['_'.APP_TAX_CAT], APP_TAX_CAT );
			$cat_id = is_wp_error( $listing_cat ) ? false : $listing_cat->term_id;
		} else {
			$cat_id = false;
		}
	}

	return $cat_id;
}

function jr_job_details( $job_id = 0 ) {
	$job_id = $job_id ? $job_id : get_the_ID();

	$job_details = get_post( $job_id );
	$meta = get_post_custom( $job_id );
	$data = jr_reset_data( $meta );

	if ( ! $job_details ) 
		return;

	$category = '';

	$cat_terms = get_the_job_terms( $job_details->ID, APP_TAX_CAT );
	if ( $cat_terms ) 
		$category = get_the_job_terms( $job_details->ID, APP_TAX_CAT )->term_id;

	$salary = '';

	$salary_terms = get_the_job_terms( $job_details->ID, APP_TAX_SALARY );
	if ( $salary_terms ) 
		$salary = get_the_job_terms( $job_details->ID, APP_TAX_SALARY )->term_id;

	$tags = '';

	$tags_terms = get_the_terms( $job_details->ID, APP_TAX_TAG );
	if ( $tags_terms ) {
		foreach ($tags_terms as $term) :
			$job_tags[] = $term->name;
		endforeach;
		$tags = implode(', ', $job_tags ); 
	} 

	$details = array( 
		'your_name' => $data['_Company'],
		'website' => $data['_CompanyURL'],
		'job_title' => $job_details->post_title,
		'job_term_type' => get_the_job_terms( $job_details->ID, APP_TAX_TYPE )->slug,
		'job_term_cat' => $category,
		'job_term_salary' => $salary,
		'jr_address' =>  ( !empty($data['geo_address']) ? $data['geo_address'] : '' ),
		'jr_geo_latitude' => ( !empty($data['_jr_geo_latitude']) ? $data['_jr_geo_latitude'] : '' ),
		'jr_geo_longitude' => ( !empty($data['_jr_geo_longitude']) ? $data['_jr_geo_longitude'] : '' ),
		'details' => $job_details->post_content,
		'apply' => $data['_how_to_apply'],
		'tags' => $tags,
	);
	return apply_filters( 'jr_job_details', $details );
}

// get the last step
function _jr_steps_get_last( $steps = '' ) {
	if ( ! $steps  ) 
		$steps = jr_steps();
	return max( array_keys( $steps ) );
}

// steps descriptions and templates
function _jr_job_submit_steps() {

	$steps = array(
		1 => array (
			'name'	=> 'register',
			'description' => __('Create account', APP_TD),
			'template' => '',
		),
		2 => array (
			'name'	=> 'submit_job',
			'description' => __('Enter Job Details', APP_TD),
			'template' => '/includes/forms/submit-job/submit-job-form.php',
		),
		3 => array (
			'name'	=> 'preview_job',
			'description' => __('Preview', APP_TD),
			'template' => '/includes/forms/preview-job/preview-job-form.php',
		),
	);

	return $steps;
}

// steps descriptions and templates
function jr_steps() {
	$steps = _jr_job_submit_steps();

	if ( jr_charge_job_listings() ) {
		$steps[] = _jr_select_job_plan_step();
		$description = __('Pay/Thank You', APP_TD);
	} else {
		$description = __('Confirm', APP_TD);
	}

	$steps[] = _jr_confirm_step( $description );

	return apply_filters( 'jr_job_submit_steps', $steps );
}

function jr_get_step_by_name( $name ) {
	foreach( jr_steps() as $key => $step ) {
		if ( $name == $step['name'] )
			return $key;
	}
	return false;
}

function _jr_select_plans_steps() {
	$steps = array(
		1 => array (
			'name'	=> 'select_plan',
			'description' => __('Select Plan', APP_TD),
		),
		2  => array (
			'name'	=> 'select_gateway',
			'description' => __('Pay/Thank You', APP_TD),
		),
	);
	return $steps;
}

function _jr_select_job_plan_step() {
	$step = array (
		'name'	=> 'select_plan',
		'description' => __('Select Plan', APP_TD),
		'template' => '/includes/forms/select-plan/select-plan.php',
	);
	return $step;
}

function _jr_confirm_step( $description ) {

	$step = array (
		'name'	=> 'confirm_job',
		'description' => $description,
		'template' => '/includes/forms/confirm-job/confirm-job-form.php',
	);
	return $step;
}

function _jr_curr_step( $start ) {
	if ( get_query_var('step') ) {
		return get_query_var('step');
	} else {
		return $start;
	}
}

function jr_get_next_step( $start = 2 ) {
	if ( ! is_user_logged_in() )
		$step = 1;
	else
		$step =  _jr_next_step( jr_get_listing_error_obj(), $start );

	return $step;
}

// dinamically return the next step
function _jr_next_step( $errors, $start ) {

	$previous_step = _jr_curr_step( $start );

	$step = $previous_step;

	if ( ! empty($_POST) && ! $errors->get_error_codes() ) {
		if ( empty($_POST['goback']) )
			$step++;
		else
			$step = $start;
	} elseif ( $errors->get_error_codes() ) {
		$step = _jr_curr_step( $start );
	}

	if ( $step > _jr_steps_get_last() ) {
		$step = $previous_step;
	}

	return apply_filters( 'jr_next_job_submit_step', $step, $previous_step );
}
