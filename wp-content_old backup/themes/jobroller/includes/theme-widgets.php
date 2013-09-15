<?php
/**
 * Custom sidebar widgets
 *
 * @author AppThemes
 * @package JobRoller
 *
 */

// 125 ad
class JR_Widget_125ads extends WP_Widget {

    function JR_Widget_125ads() {
        $widget_ops = array( 'description' => __( 'Places an ad space in the sidebar for 125x125 ads', APP_TD) );
		$control_ops = array('width' => 500, 'height' => 350);
        $this->WP_Widget(false, __('125x125 Ad Space', APP_TD), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {

        extract($args);

		$title = isset( $instance['title'] ) ? apply_filters('widget_title', $instance['title'] ) : false;
		$newin = isset( $instance['newin'] ) ? $instance['newin'] : false;


        if (isset($instance['ads'])) :

			// separate the ad line items into an array
        	$ads = explode("\n", $instance['ads']);

        	if (sizeof($ads)>0) :

				echo $before_widget;

				if ($title) echo $before_title . $title . $after_title;
				echo '<div class="pad5"></div>';
				if ($newin) $newin = 'target="_blank"';
			?>

				<ul class="ads">
				<?php
				$alt = 1;
				foreach ($ads as $ad) :
					if ($ad && strstr($ad, '|')) {
						$alt = $alt*-1;
						$this_ad = explode('|', $ad);
						echo '<li class="';
						if ($alt==1) echo 'alt';
						echo '"><a href="'.$this_ad[0].'" rel="'.$this_ad[3].'" '.$newin.'><img src="'.$this_ad[1].'" width="125" height="125" alt="'.$this_ad[2].'" /></a></li>';
					}
				endforeach;
				?>
				</ul>

				<?php
				echo $after_widget;

	        endif;

        endif;
    }

   function update($new_instance, $old_instance) {
        $instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ads'] = strip_tags( $new_instance['ads'] );
		$instance['newin'] = $new_instance['newin'];

		return $instance;
    }

	function form( $instance ) {

		// load up the default values
		$default_ads = "http://appthemes.com|".get_bloginfo('template_url')."/images/ad125a.gif|Ad 1|nofollow\n"."http://appthemes.com|".get_bloginfo('template_url')."/images/ad125b.gif|Ad 2|follow\n"."http://appthemes.com|".get_bloginfo('template_url')."/images/ad125a.gif|Ad 3|nofollow\n"."http://appthemes.com|".get_bloginfo('template_url')."/images/ad125b.gif|Ad 4|follow";
		$defaults = array( 'title' => __('Sponsored Ads', APP_TD), 'ads' => $default_ads, 'rel' => true );
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label><?php _e('Title:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label><?php _e('Ads:', APP_TD); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('ads'); ?>" cols="5" rows="5"><?php echo $instance['ads']; ?></textarea>
			<?php _e('Enter one ad entry per line in the following format:<br /> <code>URL|Image URL|Image Alt Text|rel</code><br /><strong>Note:</strong> You must hit your &quot;enter/return&quot; key after each ad entry otherwise the ads will not display properly.',APP_TD); ?>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['newin'], 'on'); ?> id="<?php echo $this->get_field_id('newin'); ?>" name="<?php echo $this->get_field_name('newin'); ?>" />
			<label><?php _e('Open ads in a new window?', APP_TD); ?></label>
		</p>
<?php
	}
}

// 250x250 Ad
class JR_Widget_250ad extends WP_Widget {

    function JR_Widget_250ad() {
        $widget_ops = array( 'description' => __( 'Places an ad space in the sidebar for a 250x250 ad', APP_TD) );
		$control_ops = array('width' => 500, 'height' => 350);
        $this->WP_Widget(false, __('250x250 Ad Space', APP_TD), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {

        extract($args);

		$title = isset( $instance['title'] ) ? apply_filters('widget_title', $instance['title'] ) : false;
		$newin = isset( $instance['newin'] ) ? $instance['newin'] : false;


        if (isset($instance['ads'])) :

			// separate the ad line items into an array
        	$ads = explode("\n", $instance['ads']);

        	if (sizeof($ads)>0) :

				echo $before_widget;

				if ($title) echo $before_title . $title . $after_title;
				echo '<div class="pad5"></div>';
				if ($newin) $newin = 'target="_blank"';
				
				foreach ($ads as $ad) :
					if ($ad && strstr($ad, '|')) {
						$this_ad = explode('|', $ad);
						echo '<a href="'.$this_ad[0].'" rel="'.$this_ad[3].'" '.$newin.'><img src="'.$this_ad[1].'" width="250" height="250" alt="'.$this_ad[2].'" /></a><div class="pad5"></div>';
					}
				endforeach;

				echo $after_widget;


	        endif;

        endif;
    }


   function update($new_instance, $old_instance) {
        $instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ads'] = strip_tags( $new_instance['ads'] );
		$instance['newin'] = $new_instance['newin'];

		return $instance;
    }

	function form( $instance ) {

		// load up the default values
		$default_ads = "http://appthemes.com|".get_bloginfo('template_url')."/images/ad250.png|Ad 1|follow\n";
		$defaults = array( 'title' => __('Sponsored Ads', APP_TD), 'ads' => $default_ads, 'rel' => true );
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label><?php _e('Title:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label><?php _e('Ads:', APP_TD); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('ads'); ?>" cols="5" rows="5"><?php echo $instance['ads']; ?></textarea>
			<?php _e('Enter one ad entry per line in the following format:<br /> <code>URL|Image URL|Image Alt Text|rel</code><br /><strong>Note:</strong> You must hit your &quot;enter/return&quot; key after each ad entry otherwise the ads will not display properly.',APP_TD); ?>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php if (isset($instance['newin'])) checked($instance['newin'], 'on'); ?> id="<?php echo $this->get_field_id('newin'); ?>" name="<?php echo $this->get_field_name('newin'); ?>" />
			<label><?php _e('Open ad in a new window?', APP_TD); ?></label>
		</p>
<?php
	}
}

// Pack Pricing
class JR_Widget_pack_pricing extends WP_Widget {

	function JR_Widget_pack_pricing() {
		$widget_ops = array( 'description' => __( 'Displays Job Packs and Pricing information to the user.', APP_TD) );
		$this->WP_Widget(false, __('Job Packs', APP_TD), $widget_ops);
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Available Job Packs', APP_TD) : $instance['title'], $instance, $this->id_base);
		$buy = isset($instance['buy']) && jr_allow_purchase_separate_packs() ? $instance['buy'] : 'no';

		$packs = jr_get_plan_packs( jr_get_available_plans() );

		if (sizeof($packs) > 0) :
			echo $before_widget;
			if ( isset($title) && $title ) echo $before_title . $title . $after_title;
			echo '<ul class="pack_overview">';
			foreach ($packs as $pack) :

				$cost = 0;

				if ( empty($pack['plan_data'][JR_FIELD_PREFIX.'jobs_limit']) ) $pack['plan_data'][JR_FIELD_PREFIX.'jobs_limit'] = __('Unlimited', APP_TD);

				if ( !empty($pack['plan_data'][JR_FIELD_PREFIX.'pack_duration']) ) $pack['plan_data'][JR_FIELD_PREFIX.'pack_duration'] =  ', ' . __(' usable within ', APP_TD).$pack['plan_data'][JR_FIELD_PREFIX.'pack_duration'].__(' days', APP_TD);
				else $pack['plan_data'][JR_FIELD_PREFIX.'pack_duration'] = '';

				if ($pack['plan_data'][JR_FIELD_PREFIX.'duration']) $pack['plan_data'][JR_FIELD_PREFIX.'duration'] = __(' lasting ', APP_TD).$pack['plan_data'][JR_FIELD_PREFIX.'duration'].__(' days' ,APP_TD);
				else $pack['plan_data'][JR_FIELD_PREFIX.'duration'] = __( ' Endless', APP_TD );

				if ($pack['plan_data'][JR_FIELD_PREFIX.'price']) :
					$pack['plan_data'][JR_FIELD_PREFIX.'price'] = appthemes_get_price($pack['plan_data'][JR_FIELD_PREFIX.'price']); 
					$cost = 1; 
				else:
					$pack['plan_data'][JR_FIELD_PREFIX.'price'] = __('Free',APP_TD);
				endif;
				
				echo '<li><span class="cost">'.$pack['plan_data'][JR_FIELD_PREFIX.'price'].'</span><p><strong>'.$pack['plan_data']['title'].'</strong><br />'.$pack['plan_data'][JR_FIELD_PREFIX.'jobs_limit'].' '.__('Jobs', APP_TD).''.$pack['plan_data'][JR_FIELD_PREFIX.'duration'].$pack['plan_data'][JR_FIELD_PREFIX.'pack_duration'].'</p></li>';

				$checked = '';
			endforeach;

			if ( 'yes' == $buy ) :
?>
					<li><a class="button buy-pack-small" href="<?php echo add_query_arg( array('tab' => 'packs' ), jr_get_purchase_packs_url() ); ?>"><span><?php _e( 'Buy Packs', APP_TD ); ?></span></a></li>
<?php
			endif;

			echo '</ul>';

			echo $after_widget;
		endif;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form( $instance ) {
		$enable_buy = jr_allow_purchase_separate_packs();

		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$buy = isset($instance['buy']) ? esc_attr($instance['buy']) : 'no';
?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<?php if ($enable_buy == 'yes'): ?>
		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id( 'buy' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'buy' )); ?>" value="yes" <?php echo checked( $buy=='yes' ); ?>> <?php _e( 'Enable <i>Buy Now</i> button',APP_TD); ?>
		</p>
		<?php endif; 
	}
}


// facebook like box sidebar widget
class JR_Widget_Facebook extends WP_Widget {

    function JR_Widget_Facebook() {
        $widget_ops = array( 'description' => __( 'This places a Facebook page Like Box in your sidebar to attract and gain Likes from visitors.', APP_TD) );
        $this->WP_Widget(false, __('Facebook Like Box', APP_TD), $widget_ops);
    }

    function widget( $args, $instance ) {

        extract($args);

        $title = apply_filters('widget_title', $instance['title'] );
		$fid = $instance['fid'];
		$connections = $instance['connections'];
		$width = $instance['width'];
		$height = $instance['height'];

        echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;

        ?>
		<div class="pad5"></div>
        <iframe src="http://www.facebook.com/plugins/likebox.php?id=<?php echo $fid; ?>&amp;connections=<?php echo $connections; ?>&amp;stream=false&amp;header=true&amp;width=<?php echo $width; ?>&amp;height=<?php echo $height; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;" allowTransparency="true"></iframe>
		<div class="pad5"></div>
        <?php

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
       $instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fid'] = strip_tags( $new_instance['fid'] );
		$instance['connections'] = strip_tags($new_instance['connections']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);

		return $instance;
   }

   function form($instance) {

		$defaults = array( 'title' => __('Facebook Friends', APP_TD), 'fid' => '137589686255438', 'connections' => '12', 'width' => '260', 'height' => '365' );
		$instance = wp_parse_args( (array) $instance, $defaults );
   ?>

        <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('fid'); ?>"><?php _e('Facebook ID:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('fid'); ?>" name="<?php echo $this->get_field_name('fid'); ?>" value="<?php echo $instance['fid']; ?>" />
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" value="<?php echo $instance['connections']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('connections'); ?>"><?php _e('Connections', APP_TD) ?></label>
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', APP_TD) ?></label>
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height', APP_TD) ?></label>
		</p>

   <?php
   }
}




// social rss and twitter sidebar widget
class JR_Widget_Social extends WP_Widget {

   function JR_Widget_Social() {
	   $widget_ops = array( 'description' => __( 'This places Twitter and RSS Feed icons in your sidebar.', APP_TD) );
           $this->WP_Widget(false, __('Twitter &amp; RSS Icons', APP_TD), $widget_ops);
   }

   function widget($args, $instance) {
       extract( $args );
   ?>

        <?php echo $before_widget; ?>

        <ul>

            <li class="rss-balloon"><a href="<?php if ( get_option('jr_feedburner_url') <> '' ) { echo get_option('jr_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url').'?post_type=job_listing'; } ?>"><?php _e('Subscribe', APP_TD) ?></a><br/>
            <span><?php _e('Receive the latest job listings', APP_TD) ?></span></li>

            <li class="twitter-balloon"><a href="http://www.twitter.com/<?php echo get_option('jr_twitter_id'); ?>" title="<?php _e('Follow us on Twitter', APP_TD); ?>"><?php _e('Follow Us', APP_TD); ?></a><br/>
            <span><?php _e('Come join us on Twitter', APP_TD) ?></span></li>

        </ul>

        <?php echo $after_widget; ?>

   <?php
   }

   function update($new_instance, $old_instance) {
       return $new_instance;
   }

   function form($instance) {
    ?>

       <p><?php _e('There are no options for this widget.', APP_TD) ?></p>

   <?php
   }
}



// the latest job listings sidebar widget
class JR_Widget_Recent_Jobs extends WP_Widget {

	function JR_Widget_Recent_Jobs() {
		$widget_ops = array('classname' => 'widget_recent_entries', 'description' => __( "The most recent job listings on your site", APP_TD) );
		$this->WP_Widget('recent-jobs', __('New Job Listings', APP_TD), $widget_ops);
		$this->alt_option_name = 'widget_recent_entries';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_jobs', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('New Job Listings', APP_TD) : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		$r = new WP_Query(array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'post_type' => 'job_listing', 'ignore_sticky_posts' => 1));
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_jobs', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_jobs', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', APP_TD); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of jobs to show:', APP_TD); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}


// the job categories sidebar widget
class JR_Widget_Job_Categories extends WP_Widget {

	function JR_Widget_Job_Categories() {
		$widget_ops = array( 'classname' => 'widget_job_categories', 'description' => __( "A list or dropdown of job categories", APP_TD ) );
		$this->WP_Widget('job_categories', __('Job Categories', APP_TD), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Job Categories', APP_TD ) : $instance['title'], $instance, $this->id_base);
		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? '1' : '0';
		$d = $instance['dropdown'] ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		if ( $d ) {
			
			$terms = get_terms('job_cat');
			$output = '<select name="job_cat" id="dropdown_job_cat"><option value="">'.__('Select Category', APP_TD).'</option>';
			if ( $terms ) {
				$post_count = '';
				foreach ( $terms as $term ) {
					if ( $c ) $post_count = sprintf( ' (%d)', $term->count );
				 	$output .= "<option value='".esc_attr($term->slug)."'>".$term->name.$post_count."</option>";
				}
			}
			$output .= "</select>";
			echo $output;
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("dropdown_job_cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value ) {
			location.href = "<?php echo home_url(); ?>/?job_cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';
		$cat_args['taxonomy'] = APP_TAX_CAT;
		$cat_args['show_count'] = $c;
		wp_list_categories(apply_filters('widget_job_categories_args', $cat_args));
?>
		</ul>
<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('dropdown')); ?>" name="<?php echo esc_attr($this->get_field_name('dropdown')); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('dropdown')); ?>"><?php _e( 'Show as dropdown', APP_TD ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e( 'Show post counts', APP_TD ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php _e( 'Show hierarchy', APP_TD ); ?></label></p>
<?php
	}

}


// Twitter sidebar widget
class AppThemes_Widget_Twitter extends WP_Widget {

    function AppThemes_Widget_Twitter() {
        $widget_ops = array( 'description' => __( 'This places a real-time Twitter feed in your sidebar.', APP_TD) );
        $this->WP_Widget(false, __('Real-Time Twitter Feed', APP_TD), $widget_ops);
    }

    function widget( $args, $instance ) {

        extract($args);

        $title = apply_filters('widget_title', $instance['title'] );
		$tid = $instance['tid'];
		$api_key = $instance['api_key'];
		$keywords = strip_tags($instance['keywords']);
		$type = $instance['type'];
		$tcount = $instance['tcount'];
		$paging = $instance['paging'];
		$trefresh = $instance['trefresh'];
		$lang = $instance['lang'];
		$follow = isset($instance['follow']) ? $instance['follow'] : false;
		$connect = isset($instance['connect']) ? $instance['connect'] : false;

        echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;
        ?>

		<script type='text/javascript' src='<?php bloginfo('template_directory'); ?>/includes/js/jtweetsanywhere/jtweetsanywhere.min.js'></script>
		<?php if($api_key) : ?>
			<script type="text/javascript" src="http://platform.twitter.com/anywhere.js?id=<?php echo $api_key; ?>&v=1"></script>
		<?php endif; ?>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory') ?>/includes/js/jtweetsanywhere/jtweetsanywhere.css" />

		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#tweetFeed').jTweetsAnywhere({
					//searchParams: ['geocode=48.856667,2.350833,30km'],
				<?php if($type == 'username') { ?>
					  username: '<?php echo $tid; ?>',
			    <?php } else { ?>
					  searchParams: ['q=<?php echo $keywords; ?>', 'lang=<?php echo $lang; ?>'],
				<?php } ?>
					count: <?php echo $tcount; ?>,
				<?php if($follow) echo "showFollowButton: true,"; ?>
				<?php if($connect) echo "showConnectButton: true,"; ?>
					showTweetFeed: {
						expandHovercards: true,
						showSource: true,
						paging: {
							mode: '<?php echo $paging; ?>'
						},
						showTimestamp: {
							refreshInterval: 30
						},
						autorefresh: {
							mode: '<?php echo $trefresh; ?>',
							interval: 20
						}

					},
					onDataRequestHandler: function(stats, options) {
						if (stats.dataRequestCount < 11) {
							return true;
						}
						else {
							stopAutorefresh(options);
							// alert("To avoid struggling with Twitter's rate limit, we stop loading data after 10 API calls.");
						}
					}


				});

			});
		</script>

		<div id="tweetFeed"></div>
        <div class="pad5"></div>

        <?php

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
       $instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tid'] = strip_tags($new_instance['tid']);
		$instance['api_key'] = strip_tags($new_instance['api_key']);
		$instance['keywords'] = strip_tags($new_instance['keywords']);
		$instance['type'] = $new_instance['type'];
		$instance['trefresh'] = $new_instance['trefresh'];
		$instance['tcount'] = strip_tags($new_instance['tcount']);
		$instance['paging'] = $new_instance['paging'];
		$instance['lang'] = strip_tags($new_instance['lang']);
		$instance['follow'] = $new_instance['follow'];
		$instance['connect'] = $new_instance['connect'];

		return $instance;
   }

   function form($instance) {

		$defaults = array(
				'title' => 'Twitter Updates',
				'tid' => APP_TD,
				'api_key' => 'ZSO1guB57M6u0lm4cwqA',
				'keywords' => 'wordpress',
				'tcount' => '5',
				'type' => 'keyword',
				'paging' => 'prev-next',
				'trefresh' => 'trigger-insert',
				'lang' => 'en'
			);

		$instance = wp_parse_args((array) $instance, $defaults);
   ?>

        <p>
			<label><?php _e('Title:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label><?php _e('Twitter Username:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('tid'); ?>" name="<?php echo $this->get_field_name('tid'); ?>" value="<?php echo $instance['tid']; ?>" />
		</p>

		<p>
			<label><?php _e('Twitter API Key:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" value="<?php echo $instance['api_key']; ?>" />
		</p>

		<p>
			<label><?php _e('Keyword Tweets:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('keywords'); ?>" name="<?php echo $this->get_field_name('keywords'); ?>" value="<?php echo $instance['keywords']; ?>" />
		</p>

		<p>
			<label><?php _e('Display Type:', APP_TD) ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('type')); ?>" name="<?php echo esc_attr($this->get_field_name('type')); ?>" >
				<option value="username" <?php selected('username' == $instance['type']); ?>><?php _e('Show Username Tweets', APP_TD) ?></option>
				<option value="keywords" <?php selected('keywords' == $instance['type']); ?>><?php _e('Show Keyword Tweets', APP_TD) ?></option>
			</select>
		</p>

		<p>
			<label><?php _e('Refresh Mode:', APP_TD) ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('trefresh'); ?>" name="<?php echo $this->get_field_name('trefresh'); ?>" >
				<option value="none" <?php if ('none' == $instance['trefresh']) echo 'selected="selected"'; ?>><?php _e('None', APP_TD) ?></option>
				<option value="auto-insert" <?php if ('auto-insert' == $instance['trefresh']) echo 'selected="selected"'; ?>><?php _e('Real-Time Updates', APP_TD) ?></option>
				<option value="trigger-insert" <?php if ('trigger-insert' == $instance['trefresh']) echo 'selected="selected"'; ?>><?php _e('Click Button Updates', APP_TD) ?></option>
			</select>
		</p>

		<p>
			<label><?php _e('Paging Style:', APP_TD) ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('paging'); ?>" name="<?php echo $this->get_field_name('paging'); ?>" >
				<option value="more" <?php if ('more' == $instance['paging']) echo 'selected="selected"'; ?>><?php _e('More Button', APP_TD) ?></option>
				<option value="prev-next" <?php if ('prev-next' == $instance['paging']) echo 'selected="selected"'; ?>><?php _e('Next &amp; Previous Buttons', APP_TD) ?></option>
				<option value="endless-scroll" <?php if ('endless-scroll' == $instance['paging']) echo 'selected="selected"'; ?>><?php _e('Endless Scrolling', APP_TD) ?></option>
			</select>
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('tcount'); ?>" name="<?php echo $this->get_field_name('tcount'); ?>" value="<?php echo $instance['tcount']; ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('tcount'); ?>"><?php _e('Tweets Shown', APP_TD) ?></label>
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" value="<?php echo $instance['lang']; ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Default Language', APP_TD) ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['follow'], 'on'); ?> id="<?php echo $this->get_field_id('follow'); ?>" name="<?php echo $this->get_field_name('follow'); ?>" />
			<label for="<?php echo $this->get_field_id('follow'); ?>"><?php _e('Show Follow Button', APP_TD) ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php checked($instance['connect'], 'on'); ?> id="<?php echo $this->get_field_id('connect'); ?>" name="<?php echo $this->get_field_name('connect'); ?>" />
			<label for="<?php echo $this->get_field_id('connect'); ?>"><?php _e('Show Connect Button', APP_TD) ?></label>
		</p>


   <?php
   }
}


// sidebar top Listings today widget
class JR_Widget_Top_Listings_Today extends WP_Widget {

    function JR_Widget_Top_Listings_Today() {
        $widget_ops = array( 'description' => __( 'Your sidebar top listings today', APP_TD) );
        $this->WP_Widget('top_listings', __('Popular Listings Today', APP_TD), $widget_ops);
    }

    function widget( $args, $instance ) {

        extract($args);
        
        $post_type = (isset($instance['post_type']) && $instance['post_type']) ? $instance['post_type'] : 'job_listing';
        
        if ($post_type=='job_listing') :
        	$title = apply_filters('widget_title', empty($instance['title']) ? __('Popular Jobs Today', APP_TD) : $instance['title']);
		else :
			$title = apply_filters('widget_title', empty($instance['title']) ? __('Popular Resumes Today', APP_TD) : $instance['title']);
		endif;
		
        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        jr_todays_count_widget($post_type, 10);

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['post_type'] = strip_tags(stripslashes($new_instance['post_type']));
        return $instance;
    }

    function form($instance) {
    
    $post_type = (isset($instance['post_type'])) ? $instance['post_type'] : 'job_listing';
?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', APP_TD) ?></label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ($instance['title'])) {echo esc_attr( $instance['title']);} ?>" /></p>
    
    <p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Post type:', APP_TD) ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
		<option value="job_listing" <?php selected('job_listing', $post_type) ?>><?php _e('Job', APP_TD) ?></option>
		<option value="resume" <?php selected('resume', $post_type) ?>><?php _e('Resume', APP_TD) ?></option>
	</select>
	</p>
<?php
    }
}



// sidebar top Listings overall widget
class JR_Widget_Top_Listings_Overall extends WP_Widget {

    function JR_Widget_Top_Listings_Overall() {
        $widget_ops = array( 'description' => __( 'Your sidebar top listings overall', APP_TD) );
        $this->WP_Widget('top_listings_overall', __('Popular listings Overall', APP_TD), $widget_ops);
    }

    function widget( $args, $instance ) {

        extract($args);
        
        $post_type = (isset($instance['post_type']) && $instance['post_type']) ? $instance['post_type'] : 'job_listing';
        
        if ($post_type=='job_listing') :
        	$title = apply_filters('widget_title', empty($instance['title']) ? __('Popular Jobs Overall', APP_TD) : $instance['title']);
		else :
			$title = apply_filters('widget_title', empty($instance['title']) ? __('Popular Resumes Overall', APP_TD) : $instance['title']);
		endif;
		
        echo $before_widget;
        if ( $title )
			echo $before_title . $title . $after_title;
        
        jr_todays_overall_count_widget($post_type, 10);

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['post_type'] = strip_tags(stripslashes($new_instance['post_type']));
        return $instance;
    }

    function form($instance) {
    
    $post_type = (isset($instance['post_type'])) ? $instance['post_type'] : 'job_listing';
    
?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', APP_TD) ?></label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ($instance['title'])) { echo esc_attr( $instance['title']);} ?>" /></p>
    
    <p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Post type:', APP_TD) ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
		<option value="job_listing" <?php selected('job_listing', $post_type) ?>><?php _e('Job', APP_TD) ?></option>
		<option value="resume" <?php selected('resume', $post_type) ?>><?php _e('Resume', APP_TD) ?></option>
	</select>
	</p>
<?php
    }
}


// job tags and categories cloud widget
class JR_Widget_Jobs_Tag_Cloud extends WP_Widget {

	function JR_Widget_Jobs_Tag_Cloud() {
		$widget_ops = array( 'description' => __( 'Your most used job tags in cloud format', APP_TD) );
		$this->WP_Widget('job_tag_cloud', __('Jobs Tag Cloud', APP_TD), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'job_tag' == $current_taxonomy ) {
				$title = __('Job Tags', APP_TD);
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="tag_cloud">';
		wp_tag_cloud( apply_filters('widget_tag_cloud_args', array('taxonomy' => $current_taxonomy) ) );
		echo "</div>\n";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
			$instance['title'] = strip_tags(stripslashes($new_instance['title']));
			$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
			return $instance;
		}

		function form( $instance ) {
			$current_taxonomy = $this->_get_current_taxonomy($instance);
	?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy:', APP_TD) ?></label>

				<select class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
			<?php foreach ( get_object_taxonomies('job_listing') as $taxonomy ) :
					$tax = get_taxonomy($taxonomy);
					if ( !$tax->show_tagcloud || empty($tax->labels->name) )
						continue;
			?>
				<option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, $current_taxonomy) ?>><?php echo $tax->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			</p>
		<?php
		}

		function _get_current_taxonomy($instance) {
			if ( !empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy']) )
				return $instance['taxonomy'];

			return 'post_tag';
		}
}


// job tags and categories cloud widget
class JR_Widget_Resumes_Tag_Cloud extends WP_Widget {

	function JR_Widget_Resumes_Tag_Cloud() {
		$widget_ops = array( 'description' => __( 'Your most used resume tags in cloud format', APP_TD) );
		$this->WP_Widget('resume_tag_cloud', __('Resumes Tag Cloud', APP_TD), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'job_tag' == $current_taxonomy ) {
				$title = __('Resume Tags', APP_TD);
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="tag_cloud">';
		wp_tag_cloud( apply_filters('widget_tag_cloud_args', array('taxonomy' => $current_taxonomy) ) );
		echo "</div>\n";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
			$instance['title'] = strip_tags(stripslashes($new_instance['title']));
			$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
			return $instance;
		}

		function form( $instance ) {
			$current_taxonomy = $this->_get_current_taxonomy($instance);
	?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', APP_TD) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy:', APP_TD) ?></label>

				<select class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
			<?php foreach ( get_object_taxonomies('resume') as $taxonomy ) :
					$tax = get_taxonomy($taxonomy);
					if ( !$tax->show_tagcloud || empty($tax->labels->name) )
						continue;
			?>
				<option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, $current_taxonomy) ?>><?php echo $tax->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			</p>
		<?php
		}

		function _get_current_taxonomy($instance) {
			if ( !empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy']) )
				return $instance['taxonomy'];

			return 'resume_specialities';
		}
}


// Resume categories widget
class JR_Widget_Resume_Categories extends WP_Widget {

	function JR_Widget_Resume_Categories() {
		$widget_ops = array( 'classname' => 'widget_resume_categories', 'description' => __( "A list of resume categories", APP_TD ) );
		$this->WP_Widget('resume_categories', __('Resume Categories', APP_TD), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Resume Categories', APP_TD ) : $instance['title'], $instance, $this->id_base);
		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h, 'taxonomy' => 'resume_category', 'title_li' => '');
		
		echo '<ul>';
		
		wp_list_categories(apply_filters('widget_job_categories_args', $cat_args));
		
		echo '</ul>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e( 'Show post counts', APP_TD ); ?></label></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php _e( 'Show hierarchy', APP_TD ); ?></label></p>
<?php
	}

}

// register the custom sidebar widgets
function jr_widgets_init() {
    if (!is_blog_installed())
        return;
	
	register_widget('JR_Widget_250ad');
	register_widget('JR_Widget_pack_pricing');
	register_widget('JR_Widget_125ads');
    register_widget('JR_Widget_Facebook');
    register_widget('JR_Widget_Social');
    register_widget('JR_Widget_Recent_Jobs');
    register_widget('JR_Widget_Job_Categories');
	register_widget('AppThemes_Widget_Twitter');
	register_widget('JR_Widget_Top_Listings_Today');
	register_widget('JR_Widget_Top_Listings_Overall');
	register_widget('JR_Widget_Jobs_Tag_Cloud');
	register_widget('JR_Widget_Resumes_Tag_Cloud');
	register_widget('JR_Widget_Resume_Categories');

    do_action('widgets_init');
}

add_action('init', 'jr_widgets_init', 1);


// remove some of the default sidebar widgets
function jr_unregister_widgets() {
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Search');
}

add_action('widgets_init', 'jr_unregister_widgets');
