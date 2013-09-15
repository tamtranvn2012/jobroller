<?php

class JR_Pricing_General_Box extends APP_Meta_Box{

	public function __construct(){
		parent::__construct( 'pricing-details', __( 'Pricing Details' , APP_TD ), APPTHEMES_PRICE_PLAN_PTYPE, 'normal', 'default' );
	}
	
	public function before_form(){
		?><style type="text/css">#notice{ display: none; }</style><?php
		global $jr_options;

		if ( 'pack' == $jr_options->plan_type ) {
			echo html( 'span', array( 'class' => 'pack-fields' ) );
		}
	}

	public function form(){
		global $jr_options;

		$fields =  array(
			array(
				'title' => __( 'Plan Name', APP_TD ),
				'type' => 'text',
				'name' => 'title',
				'extra' => array(
					'tabindex' => 1,
				),
			),
			array(
				'title' => __( 'Description', APP_TD ),
				'type' => 'textarea',
				'name' => 'description',
				'extra' => array(
					'style' => 'width: 25em;',
					'tabindex' => 2,
				)
			),
			array(
				'title' => __( 'Price', APP_TD ),
				'type' => 'text',
				'name' => 'price',
				'desc' => sprintf( __( 'Example: %s ( 0 = Free )' , APP_TD ), '25' ),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 3,
				)
			),
			array(
				'title' => __( 'Relist Price', APP_TD ),
				'type' => 'text',
				'name' => 'relist_price',
				'desc' => sprintf( __( 'Example: %s ( 0 = Free Relisting )' , APP_TD ), '15' ),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 4,
				)
			),
			array(
				'title' => __( 'Job Duration', APP_TD ),
				'type' => 'text',
				'name' => 'duration',
				'desc' => __( 'day(s) ( 0 = Endless )', APP_TD),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 5,
				)
			),
			array(
				'title' => __( 'Usage Limit', APP_TD ),
				'type' => 'text',
				'name' => 'limit',
				'desc' => __( 'use(s). How many times can this Plan be selectable by the same user? ( 0 = Unlimited Uses )', APP_TD),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 6,
				)
			),
		);

		if ( ! jr_allow_relist() ) {
			unset( $fields[3] ); // remove allow relist option
		}

		if ( 'pack' == $jr_options->plan_type ) {

			$additional_pack_fields = array(
				array(
					'title' => __( 'Pack :: Job Count', APP_TD ),
					'type' => 'text',
					'name' => 'jobs_limit',
					'desc' => __( 'How many jobs can the user list with this Plan? ( 0 = Unlimited )' , APP_TD ),
					'extra' => array(
						'style' => 'width: 50px;',
						'tabindex' => 7,
					)
				),
				array(
					'title' => __( 'Pack :: Job Offers', APP_TD ),
					'type' => 'text',
					'name' => 'job_offers_limit',
					'desc' => __( 'Job offers are added to the jobs count ( Total Jobs = Job Count + Job Offers ) ( 0 = No Offers )', APP_TD ),
					'extra' => array(
						'style' => 'width: 50px;',
						'tabindex' => 8,
					)
				),
				array(
					'title' => __( 'Pack :: Duration', APP_TD ),
					'type' => 'text',
					'name' => 'pack_duration',
					'desc' => __( 'day(s). Days this Plan remains valid to use ( 0 = Endless ) ', APP_TD),
					'extra' => array(
						'style' => 'width: 50px;',
						'tabindex' => 9,
					)
				),
			);

			$fields = array_merge( $fields, $additional_pack_fields );
		}

		return _jr_prefix_fields( $fields );
		
	}

	public function validate_post_data( $data ){
		global $jr_options;

		$errors = new WP_Error();

		$fields = array( 
			array(
				'name' => 'title',
				'type' => 'text'
			),
			array(
				'name' => 'price',
				'type' => 'numeric'
			),
			array(
				'name' => 'duration',
				'type' => 'numeric'
			),
			array(
				'name' => 'limit',
				'type' => 'numeric'
			),
		);

		if ( 'pack' == $jr_options->plan_type ) {
			$pack_fields = array(
				array(
					'name' => 'jobs_limit',
					'type' => 'numeric'
				),
				array(
					'name' => 'pack_duration',
					'type' => 'numeric'
				),
				array(
					'name' => 'job_offers_limit',
					'type' => 'numeric'
				),
			);

			if ( jr_allow_relist() ) {
				$relist_field = array (
				 array(
					'name' => 'relist_price',
					'type' => 'numeric',
				) );
				$fields = array_merge( $fields, $relist_field );
			}

			$fields = array_merge( $fields, $pack_fields );
		}

		$fields = _jr_prefix_fields( $fields );

		foreach ( $fields as $field ) {

			$error = FALSE;
			if ( 'text' == $field['type'] ) {
				if( empty( $data[$field['name']] ) ) {
					$error = TRUE;
				}
			} else {
				if( !is_numeric( $data[$field['name']] ) ){
					$error = TRUE;
				}
			}
			if ( $error ) $errors->add( $field['name'], '' );

		}

		if( $data[JR_FIELD_PREFIX.'duration'] < 0 ){
			$errors->add ( 'duration', '' );
		}

		return $errors;

	}


	public function before_save( $data, $post_id ){
		$data[JR_FIELD_PREFIX.'duration'] = absint( $data[JR_FIELD_PREFIX.'duration'] );

		return $data;
	}

	public function post_updated_messages( $messages ) {
		$messages[ APPTHEMES_PRICE_PLAN_PTYPE ] = array(
		 	1 => __( 'Plan updated.', APP_TD ),
		 	4 => __( 'Plan updated.', APP_TD ),
		 	6 => __( 'Plan created.', APP_TD ),
		 	7 => __( 'Plan saved.', APP_TD ),
		 	9 => __( 'Plan scheduled.', APP_TD ),
			10 => __( 'Plan draft updated.', APP_TD ),
		);
		return $messages;
	}

}

