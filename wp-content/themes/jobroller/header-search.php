<?php global $app_abbr, $header_search; $header_search = true; ?>

<?php if (get_option('jr_show_searchbar')!=='no' && ( !isset($_GET['submit']) || ( isset($_GET['submit']) && $_GET['submit']!=='true' ) ) && ( !isset($_GET['myjobs']) || ( isset($_GET['myjobs']) && $_GET['myjobs']!=='true' ) ) ) : ?>

	<form action="<?php echo esc_url( home_url() ); ?>/" method="get" id="searchform">

		<div class="search-wrap">

			<div>
            <div class="searchWidth">
                <label>Keywords, Skills, Designation<br/>
				<input type="text" id="search" title="" name="s" class="text" placeholder="<?php _e('Type your skills, Designation',APP_TD); ?>" value="<?php if (isset($_GET['s'])) echo esc_attr(get_search_query()); ?>" /> </label>

              </div>
               
               <div class="searchWidth">
               <label>Location <br/>
				<input type="text" id="near" title="<?php _e('Location',APP_TD); ?>" name="location" class="text" placeholder="<?php _e('Type city name where you want to work',APP_TD); ?>" value="<?php if (isset($_GET['location'])) echo esc_attr($_GET['location']); ?>" /> </label>
                </div>
				<label for="search">&nbsp; <br/>
                <button type="submit" title="<?php _e('Go',APP_TD); ?>" class="submit"><?php _e('SEARCH',APP_TD); ?></button></label>

				<input type="hidden" name="ptype" value="<?php echo esc_attr( APP_POST_TYPE ); ?>" />

				<input type="hidden" name="latitude" id="field_latitude" value="" />
				<input type="hidden" name="longitude" id="field_longitude" value="" />
				<input type="hidden" name="full_address" id="field_full_address" value="" />
				<input type="hidden" name="north_east_lng" id="field_north_east_lng" value="" />
				<input type="hidden" name="south_west_lng" id="field_south_west_lng" value="" />
				<input type="hidden" name="north_east_lat" id="field_north_east_lat" value="" />
				<input type="hidden" name="south_west_lat" id="field_south_west_lat" value="" />
			</div>

			<?php jr_radius_dropdown(); ?>
		<div class="clear"></div>
		</div><!-- end search-wrap -->

	</form>

<?php endif; ?>
