<?php
/**
 * JobRoller Filter Form
 * Function outputs the job filters
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_filter_form() {
	
	if (get_option('jr_show_filterbar')!=='no') : ?>
	<form class="filter" method="get" action="<?php echo jr_get_current_url(); ?>">
		<?php
		$job_types = get_terms( 'job_type', array( 'hide_empty' => '0' ) );
		if ($job_types && sizeof($job_types) > 0) {
			foreach ($job_types as $type) {
				?>
				<p><input type="checkbox" name="<?php echo $type->slug; ?>" id="<?php echo $type->slug; ?>" <?php 
					
					if (isset($_GET[$type->slug]) || !isset($_GET['action'])) echo 'checked="checked"'; 
					
				?> value="show" /> <label for="<?php echo $type->slug; ?>"><?php echo $type->name; ?></label></p>
				<?php
			}
		}
		?>
		<p>
		<input type="submit" value="<?php _e('Filter', APP_TD); ?>" class="submit" />
		<input type="hidden" name="action" value="Filter" />
		<?php
			// hidden fields for search
			if (isset($_GET['s'])) {
				echo '<input type="hidden" name="s" value="'.esc_attr($_GET['s']).'" />';
			}
			if (isset($_GET['location'])) {
				echo '<input type="hidden" name="location" value="'.esc_attr($_GET['location']).'" />';
			}
			if (isset($_GET['radius'])) {
				echo '<input type="hidden" name="radius" value="'.esc_attr($_GET['radius']).'" />';
			}
		?>
		</p>
		<div class="clear"></div>
	</form>
	<?php endif;
	
	return jr_process_filter_form();
}
