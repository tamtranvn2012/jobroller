<?php
/**
 * New sidebar widgets
 *
 */
class JR_FX_mini_gmaps extends WP_Widget {
    function JR_FX_mini_gmaps() {
        $widget_ops = array( 'description' => __( 'FXtender Mini Google Maps Location', JR_FX_i18N_DOMAIN) );
        $this->WP_Widget('jr_fx_mini_gmaps', __('FXtender Mini Google Maps Location', JR_FX_i18N_DOMAIN), $widget_ops);
    }

    function widget( $args, $instance ) {
		global $wp_query;
		
        extract($args);
		
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);			
		$coord = (isset($instance['coord'])?$instance['coord']:0);		
		$zoom = (isset($instance['zoom'])?$instance['zoom']:10);		
		
		if ( $wp_query->post  && is_singular(JR_FX_JR_POST_TYPE) ) : 
		    echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;

			jr_fx_display_location_meta_box( $wp_query->post, $coord, ($zoom?$zoom:1) );
			echo $after_widget;			
		endif;
    } 

  function update( $new_instance, $old_instance ) {
        
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['zoom'] = strip_tags( $new_instance['zoom'] );
		$instance['coord'] = strip_tags( $new_instance['coord'] );  
		
    return $new_instance;
  }

  function form( $instance ) {	
	
	$defaults = array( 'title' => __('Location', JR_FX_i18N_DOMAIN), 'coord' => 0, 'zoom' => 10 );
	$instance = wp_parse_args( (array) $instance, $defaults );	
    ?>

    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', JR_FX_i18N_DOMAIN ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title'];  ?>" />
      </label>
    </p>
	<p>
		<?php _e( 'Show Latitude/Longitude?', JR_FX_i18N_DOMAIN ); ?> <input type="checkbox"  id="<?php echo $this->get_field_id( 'coord' ); ?>" name="<?php echo $this->get_field_name( 'coord' ); ?>" <?php echo ($instance['coord']?'checked':''); ?>>
	</p>	
	<p>
      <label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php _e( 'Zoom Level:' ); ?>
      <input size=2 id="<?php echo $this->get_field_id( 'zoom' ); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>" type="text" value="<?php echo $instance['zoom']; ?>" />
      </label>	
	<p>	
	<p> <?php _e('<strong>Note</strong>: This widget will only work within the Job page.', JR_FX_i18N_DOMAIN ); ?> </p>

    <?php
  }
}

class JR_FX_company_logo extends WP_Widget {
    function JR_FX_company_logo() {
        $widget_ops = array( 'description' => __( 'FXtender Company Logo', JR_FX_i18N_DOMAIN) );
        $this->WP_Widget('jr_fx_company_logo', __('FXtender Company Logo', JR_FX_i18N_DOMAIN), $widget_ops);
    }

    function widget( $args, $instance ) {
		global $wp_query;
		$post_id = $wp_query->post->ID;
		$post_title = $wp_query->post->post_title;
	
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);

		$size = $instance['size'];
		$width = $instance['width'];
		$height = $instance['height'];
		
		# if width and height are passed, ignore the fixed sizes
		if ( $width && $height && $instance['size']=='custom' )
			$size = array($width,  $height);
		
		if ( $wp_query->post &&  is_singular(JR_FX_JR_POST_TYPE) && has_post_thumbnail($post_id) ) :
			echo $before_widget;
			
			if ( $title )
				echo $before_title . $title . $after_title;
				
				if ( has_post_thumbnail($post_id) ) {
					$logo = '<div class="jr_fx_side_logo">';
					$author = get_user_by('id', $wp_query->post->post_author);
					$logo_link = '<a href="' . get_author_posts_url( $author->ID, $author->user_nicename ) . '" title="' . esc_attr( $post_title ) . '">';
					if ($instance['link']) $logo .= $logo_link;
					$logo .= get_the_post_thumbnail( $post_id, $size, array( 'title' => esc_attr( $post_title ) ) );				
					if ($instance['link']) $logo .= '</a>';
					$logo .= '</div>';			
					echo $logo;
				}
				echo $after_widget;
		endif;
				
    } 

  function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );		
		$instance['size'] = strip_tags( $new_instance['size'] );
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['link'] = strip_tags( $new_instance['link'] );

		return $instance;  
  }

  function form( $instance ) {	
	$defaults = array( 'title' => __('Company Logo', JR_FX_i18N_DOMAIN), 'size' =>'thumbnail', 'width' => '', 'height' => '', 'link' => '');
	$instance = wp_parse_args( (array) $instance, $defaults );	
	    
    ?>

    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title'];  ?>" />
      </label>
    </p>
	<p>
		<label><?php _e('Size',JR_FX_i18N_DOMAIN) ?>:</label><br/>
		<select id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>"  >
			<option value="thumbnail" <?php echo ($instance['size']=='thumbnail'?'selected':''); ?>><?php _e( 'Thumbnail',JR_FX_i18N_DOMAIN); ?></option>
			<option value="medium" <?php echo ($instance['size']=='medium'?'selected':''); ?>><?php _e( 'Medium',JR_FX_i18N_DOMAIN); ?></option>
			<option value="large" <?php echo ($instance['size']=='large'?'selected':''); ?>><?php _e( 'Large',JR_FX_i18N_DOMAIN); ?></option>					
			<option value="custom" <?php echo ($instance['size']=='custom'?'selected':''); ?>><?php _e( 'Custom Size',JR_FX_i18N_DOMAIN); ?></option>
		</select >
	</p>
	<p>
		<?php _e( 'Link to Author Jobs?', JR_FX_i18N_DOMAIN ); ?> <input type="checkbox"  id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" <?php echo ($instance['link']?'checked':''); ?>>
	</p>	
		<p>	W: <input type="text" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" size=3 value="<?php echo $instance['width']; ?>"> x	H: <input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" size=3 value="<?php echo $instance['height']; ?>"> </p>		
	<p> <?php _e('<strong>Note</strong>: This widget will only work on a job page.', JR_FX_i18N_DOMAIN ); ?> </p>
    <?php
  }
}
add_action( 'widgets_init', 'jr_fx_widgets_init' );
function jr_fx_widgets_init() {
  if ( jr_fx_has_perms_to( '_widget_mini_gmaps' ) ) 
	register_widget( 'JR_FX_mini_gmaps' );
  if ( jr_fx_has_perms_to ( '_widget_company_logo' ) ) 
	register_widget ( 'JR_FX_company_logo' );
}

