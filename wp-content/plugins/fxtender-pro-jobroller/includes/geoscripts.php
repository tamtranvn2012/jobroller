<?php
/*
 *  This function replaces the original jr_fx_geolocation_scripts() from AppThemes just for the customized zoom.
 *  The zoom is set by a constant (JR_DEFAULT_ZOOM ) that can not be overriden. 
 */
function jr_fx_geolocation_scripts( $zoom ) {
	$http = (is_ssl()) ? 'https' : 'http';
	$google_maps_api = (is_ssl()) ? 'https://maps-api-ssl.google.com/maps/api/js' : 'http://maps.google.com/maps/api/js';
?>
	<script type="text/javascript">
		
		function initialize_map() {
			
			var hasLocation = false;
			var center = new google.maps.LatLng(0.0,0.0);
			
			var postLatitude =  '<?php global $posted, $job_details, $post; if (isset($posted['jr_geo_latitude'])) echo $posted['jr_geo_latitude']; elseif (isset($job_details->ID)) echo get_post_meta($job_details->ID, '_jr_geo_latitude', true); elseif (isset($post->ID)) echo get_post_meta($post->ID, '_jr_geo_latitude', true); ?>';
			var postLongitude =  '<?php global $posted, $job_details; if (isset($posted['jr_geo_longitude'])) echo $posted['jr_geo_longitude']; elseif (isset($job_details->ID)) echo get_post_meta($job_details->ID, '_jr_geo_longitude', true); elseif (isset($post->ID)) echo get_post_meta($post->ID, '_jr_geo_longitude', true); ?>';

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
				if(jQuery("#geolocation-address").val() != '') {
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
						    //alert(results[i]['formatted_address']);
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
			script.src = "<?php echo $google_maps_api; ?>?v=3&sensor=false&language=<?php echo get_option('jr_gmaps_lang') ?>&region=<?php echo get_option('jr_gmaps_region') ?>&hl=<?php echo get_option('jr_gmaps_lang') ?>&callback=initialize_map";
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

function jr_fx_display_location_meta_box( $post, $show_lat_long = '', $zoom = 1) {
	global $key;
	
	//$zoom = jr_fx_validate( '_opt_widget_map_zoom' );
	jr_fx_geolocation_scripts( $zoom );
	?>
<div class="">	
	<?php wp_nonce_field( plugin_basename( __FILE__ ), $key . '_wpnonce', false, true ); ?>
	
	<div id="geolocation_box">
	
		<?php 
			$jr_geo_latitude = get_post_meta($post->ID, '_jr_geo_latitude', true);
			$jr_geo_longitude = get_post_meta($post->ID, '_jr_geo_longitude', true);
			
			if ($jr_geo_latitude && $jr_geo_longitude) :
				$jr_address = jr_reverse_geocode($jr_geo_latitude, $jr_geo_longitude);
				$jr_address = $jr_address['address'];
			else :
				$jr_address =  __('Anywhere', JR_FX_i18N_DOMAIN);
			endif;
		?>
	

		<div id="map_wrap" style="margin-top:5px; border:solid 2px #ddd;"><div id="geolocation-map" style="width:100%;height:200px;"></div></div>
	
	</div>
	<br/>
	<p class="geolocation_coordinates"><strong><?php _e('Current location:',JR_FX_i18N_DOMAIN); ?></strong><br/><?php echo $jr_address; ?>
	<?php
		if ($jr_geo_latitude && $jr_geo_longitude && $show_lat_long) :
			echo '<span class="geo_coord">';
			echo '<br/><em>Latitude:</em> '.$jr_geo_latitude;
			echo '<br/><em>Longitude:</em> '.$jr_geo_longitude;
			echo '</span>';
		endif;
	?></p>	
</div>	
<?php
}
