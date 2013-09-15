<?php
	$user_packs = jr_get_user_packs( $user_ID );

	if ( $user_packs )
		$sub_title = __('Below you will find a list of active packs you have purchased.', APP_TD);
	else
		$sub_title = __('No active packs found.', APP_TD);
?>
<div id="packs" class="myjobs_section">

	<h2 class="pack_select dashboard"><?php _e( 'My Packs', APP_TD ); ?></h2><p><?php echo $sub_title; ?></p>

<?php

	### display user packs

	$display_options = array ( 
		'selectable' => 'no', 
	);

	$plans_data = jr_get_available_plans();

	jr_display_packs( 'user', $plans_data, $display_options );

	### display new packs

	if ( jr_allow_purchase_separate_packs() ):

		the_purchase_pack_link();

	endif;
?>
</div>