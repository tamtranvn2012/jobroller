<?php
/**
 * JobRoller Geoloaction functions
 * This file controls code for the Geolocation features.
 * Geolocation adapted from 'GeoLocation' plugin by Chris Boyd - http://geo.chrisboyd.net
 *
 *
 * @version 1.1
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */
 
define( 'JR_DEFAULT_ZOOM', 1 );

function _jr_get_geolocation_url( $address = '' ) {

	$google_maps_json_url = ( is_ssl() ? 'https' : 'http' ) . '://maps.googleapis.com/maps/api/geocode/json';

	$lang = get_option('jr_gmaps_lang');
	$region = get_option('jr_gmaps_region');

	$args = array(
		'sensor' 	=> 'false',
		'language' 	=> $lang,
	);

	if ( is_array( $address ) ) {
		$args['latlng'] = implode( ',', $address );
	} elseif( $address ) {
		$args['address'] = urlencode( $address );
	}
	$args['region'] = $region;

	return add_query_arg( $args, $google_maps_json_url );
}

function _jr_get_js_geolocation_url( $callback ) {

	$google_maps_js_url = ( is_ssl() ? 'https' : 'http' ) . '://maps.googleapis.com/maps/api/js';

	$lang = get_option('jr_gmaps_lang');
	$region = get_option('jr_gmaps_region');

	$params = array(
		'v' => 3,
		'sensor' => 'false',
		'language' => $lang,
		'callback' => $callback,
		'region' => $region,
	);

	return add_query_arg( $params, $google_maps_js_url );
}

function jr_clean_coordinate($coordinate) {
	//$pattern = '/^(\-)?(\d{1,3})\.(\d{1,15})/';
	$pattern = '/^(\-)?(\d{1,3}).(\d{1,15})/';
	preg_match($pattern, $coordinate, $matches);
	if (isset($matches[0])) return $matches[0];
}

