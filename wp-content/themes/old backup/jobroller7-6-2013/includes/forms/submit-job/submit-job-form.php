<?php
/**
 * JobRoller Submit/Edit/Relist Job form
 * Function outputs the job submit form
 *
 *
 * @version 1.7
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */
	jr_geolocation_scripts( $job );
?>

	<form action="<?php echo esc_url( $form_action ) ?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">
		<?php wp_nonce_field('submit_job', 'nonce') ?>
		<fieldset>
			<legend><?php _e('Company Details', APP_TD); ?></legend>
			<p><?php _e('Fill in the company section to provide details of the company listing the job. Leave this section blank to show your display name and profile page link instead.', APP_TD); ?></p>
			<p class="optional"><label for="your_name"><?php _e('Your Name/Company Name', APP_TD); ?></label> <input type="text" class="text" name="your_name" id="your_name" value="<?php echo esc_attr( $job->your_name); ?>" /></p>
			<p class="optional"><label for="website"><?php _e('Website', APP_TD); ?></label> <input type="text" class="text" name="website" value="<?php echo esc_attr( $job->website); ?>" placeholder="http://" id="website" /></p>
			<?php the_listing_logo_editor( $job->ID ); ?>
		</fieldset>	
		<fieldset>
			<legend><?php _e('Job Details', APP_TD); ?></legend>
			<p><?php _e('Enter details about the job below. Be as descriptive as possible so that potential candidates can find your job listing easily.', APP_TD); ?></p>
			<p><label for="post_title"><?php _e('Job title', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text required" name="post_title" id="post_title" value="<?php echo esc_attr( $job->post_title ); ?>" /></p>
			<p><label for="job_type"><?php _e('Job type', APP_TD); ?> <span title="required">*</span></label> 
			<select name="job_term_type" id="job_type" class="required">
				<?php
				$job_types = get_terms( 'job_type', array( 'hide_empty' => false ) );
				if ($job_types && sizeof($job_types) > 0) {
					foreach ($job_types as $type) {
						?>
						<option <?php if ( $job->type==$type->term_id ) echo 'selected="selected"'; ?> value="<?php echo $type->term_id; ?>"><?php echo $type->name; ?></option>
						<?php
					}
				}
				?>
			</select></p>
			<?php 
				$cat_required = '';
				if ( 'yes' == get_option('jr_submit_cat_required') )
					$cat_required = 'required';
			?>
			<p class="<?php if ( ! $cat_required ) : echo 'optional'; endif; ?>"><label for="job_cat"><?php _e('Job Category', APP_TD); ?> <?php if ( $cat_required ) : ?><span title="required">*</span><?php endif; ?></label> <?php
				$args = array(
					'taxonomy'			=> APP_TAX_CAT,
					'orderby'			=> 'name', 
					'order'				=> 'ASC',
					'name'				=> 'job_term_cat',
					'class'				=> 'job_cat ' . $cat_required,
					'selected' 			=> $job->category,
					'hide_empty'		=> false,
					'hierarchical'		=> true,
					'show_option_none' 	=> __( 'Select a category&hellip;', APP_TD ),
					'echo'				=> false,
				);
				$drop_cats = wp_dropdown_categories( $args );
				if ( $cat_required && get_query_var('job_edit') && !empty($job->category) && 'no' == get_option( 'jr_submit_cat_editable' ) ) {
					$drop_cats = str_replace( '<select', '<select disabled', $drop_cats );
					$display_no_edit_cat_msg = __( 'The category cannot be edited', APP_TD );
					echo "<input type='hidden' name='job_term_cat' value='".esc_attr($job->category)."'>";
				}
				echo $drop_cats;

				if ( ! empty($display_no_edit_cat_msg) ) {
					echo html( 'p', html( 'strong', __( 'Note: ', APP_TD ) ) . $display_no_edit_cat_msg );
				}
			?></p>

			<?php do_action( 'jr_after_submit_job_form_category', $job ); ?>

			<?php if (get_option('jr_enable_salary_field')!=='no') : ?><p class="optional"><label for="job_term_salary"><?php _e('Job Salary', APP_TD); ?></label> <?php
				$args = array(
				    'orderby'            => 'ID', 
				    'order'              => 'ASC',
				    'name'               => 'job_term_salary',
				    'hierarchical'       => false, 
				    'echo'				 => false,
				    'class'              => 'job_salary',
				    'selected'			 => $job->salary,
				    'taxonomy'			 => 'job_salary',
				    'hide_empty'		 => false
				);
				$dropdown = wp_dropdown_categories( $args );
				$dropdown = str_replace('class=\'job_salary\' >','class=\'job_salary\' ><option value="">'.__('Select a salary&hellip;', APP_TD).'</option>', $dropdown);
				echo $dropdown;
			?></p><?php endif; ?>
			<p class="optional"><label for="tax_input[<?php echo APP_TAX_TAG; ?>]"><?php _e('Tags (comma separated)', APP_TD); ?></label> <input type="text" class="text" name="tax_input[<?php echo APP_TAX_TAG; ?>]" value="<?php the_job_listing_tags_to_edit( $job->ID ); ?>" id="tax_input[<?php echo APP_TAX_TAG; ?>]" /></p>
		</fieldset>

		<fieldset>
			<legend><?php _e('Job Location', APP_TD); ?></legend>
			<p><?php _e('Leave blank if the location of the applicant does not matter e.g. the job involves working from home.', APP_TD); ?></p>	
			<div id="geolocation_box">
			
				<p>
					<label>
						<input id="geolocation-load" type="button" class="button geolocationadd submit" value="<?php _e('Find Address/Location', APP_TD); ?>" />
					</label> 

					<input type="text" class="text" name="jr_address" id="geolocation-address" value="<?php echo esc_attr( $job->jr_address ); ?>" autocomplete="off" />
					
					<input type="hidden" class="text" name="jr_geo_latitude" id="geolocation-latitude" value="<?php echo esc_attr( $job->jr_geo_latitude ); ?>" />
					<input type="hidden" class="text" name="jr_geo_longitude" id="geolocation-longitude" value="<?php echo esc_attr( $job->jr_geo_longitude ); ?>" />

					<input type="hidden" class="text" name="jr_geo_country" id="geolocation-country" value="<?php echo esc_attr( $job->jr_geo_country ); ?>" />
					<input type="hidden" class="text" name="jr_geo_short_address" id="geolocation-short-address" value="<?php echo esc_attr( $job->jr_geo_short_address ); ?>" />
					<input type="hidden" class="text" name="jr_geo_short_address_country" id="geolocation-short-address-country" value="<?php echo esc_attr( $job->jr_geo_short_address_country ); ?>" />
				</p>
	
				<div id="map_wrap" style="border:solid 2px #ddd;"><div id="geolocation-map" style="width:100%;height:350px;"></div></div>
			
			</div>
			
		</fieldset>
		<fieldset>
			<legend><?php _e('Job Description', APP_TD); ?> <span title="required">*</span></legend>	
			<p><?php _e('Give details about the position, such as responsibilities &amp; salary.', APP_TD); ?><?php if (get_option('jr_html_allowed')=='no') : ?><?php _e(' HTML is not allowed.', APP_TD); ?><?php endif; ?></p>
			<p><textarea rows="5" cols="30" name="post_content" id="post_content" class="mceEditor required"><?php echo esc_textarea( $job->post_content ); ?></textarea></p>
		</fieldset>
		<?php if (get_option('jr_submit_how_to_apply_display')=='yes') : ?><fieldset>
			<legend><?php _e('How to apply', APP_TD); ?></legend>
			<p><?php _e('Tell applicants how to apply &ndash; they will also be able to email you via the &ldquo;apply&rdquo; form on your job listing\'s page.', APP_TD); ?><?php if (get_option('jr_html_allowed')=='no') : ?><?php _e(' HTML is not allowed.', APP_TD); ?><?php endif; ?></p>
			<p><textarea rows="5" cols="30" name="apply" id="apply" class="how mceEditor"><?php echo esc_textarea( $job->apply ); ?></textarea></p>
		</fieldset><?php endif; ?>

		<?php do_action( 'jr_after_submit_job_form', $job ); ?>

		<input type="hidden" name="action" value="<?php echo esc_attr($post_action); ?>" />
		<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>"/>
		<input type="hidden" name="ID" value="<?php echo esc_attr($job->ID); ?>">
		<input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">

		<input type="hidden" name="preview_job" />
		 <?php if ( get_query_var('job_relist') ): ?>
 			<input type="hidden" name="relist" value="1"/>
		<?php endif; ?>

		<p><input type="submit" class="submit" name="job_submit" value="<?php echo esc_attr( $submit_text ); ?>" /></p>

		<div class="clear"></div>
			
	</form>
	<script type="text/javascript">
		/* <![CDATA[ */

		jQuery.noConflict();
		(function($) { 
			<?php get_template_part('includes/countries'); ?>
			var availableCountries = [
				<?php
					global $countries;
					$countries_array = array();
					if ($countries) foreach ($countries as $code=>$country) {
						$countries_array[] = '"'.$country.'"';
					}
					echo implode(',', $countries_array);
				?>
			];
			var availableStates = [
				<?php
					global $states;
					echo implode(',', $states);
				?>
			];
			$("input#job_country").autocomplete({
				source: availableCountries,
				minLength: 2
			});
			$("input#job_city").autocomplete({
				source: availableStates,
				minLength: 1,
				search: function(){
					var c_val = $("input#job_country").val();
					if (c_val=='United States' || c_val.val()=='USA' || c_val=='US') return true; else return false;
				}
			});

			$("#submit_form").submit(function() {
				$('input#job_city, input#job_country').removeAttr('autocomplete');
			});

		})(jQuery);
		/* ]]> */
	</script>
<?php
	if ( 'yes' == get_option('jr_html_allowed') && ! wp_is_mobile() )
		jr_tinymce();
