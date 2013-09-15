<?php
/**
 * JobRoller Resume form
 * Function outputs the resume submit form
 *
 *
 * @version 1.4
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_submit_resume_form( $resume_id = 0 ) {
	
	global $post, $posted;
	
	jr_geolocation_scripts();

	?>
	<form action="<?php 
		if ($resume_id>0) echo add_query_arg('edit', $resume_id, get_permalink( $post->ID )); 
		else echo get_permalink( $post->ID ); 
	?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">
		
		<p><?php _e('Enter your resume details below. Once saved you will be able to view your resume and optionally add links to your websites/social networks if you wish.', APP_TD); ?></p>
		
		<fieldset>
			<legend><?php _e('Your Resume', APP_TD); ?></legend>
			
			<p><label for="resume_name"><?php _e('Resume Title', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text" name="resume_name" id="resume_name" class="text" placeholder="<?php _e('e.g. Lead Developer', APP_TD); ?>" value="<?php if (isset($posted['resume_name'])) echo $posted['resume_name']; ?>" /></p>
			
			<p><label for="summary"><?php _e('Resume Summary', APP_TD); ?> <span title="required">*</span></label> <textarea rows="5" cols="30" name="summary" id="summary" placeholder="<?php _e('Briefly describe yourself.', APP_TD); ?>" class="short" style="height:100px;"><?php if (isset($posted['summary'])) echo $posted['summary']; ?></textarea></p>
			
			<p class="optional"><label for="resume_cat"><?php _e('Resume Category', APP_TD); ?></label> <?php
				$sel = 0;
				if (isset($posted['resume_cat']) && $posted['resume_cat']>0) $sel = $posted['resume_cat']; 
				$args = array(
				    'orderby'            => 'name', 
				    'order'              => 'ASC',
				    'name'               => 'resume_cat',
				    'hierarchical'       => 1, 
				    'echo'				 => 0,
				    'class'              => 'resume_cat',
				    'selected'			 => $sel,
				    'taxonomy'			 => 'resume_category',
				    'hide_empty'		 => false
				);
				$dropdown = wp_dropdown_categories( $args );
				$dropdown = str_replace('class=\'resume_cat\' >','class=\'resume_cat\' ><option value="">'.__('Select a category&hellip;', APP_TD).'</option>',$dropdown);
				echo $dropdown;
			?></p>	
			
			<p class="optional"><label for="your-photo"><?php _e('Resume Photo (.jpg, .gif or .png)', APP_TD); ?></label> <input type="file" class="text" name="your-photo" id="your-photo" /></p>
  
			<p class="optional"><label for="desired_salary"><?php _e('Desired Salary (only numeric values)', APP_TD); ?></label> <input type="text" class="tags text" name="desired_salary" id="desired_salary" placeholder="<?php _e('e.g. 25000', APP_TD); ?>" value="<?php if (isset($posted['desired_salary'])) echo $posted['desired_salary']; ?>" /></p>
			
			<p class="optional"><label for="desired_position"><?php _e('Desired Type of Position', APP_TD); ?></label> <select name="desired_position" id="desired_position">
				<option value=""><?php _e('Any', APP_TD); ?></option>
				<?php
				$job_types = get_terms( 'resume_job_type', array( 'hide_empty' => '0' ) );
				if ($job_types && sizeof($job_types) > 0) {
					foreach ($job_types as $type) {
						?>
						<option <?php if (isset($posted['desired_position']) && $posted['desired_position']==$type->slug) echo 'selected="selected"'; ?> value="<?php echo $type->slug; ?>"><?php echo $type->name; ?></option>
						<?php
					}
				}
				?>
			</select></p>
			
		</fieldset>	

		<fieldset>
			<legend><?php _e('Your Contact Details', APP_TD); ?></legend>
			
			<p><?php _e('Optionally fill in your contact details below to have them appear on your resume. This is important if you want employers to be able to contact you!', APP_TD); ?></p>
			
			<p class="optional"><label for="email_address"><?php _e('Email Address', APP_TD); ?></label> <input type="text" class="text" name="email_address" value="<?php if (isset($posted['email_address'])) echo $posted['email_address']; ?>" id="email_address" placeholder="<?php _e('you@yourdomain.com', APP_TD); ?>" /></p>
			<p class="optional"><label for="tel"><?php _e('Telephone', APP_TD); ?></label> <input type="text" class="text" name="tel" value="<?php if (isset($posted['tel'])) echo $posted['tel']; ?>" id="tel" placeholder="<?php _e('Telephone including area code', APP_TD); ?>" /></p>
			<p class="optional"><label for="mobile"><?php _e('Mobile', APP_TD); ?></label> <input type="text" class="text" name="mobile" value="<?php if (isset($posted['mobile'])) echo $posted['mobile']; ?>" id="mobile" placeholder="<?php _e('Mobile number', APP_TD); ?>" /></p>
			
		</fieldset>	
		
		<fieldset>
			<legend><?php _e('Resume Location', APP_TD); ?></legend>
			<p><?php _e('Entering your location will help employers find you.', APP_TD); ?></p>
			<div id="geolocation_box">
				<p>
					<label>
						<input id="geolocation-load" type="button" class="button geolocationadd submit" value="<?php esc_attr_e('Find Address/Location', APP_TD); ?>" />
					</label>

					<input type="text" class="text" name="jr_address" id="geolocation-address" value="<?php if (isset($posted['jr_address'])) echo esc_attr($posted['jr_address']); ?>" />
					<input type="hidden" class="text" name="jr_geo_latitude" id="geolocation-latitude" value="<?php if (isset($posted['jr_geo_latitude'])) echo esc_attr($posted['jr_geo_latitude']); ?>" />
					<input type="hidden" class="text" name="jr_geo_longitude" id="geolocation-longitude" value="<?php if (isset($posted['jr_geo_longitude'])) echo esc_attr($posted['jr_geo_longitude']); ?>" />
				</p>

				<div id="map_wrap" style="border:solid 2px #ddd;"><div id="geolocation-map" style="width:100%;height:300px;"></div></div>
			</div>
			
		</fieldset>	

		<fieldset>
			<legend><?php _e('Education', APP_TD); ?></legend>
			<p><?php _e('Detail your education, including details on your qualifications and schools/universities attended.', APP_TD); ?></p>
			<p><textarea rows="5" cols="30" name="education" id="education" class="mceEditor"><?php if (isset($posted['education'])) echo $posted['education']; ?></textarea></p>
		</fieldset>
		<fieldset>
			<legend><?php _e('Experience', APP_TD); ?></legend>
			<p><?php _e('Detail your work experience, including details on your employers and job roles and responsibilities.', APP_TD); ?></p>
			<p><textarea rows="5" cols="30" name="experience" id="experience" class="mceEditor"><?php if (isset($posted['experience'])) echo $posted['experience']; ?></textarea></p>
		</fieldset>	
		
		<fieldset>
			<legend><?php _e('Skills &amp; Specialties', APP_TD); ?></legend>

			<p class="optional"><label for="skills"><?php _e('Skills <small>(one per line)</small>', APP_TD); ?></label> <textarea rows="1" cols="30" name="skills" id="skills" class="short grow" placeholder="<?php _e('e.g. XHTML (5 years experience)', APP_TD); ?>"><?php if (isset($posted['skills'])) echo $posted['skills']; ?></textarea></p>
			
			<p class="optional"><label for="specialities"><?php _e('Specialties <small>e.g. Public speaking, Team management</small>', APP_TD); ?></label> <input type="text" class="tags text tag-input-commas" data-separator="," name="specialities" id="specialities" placeholder="<?php _e('e.g. Public Speaking, Team Management', APP_TD); ?>" value="<?php if (isset($posted['specialities'])) echo $posted['specialities']; ?>" /></p>
			
			<p class="optional"><label for="groups"><?php _e('Groups/Associations <small>e.g. IEEE, W3C</small>', APP_TD); ?></label> <input type="text" class="text text tag-input-commas" data-separator="," name="groups" value="<?php if (isset($posted['groups'])) echo $posted['groups']; ?>" id="groups" placeholder="<?php _e('e.g. IEEE, W3C', APP_TD); ?>" /></p>
			
			<p class="optional" id="languages_wrap"><label for="languages"><?php _e('Spoken Languages <small>e.g. English, French</small>', APP_TD); ?></label> <input type="text" class="text text tag-input-commas" data-separator="," name="languages" value="<?php if (isset($posted['languages'])) echo $posted['languages']; ?>" id="languages" placeholder="<?php _e('e.g. English, French', APP_TD); ?>" /></p>
			
		</fieldset>
		
		<p><input type="submit" class="submit" name="save_resume" value="<?php _e('Save &rarr;', APP_TD); ?>" /></p>
			
		<div class="clear"></div>
			
	</form>
	<script type="text/javascript">
		
		jQuery(function(){
		
			/* Auto Complete */
			var availableTags = [
				<?php
					$terms_array = array();
					$terms = get_terms( 'resume_languages', 'hide_empty=0' );
					if ($terms) foreach ($terms as $term) {
						$terms_array[] = '"'.$term->name.'"';
					}
					echo implode(',', $terms_array);
				?>
			];
			function split( val ) {
				return val.split( /,\s*/ );
			}
			function extractLast( term ) {
				return split( term ).pop();
			}
			jQuery("#languages_wrap input").on( "keydown", function( event ) {
				if ( (event.keyCode === jQuery.ui.keyCode.TAB || event.keyCode === jQuery.ui.keyCode.COMMA) &&
						jQuery( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			}).autocomplete({
			    minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( jQuery.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
			    focus: function() {
			    	jQuery('input.ui-autocomplete-input').val('');
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {

					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					//this.value = terms.join( ", " );
					this.value = terms.join( "" );
					
					jQuery(this).blur();
					jQuery(this).focus();
					
					return false;
				}
			});
		
		});
	</script>
	<?php
	if ( get_option('jr_html_allowed') == 'yes' && ! wp_is_mobile() )
		jr_tinymce();
}