function jr_reverse_geocode($latitude, $longitude) {

	$url = _jr_get_geolocation_url( array( $latitude, $longitude ) );

	$result = wp_remote_get($url);
	
	if( is_wp_error( $result ) ) :
		global $jr_log;
		$jr_log->write_log( __('Could not access Google Maps API. Your server may be blocking the request.', APP_TD) ); 
		return false;
	endif;
	$json = json_decode($result['body']);
	$city = '';
	$country = '';
	$short_country = '';
	$state = '';

	foreach ($json->results as $result)
	{
		foreach($result->address_components as $addressPart) {
			if((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$city = $addressPart->long_name;
	    	else if((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$state = $addressPart->long_name;
	    	else if((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types))) {
	    		$country = $addressPart->long_name;
	    		$short_country = $addressPart->short_name;
	    	}
		}
		if(($city) && ($state) && ($country)) break;
	}
			
	if(($city != '') && ($state != '') && ($country != ''))
		$address = $city.', '.$state.', '.$country;
	else if(($city != '') && ($state != ''))
		$address = $city.', '.$state;
	else if(($state != '') && ($country != ''))
		$address = $state.', '.$country;
	// fix for countries with no valid state
	else if(($city != '') && ($country !=''))
		$address = $city . ', ' . $country;
	//
	else if($country != '')
		$address = $country;

	if ($country=='United Kingdom') $short_country = 'UK';
		
	if(($city != '') && ($state != '') && ($country != '')) {
		$short_address = $city;
		$short_address_country = $state.', '.$country;
	} else if(($city != '') && ($state != '')) {
		$short_address = $city;
		$short_address_country = $state;
	} else if(($state != '') && ($country != '')) {
		$short_address = $state;
		$short_address_country = $country;
	// fix for countries with no valid state
	} else if(($city != '') && ($country != '')){
		$short_address = $city;
		$short_address_country = $country;
	//		
	} else if($country != '') {
		$short_address = $country;
		$short_address_country = '';
	}
	
	return array(
		'address' => $address,
		'country' => $country,
		'short_address' => $short_address,
		'short_address_country' => $short_address_country
	);
}

// Radial location search
function jr_radial_search($location, $radius, $address_array = '') {
	global $wpdb, $app_abbr;
	if (function_exists('json_decode') && isset($location)) :

		if (!$radius) $radius = 50;

		// KM/Miles
		if (get_option($app_abbr.'_distance_unit')=='km') $radius = $radius / 1.609344;

		// If address is not given, find it via Google Maps API or Cache
		if (!is_array($address_array)) {
			$address = _jr_get_geolocation_url( $location );

			$cached = get_transient( 'jr_geo_'.sanitize_title($location) );

			if ($cached) :
				$address = $cached;
			else :
				$address = json_decode( wp_remote_retrieve_body( wp_remote_get( $address ) ), true );
				if (is_array($address)) :
					set_transient( 'jr_geo_'.sanitize_title($location), $address, 60*60*24*7 ); // Cache for a week
				endif;
			endif;

			if (isset($address['results'])) :
				// Put address info into a nice array format
				$address_array = array(
					'longitude' => $address['results'][0]['geometry']['location']['lng'],
					'latitude' 	=> $address['results'][0]['geometry']['location']['lat']
				);

				$address_array['full_address'] = $address['results'][0]['formatted_address'];

			endif;

		}

		if ( is_array( $address_array ) ) {

			if (isset($address_array['longitude']) && isset($address_array['latitude'])) :

				$lng_min = $address_array['longitude'] - $radius / abs(cos(deg2rad($address_array['latitude'])) * 69);
				$lng_max = $address_array['longitude'] + $radius / abs(cos(deg2rad($address_array['latitude'])) * 69);
				$lat_min = $address_array['latitude'] - ($radius / 69);
				$lat_max = $address_array['latitude'] + ($radius / 69);

				$longitude_sql = "
					SELECT ID
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE meta_key = '_jr_geo_longitude'
					AND (meta_value+0)  between ('$lng_min') AND ('$lng_max') AND $wpdb->posts.post_status = 'publish'
				";

				// give a low weight to 'anywhere' jobs so they appear later on the listings
				$anywhere_sql = "
					SELECT ID, -1 as weight, post_date as date FROM $wpdb->posts
					WHERE ID NOT IN (
						SELECT $wpdb->postmeta.post_id FROM $wpdb->postmeta WHERE meta_key = 'geo_short_address'
					) AND $wpdb->posts.post_status = 'publish'
					ORDER BY post_date DESC
				";

				// Intersect the location 'latitude' and 'longitude' to return the related ID's. Additionally returns location 'anywhere' ID's.
				$posts = $wpdb->get_col ("
					SELECT ID FROM (
						SELECT ID, 1 as weight, post_date as date
						FROM $wpdb->posts
						LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
						INNER JOIN (
							$longitude_sql
						) as LONGITUDE using (ID)
						WHERE meta_key = '_jr_geo_latitude'
						AND (meta_value+0) between ('$lat_min') AND ('$lat_max') AND $wpdb->posts.post_status = 'publish'
						UNION (
							$anywhere_sql
						)
					) AS radial_search
					ORDER BY weight DESC, date DESC
				");
				$posts = array_unique($posts);

				return array('address' => $address_array['full_address'], 'posts' => $posts);
			endif;
		}
	endif;
	return false;
}

// Shows the map on single job listings
function jr_job_map() {
	global $post;

	$title = str_replace('"', '&quot;', wptexturize($post->post_title));
	$long 	= get_post_meta($post->ID, '_jr_geo_longitude', true);
	$lat 	= get_post_meta($post->ID, '_jr_geo_latitude', true);

	if (!$long || !$lat) return;
	?>

	<div id="job_map" style="height: 300px; display:none;"></div>
	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery.noConflict();
		(function($) {

			$(function() {

				// Map global vars
				var map;
				var marker;
				var center;

				// initialize Google Maps API
				function initMap() {

					// Define Map center
					center = new google.maps.LatLng('<?php echo $lat; ?>','<?php echo $long; ?>');

					// Define Map options
					var myOptions = {
					  'zoom': 10,
					  'center': center,
					  'mapTypeId': google.maps.MapTypeId.ROADMAP
					};

					// Load Map
					map = new google.maps.Map(document.getElementById('job_map'), myOptions);

					// Marker
					marker = new google.maps.Marker({ position: center, map: map, title: "<?php echo $title; ?>" });

				}

				// Slide Toggle
				$('a.toggle_map').click(function(){
			    	$('#share_form').slideUp();
			        $('#apply_form').slideUp();
					if (!map) initMap();
			        $('#job_map').slideToggle(function(){
						google.maps.event.trigger(map, 'resize');
						map.setCenter(center);
			        });
			        $('a.apply_online').removeClass('active');
			        $(this).toggleClass('active');
			        return false;
			    });

			});
		})(jQuery);
	/* ]]> */
	</script>
	<?php

}

function jr_radius_dropdown() {
	global $app_abbr;

?>
			<div class="radius">
				<label for="radius"><?php _e('Radius:', APP_TD); ?></label>
				<select name="radius" class="radius">
<?php
				$selected_radius = isset( $_GET['radius'] ) ? absint( $_GET['radius'] ) : 0;

				if ( !$selected_radius )
					$selected_radius = 50;

				foreach ( array( 1, 5, 10, 50, 100, 1000, 5000 ) as $radius ) {
?>
					<option value="<?php echo $radius; ?>" <?php selected( $selected_radius, $radius ); ?>><?php echo number_format_i18n( $radius ) . ' ' . get_option( $app_abbr.'_distance_unit' ) ?></option>
<?php
				}
?>
				</select>
			</div><!-- end radius -->
<?php
}

// clear cached locations to make sure geolocation changes are applied
function _jr_clear_geolocation_cache() {
	global $wpdb;

	$wpdb->query( "
		DELETE FROM $wpdb->options WHERE option_name LIKE '%_jr_geo_%'
	");
}

function jr_geolocation_scripts( $job = '' ) {
	global $job_details, $post, $posted; 

	$zoom = JR_DEFAULT_ZOOM;

	if ( $job ) {
		$posted['jr_geo_latitude'] = $job->jr_geo_latitude;
		$posted['jr_geo_longitude'] = $job->jr_geo_longitude;
	}
?>
	<script type="text/javascript">

		function initialize_map() {

			var hasLocation = false;
			var center = new google.maps.LatLng(0.0,0.0);
			
			var postLatitude =  '<?php if (isset($posted['jr_geo_latitude'])) echo $posted['jr_geo_latitude']; elseif (isset($job_details->ID)) echo get_post_meta($job_details->ID, '_jr_geo_latitude', true); elseif (isset($post->ID)) echo get_post_meta($post->ID, '_jr_geo_latitude', true); ?>';
			var postLongitude =  '<?php if (isset($posted['jr_geo_longitude'])) echo $posted['jr_geo_longitude']; elseif (isset($job_details->ID)) echo get_post_meta($job_details->ID, '_jr_geo_longitude', true); elseif (isset($post->ID)) echo get_post_meta($post->ID, '_jr_geo_longitude', true); ?>';

			if((postLatitude != '') && (postLongitude != '') ) {
				center = new google.maps.LatLng(postLatitude, postLongitude);
				hasLocation = true;
				jQuery("#geolocation-latitude").val(center.lat());
				jQuery("#geolocation-longitude").val(center.lng());
				reverseGeocode(center);
			}
				
		 	var myOptions = {
		      zoom: <?php echo $zoom; ?>,
		      center: center,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    };
		    
		    var geocoder = new google.maps.Geocoder();
		       
		    var map = new google.maps.Map(document.getElementById('geolocation-map'), myOptions);
			var marker = '';
			
			if(!hasLocation) {
		    	map.setZoom(<?php echo $zoom; ?>);
		    } else {
		    	map.setZoom(9);
		    }
			
			google.maps.event.addListener(map, 'click', function(event) {
				reverseGeocode(event.latLng);
			});
			
			var currentAddress;
			var customAddress = false;
			
			jQuery("#geolocation-load").click(function(){
				if( jQuery("#geolocation-address").val() != 'undefined' ) {
					customAddress = true;
					currentAddress = jQuery("#geolocation-address").val();
					geocode(currentAddress);
					return false;
				} else {
					marker.setMap(null);
					marker = '';
					jQuery("#geolocation-latitude").val('');
					jQuery("#geolocation-longitude").val('');
					return false;
				}
			});
			
			jQuery("#geolocation-address").keyup(function(e) {
				if(e.keyCode == 13)
					jQuery("#geolocation-load").click();
			});

			function placeMarker(location) {
				if (marker=='') {
					marker = new google.maps.Marker({
						position: center, 
						map: map, 
						title:'Job Location'
					});
				}
				marker.setPosition(location);
				map.setCenter(location);
				if((location.lat() != '') && (location.lng() != '')) {
					jQuery("#geolocation-latitude").val(location.lat());
					jQuery("#geolocation-longitude").val(location.lng());
				}
			}
			
			function geocode(address) {
				var geocoder = new google.maps.Geocoder();
			    if (geocoder) {
					geocoder.geocode({"address": address}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							placeMarker(results[0].geometry.location);
							reverseGeocode(results[0].geometry.location);
							if(!hasLocation) {
						    	map.setZoom(9);
						    	hasLocation = true;
							}
							jQuery("#geodata").html(results[0].geometry.location.lat() + ', ' + results[0].geometry.location.lng());
						}
					});
				}
			}

			function reverseGeocode(location) {
				var geocoder = new google.maps.Geocoder();
			    if (geocoder) {
					geocoder.geocode({"latLng": location}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {

						var address, country, state, short_address, short_address_country;
						
						var city = [];

						for ( var i in results ) {

						    var address_components = results[i]['address_components'];

						    for ( var j in address_components ) {

						    	var types = address_components[j]['types'];
						    	var long_name = address_components[j]['long_name'];
						    	var short_name = address_components[j]['short_name']; 

						    	if ( jQuery.inArray('locality', types)>=0 && jQuery.inArray('political', types)>=0 ) {
									if (jQuery.inArray(long_name, city)<0) city.push(long_name);
						    	}
						    	else if ( jQuery.inArray('administrative_area_level_1', types)>=0 && jQuery.inArray('political', types)>=0 ) {
						    		state = long_name;
						    	}
						    	else if ( jQuery.inArray('country', types)>=0 && jQuery.inArray('political', types)>=0 ) {
						    		country = long_name;
						    	}
						    } 

						    if((city) && (state) && (country)) break;
						}

						// fix for countries with no valid state
						if (!state) 
							city = city[0];
						else
							city = city.join(", ");

						if((city) && (state) && (country))
							address = city + ', ' + state + ', ' + country;
						else if((city) && (state))
							address = city + ', ' + state;
						else if((state) && (country))
							address = state + ', ' + country;
						// fix for countries with no valid state
						else if((city) && (country)) {
							address = city + ', ' + country;
						}	
						//
						else if(country)
							address = country;
							
						if((city) && (state) && (country)) {
							short_address = city;
							short_address_country = state + ', ' + country;
						} else if((city) && (state)) {
							short_address = city;
							short_address_country = state;
						} else if((state) && (country)) {
							short_address = state;
							short_address_country = country;
						// fix for countries with no valid state
						} else if((city) && (country)) {
							short_address = city;
							short_address_country = country;
						//
						} else if(country) {
							short_address = country;
							short_address_country = '';
						}

						// Set address field
						jQuery("#geolocation-address").val(address);
						
						// Set hidden address fields
						jQuery("#geolocation-short-address").val(short_address);
						jQuery("#geolocation-short-address-country").val(short_address_country);
						jQuery("#geolocation-country").val(country);
						
						// Place Marker
						placeMarker(location);
						
						return true;
					} 
					
					});
				}
				return false;
			}

		}

		function loadScript() {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "<?php echo _jr_get_js_geolocation_url('initialize_map'); ?>";
			document.body.appendChild(script);
		}

		jQuery(function(){
			// Prevent form submission on enter key
			jQuery("#submit_form").submit(function(e) {
				if (jQuery("input:focus").attr("id")=='geolocation-address') return false;
			});
			loadScript();
		});
		

	</script>
	<?php
}