class JR_Featured_Addon_Box extends APP_Meta_Box{

	public function __construct(){
		parent::__construct( 'pricing-addons', __( 'Featured Addons', APP_TD ), APPTHEMES_PRICE_PLAN_PTYPE, 'normal', 'default' );
	}

	public function form(){
		global $jr_options;

		$output = array();

		foreach( _jr_featured_addons() as $addon ){

			$enabled = array(
				'title' => APP_Item_Registry::get_title( $addon ),
				'type' => 'checkbox',
				'name' => $addon,
				'desc' => __( 'Included', APP_TD ),
				'extra' => array(
				),
			);

			$duration = array(
				'title' => __( 'Duration', APP_TD ),
				'type' => 'text',
				'name' => $addon . '_duration',
				'desc' => __( 'days', APP_TD ),
				'extra' => array(
					'size' => '3',
					'tabindex' => 15,
				),
			);

			$output[] = $enabled;
			$output[] = $duration;

			if ( 'pack' == $jr_options->plan_type ) {
				$uses = array(
					'title' => __( 'Limit', APP_TD ),
					'type' => 'text',
					'name' => $addon . '_limit',
					'desc' => __( 'use(s) ( 0 = Unlimited Uses )', APP_TD ),
					'extra' => array(
						'size' => '3',
						'tabindex' => 16,
					),
				);
				$output[] = $uses;
			}

		}

		return $output;
	
	}

	public function before_save( $data, $post_id ){

		foreach( _jr_featured_addons() as $addon ){

			if( !empty( $data[ $addon ] ) && empty( $data[ $addon . '_duration' ] ) ){
				$data[ $addon . '_duration' ] = get_post_meta( $post_id, JR_FIELD_PREFIX . 'duration', true );
			}

			$data[ $addon . '_duration' ] = absint( $data[ $addon . '_duration' ] );

		}

		return $data;
	}

