<?php
	// Template Name: Purchase Packs

	if ( empty($params) ) {
		 $params = array();
	}

	if ( ! empty($params['order_template']) ) {
		$template = $params['order_template'];
		$params['step'] = max(array_keys( _jr_select_plans_steps()) );
	} else {
		$template = '/includes/forms/lister-packs/lister-packs-form.php';
	}

	$default_params = array(
		'step'		=> jr_get_next_step( $start = 1 ),
		'order_id' 	=> get_query_var('order_id'),
	);
	$params = wp_parse_args( $params, $default_params );

	$step = $params['step'];
?>
	<div class="section">

		<div class="section_content">

			<h1><?php _e('Buy Plan', APP_TD); ?></h1>

			<?php do_action( 'appthemes_notices' ); ?>

			<?php 
				$steps = _jr_select_plans_steps();
				echo '<ol class="steps">';
				foreach ( $steps as $i => $value ) {
					echo '<li class="';
					if ($step==$i) { 
						echo 'current ';
					}
					if (($step-1)==$i) echo 'previous ';
					if ($i<$step) echo 'done';
					echo '"><span class="';
					if ($i==1) echo 'first';
					if ($i==count($steps)) echo 'last';
					echo '">';
					echo $value['description'];
					echo '</span></li>';
				}
				echo '</ol><div class="clear"></div>';
			?>

			<?php appthemes_load_template( $template, $params ); ?>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('submit'); ?>
