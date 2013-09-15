<?php
	$args = array();

	// only allow purchasing paid job packs from the dashboard
	if ( is_page(JR_Packs_Purchase_Page::get_id()) ) {
		$args['meta_query'] = array( 
			array(
				'key' 		=> JR_FIELD_PREFIX.'price',
				'value' 	=> 0,
				'compare' 	=> '>=',
			),
		);
	}

	$plans = jr_get_available_plans( $args );
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e( 'Select a Plan', APP_TD ); ?></h1>
	</div>
	<form id="purchase-plan" class="submit_form main_form" method="POST">
	<?php wp_nonce_field('purchase_plan', 'nonce') ?>
		<fieldset>
			<div class="pricing-options">
				<?php if( !empty( $plans ) ) {

 						$display_options = array ( 
							'order'		 => 'yes', 
							'selectable' => 'yes',
						);

						jr_display_packs( 'new', $plans, $display_options, $default = 1 );

					} else { ?>
						<em><?php _e( 'No Plans are currently available. Please come back later.', APP_TD ); ?></em>
				<?php } ?>
			</div>
		</fieldset>

		<?php do_action( 'jr_after_purchase_plan_new_form' ); ?>
		<?php do_action( 'appthemes_purchase_fields' ); ?>

		<?php if( ! empty( $plans ) ) { ?>
		<p>
			<input type="hidden" name="action" value="purchase-separate-plan">
			<input type="hidden" name="order_id" value="<?php echo esc_attr( $order_id ); ?>">
			<input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>"/>
			<input type="submit" class="submit" value="<?php _e( 'Continue', APP_TD ) ?>" />
		</p>
		<?php } ?>
	</form>
</div>