	public function validate_post_data( $data, $post_id ){
		$errors = new WP_Error();

		$limits = 0;

		$jobs_limit = intval( get_post_meta( $post_id, JR_FIELD_PREFIX . 'jobs_limit', true ) );
		$jobs_limit += intval( get_post_meta( $post_id, JR_FIELD_PREFIX . 'job_offers_limit', true ) );
		$job_listing_duration = intval( get_post_meta( $post_id, JR_FIELD_PREFIX . 'duration', true ) );
		foreach( _jr_featured_addons() as $addon ){

			if ( !empty( $data[ $addon . '_duration' ] ) ){

				$addon_duration = $data[ $addon . '_duration' ];
				if( !is_numeric( $addon_duration ) )
					$errors->add( $addon . '_duration', '' );

				if( intval( $addon_duration ) > $job_listing_duration && $job_listing_duration != 0 )
					$errors->add( $addon . '_duration', '' );

				if( intval( $addon_duration ) < 0 )
					$errors->add( $addon . '_duration', '' );

			}

			if ( ! empty( $data[ $addon . '_limit' ] ) ) {
				if ( $data[ $addon . '_limit' ] > $jobs_limit )
					$errors->add( $addon . '_limit', '' );
			}

		}



		return $errors;
	}

	public function before_form(){
		echo html( 'p', array(), __( 'You can include featured addons in a plan. These options will be selectable free of charge by the user before purchase. After they run out, the customer can then purchase regular featured addons.', APP_TD ) );
	}


	public function after_form(){
		global $jr_options;

		echo html( 'p', array('class' => 'howto'), __( 'Durations must be shorter or equal than the Job duration.', APP_TD ) );
		if ( 'pack' == $jr_options->plan_type ) {
			echo html( 'p', array('class' => 'howto'), __( 'Limits must be lower than the job count + job offers sum.', APP_TD ) );
		}
	}
	
}

class JR_Resumes_Pricing_General_Box extends APP_Meta_Box{

	public function __construct(){
		parent::__construct( 'resumes-pricing-details', __( 'Resume Plan Details' , APP_TD ), APPTHEMES_RESUMES_PLAN_PTYPE, 'normal', 'default' );
	}

	public function before_form(){
		?><style type="text/css">#notice{ display: none; }</style><?php
	}

	public function form(){

		$fields =  array(
			array(
				'title' => __( 'Name', APP_TD ),
				'type' => 'text',
				'name' => 'title',
				'extra' => array(
					'tabindex' => 1,
				),
			),
			array(
				'title' => __( 'Description', APP_TD ),
				'type' => 'textarea',
				'name' => 'description',
				'extra' => array(
					'style' => 'width: 25em;',
					'tabindex' => 2,
				)
			),
			array(
				'title' => __( 'Trial', APP_TD ),
				'type' => 'checkbox',
				'name' => 'trial',
				'desc' => __( 'Allow Job Seeker\'s to Browse/View Resumes for a limited period of time' , APP_TD ),
				'extra' => array(
					'tabindex' => 3,
				),
			),
			array(
				'title' => __( 'Price', APP_TD ),
				'type' => 'text',
				'name' => 'price',
				'desc' => sprintf( __( 'Example: %s ( 0 = Free ) ' , APP_TD ), '15' ),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 4,
				)
			),
			array(
				'title' => __( 'Recurs Every *', APP_TD ),
				'type' => 'text',
				'name' => 'duration',
				'desc' => __( 'day(s). The subscription duration' , APP_TD ),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 5,
				)
			),
			array(
				'title' => __( 'Usage Limit', APP_TD ),
				'type' => 'text',
				'name' => 'limit',
				'desc' => __( 'use(s). How many times can this Plan be selectable by the same user? ( 0 = Unlimited Uses )', APP_TD),
				'extra' => array(
					'style' => 'width: 50px;',
					'tabindex' => 6,
				)
			),
			array(
				'title' => '',
				'type' => 'hidden',
				'name' => 'recurring',
			),
		);

		return _jr_prefix_fields( $fields );
	}

	public function validate_post_data( $data ){
		global $jr_options;

		$errors = new WP_Error();

		$fields = array( 
			array(
				'name' => 'title',
				'type' => 'text'
			),
			array(
				'name' => 'price',
				'type' => 'numeric'
			),
			array(
				'name' => 'duration',
				'type' => 'numeric'
			),
			array(
				'name' => 'limit',
				'type' => 'numeric'
			),
		);

		$fields = _jr_prefix_fields( $fields );

		foreach ( $fields as $field ) {

			$error = FALSE;
			if ( 'text' == $field['type'] ) {
				if( empty( $data[$field['name']] ) ) {
					$error = TRUE;
				}
			} else {
			
				if( !is_numeric( $data[$field['name']] ) ){
					$error = TRUE;
				}
			}
			if ( $error ) $errors->add( $field['name'], '' );
		}

		if( $data[JR_FIELD_PREFIX.'duration'] < 0 ){
			$errors->add ( 'duration', '' );
		}

		return $errors;

	}

	public function after_form( $post ) { 
		echo html( 'p', __( '(*) Please note that auto recurring payments may not be available to all gateways. Subscriptions will default to one-off manual payments when auto-recurring is not available.', APP_TD ) );
	}

	public function before_save( $data, $post_id ){
		if ( ! $data[JR_FIELD_PREFIX.'trial'] ) {
			$data[JR_FIELD_PREFIX.'recurring'] = 1;
		}
		$data[JR_FIELD_PREFIX.'duration'] = absint( $data[JR_FIELD_PREFIX.'duration'] );

		return $data;
	}

	public function post_updated_messages( $messages ) {
		$messages[ APPTHEMES_PRICE_PLAN_PTYPE ] = array(
		 	1 => __( 'Plan updated.', APP_TD ),
		 	4 => __( 'Plan updated.', APP_TD ),
		 	6 => __( 'Plan created.', APP_TD ),
		 	7 => __( 'Plan saved.', APP_TD ),
		 	9 => __( 'Plan scheduled.', APP_TD ),
			10 => __( 'Plan draft updated.', APP_TD ),
		);
		return $messages;
	}

}

class JR_Resumes_Addon_Box extends APP_Meta_Box{

	public function __construct(){
		parent::__construct( 'resumes-addons', __( 'Resumes Addons', APP_TD ), APPTHEMES_PRICE_PLAN_PTYPE, 'normal', 'default' );
	}

	public function condition() {
		return ( 'yes' == get_option( 'jr_resume_require_subscription' ) );
	}

	public function form(){

		$output = array();

		foreach( (array) _jr_resumes_addons() as $addon ){

			$enabled = array(
				'title' => APP_Item_Registry::get_title( $addon ),
				'type' => 'checkbox',
				'name' => $addon,
				'desc' => __( 'Included', APP_TD ),
			);

			$duration = array(
				'title' => __( 'Duration', APP_TD ),
				'type' => 'text',
				'name' => $addon . '_duration',
				'desc' => __( 'days', APP_TD ),
				'extra' => array(
					'size' => '3',
					'tabindex' => 20,
				),
			);

			$output[] = $enabled;
			$output[] = $duration;

		}

		return $output;
	
	}

	public function before_save( $data, $post_id ){

		foreach( (array)_jr_resumes_addons() as $addon ){

			if( !empty( $data[ $addon ] ) && empty( $data[ $addon . '_duration' ] ) ){
				$data[ $addon . '_duration' ] = get_post_meta( $post_id, JR_FIELD_PREFIX . 'duration', true );
			}

			$data[ $addon . '_duration' ] = absint( $data[ $addon . '_duration' ] );

		}

		return $data;
	}

	public function validate_post_data( $data, $post_id ){
		$errors = new WP_Error();

		$pack_duration = intval( get_post_meta( $post_id, JR_FIELD_PREFIX . 'pack_duration', true ) );
		foreach( (array)_jr_resumes_addons() as $addon ){

			if( !empty( $data[ $addon . '_duration' ] ) ){

				$addon_duration = $data[ $addon . '_duration' ];
				if( !is_numeric( $addon_duration ) )
					$errors->add( $addon . '_duration', '' );

				if( intval( $addon_duration ) < 0 )
					$errors->add( $addon . '_duration', '' );

			}

		}

		return $errors;
	}

	public function before_form(){
		echo html( 'p', array(), __( 'You can include view/browse resumes access addons in a plan. These will be immediately assigned to the user upon purchase. After they run out, the customer can then purchase regular view/browse resumes access addons or subscribe to resumes access, separately.', APP_TD ) );
	}


	public function after_form(){
		echo html( 'p', array('class' => 'howto'), __( 'Access will still be determined by your visibility settings. e.g. if you set the resume visibility for \'Recruiters\', only Recruiters will have the resumes addons included on the plan.', APP_TD ) );
	}
	
}
