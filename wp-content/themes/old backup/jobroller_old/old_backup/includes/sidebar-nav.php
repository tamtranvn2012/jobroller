<li class="widget widget-nav">
	
<ul class="display_section">
	<li><a href="#browseby" class="noscroll"><?php _e('Browse by&hellip;', APP_TD); ?></a></li>
	<li><a href="#tags" class="noscroll"><?php _e('Tags', APP_TD); ?></a></li>
</ul>
<div id="browseby" class="tabbed_section"><div class="contents">
    <ul>
		<?php
		// By Type
		$args = array(
		    'hierarchical'       => false,
		    'parent'             => 0,
			'hide_empty'		 => (int)get_option('jr_show_empty_categories'),
		);
		$terms = get_terms( 'job_type', apply_filters('jr_nav_job_type', $args) );
		if ($terms) :
			echo '<li><a class="top" href="#open">'.__('Job Type', APP_TD).'</a> <ul>';
		
			foreach($terms as $term) :
				echo '<li class="page_item ';
				if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
				echo '"><a href="'.get_term_link( $term->slug, 'job_type' ).'">'.$term->name.'</a></li>';
			endforeach;
			
			echo '</ul></li>';
		endif;
		
		// By Salary
		$args = array(
		    'hierarchical'       => false,
		    'parent'               => 0,
			'hide_empty'		 => (int)get_option('jr_show_empty_categories')
		);
		$terms = get_terms( 'job_salary',  apply_filters('jr_nav_job_salary', $args) );
		if ($terms) :
			echo '<li><a class="top" href="#open">'.__('Job Salary', APP_TD).'</a> <ul>';
		
			foreach($terms as $term) :
				echo '<li class="page_item ';
				if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
				echo '"><a href="'.get_term_link( $term->slug, 'job_salary' ).'">'.$term->name.'</a></li>';
			endforeach;
			
			echo '</ul></li>';
		endif;

		// By Cat
		$args = array(
		    'hierarchical'       => false,
		    'parent'             => 0,
			'hide_empty'		 => (int)get_option('jr_show_empty_categories'),
		);
		$terms = get_terms( 'job_cat', apply_filters('jr_nav_job_cat', $args) );
		if ($terms) :
                    echo '<li><a class="top" href="#open">'.__('Job Category', APP_TD).'</a> <ul>';

                    foreach($terms as $term):
                    	echo '<li class="page_item ';
                    	if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
                    	echo '"><a href="'.get_term_link( $term->slug, 'job_cat' ).'">'.$term->name.'</a></li>';
						
						if ( ! $term->count ):
							 //	echo terms childrens
							 $children = get_term_children($term->term_id, 'job_cat');
							 if ( is_array( $children ) ) foreach($children as $child):
							 		$child_term = get_term_by('id', $child, 'job_cat');
			                    	echo '<li class="page_item page_item_children ';
			                    	if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$child_term->slug ) echo 'current_page_item';
			                    	echo '"><a href="'.get_term_link( $child_term->slug, 'job_cat' ).'">- '.$child_term->name.'</a></li>';
							 endforeach	;	
							 
						endif;
						
                    endforeach;

                    echo '</ul></li>';
		endif;
		
		// By Date
		if ( $datepage = JR_Date_Archive_Page::get_id() ) :
                    $datepagelink = get_permalink($datepage);
                    echo '<li><a class="top" href="#open">'.__('Date posted', APP_TD).'</a> <ul>';
                    echo '<li><a href="'.add_query_arg( array( 'show' => 'today', 'jobs_by_date' => '1' ), $datepagelink).'">'.__('Today',APP_TD).'</a></li>';
                    echo '<li><a href="'.add_query_arg( array( 'show' => 'week', 'jobs_by_date' => '1' ), $datepagelink).'">'.__('This Week',APP_TD).'</a></li>';
                    echo '<li><a href="'.add_query_arg( array( 'show' => 'lsstweek', 'jobs_by_date' => '1' ), $datepagelink).'">'.__('Last Week',APP_TD).'</a></li>';
                    echo '<li><a href="'.add_query_arg( array( 'show' => 'month', 'jobs_by_date' => '1' ), $datepagelink).'">'.__('This Month',APP_TD).'</a></li>';
                    echo '</ul></li>';
		endif;
		?>
		
		<?php jr_sidebar_nav_browseby(); ?>
		
    </ul>
</div></div>
<div id="tags" class="tabbed_section"><div class="contents">
	<?php
		$args = array(
		    'hierarchical'       => false,
		    'parent'             => 0,
			'hide_empty'		 => (int)get_option('jr_show_empty_categories')
		);
		$terms = get_terms( 'job_tag',  apply_filters('jr_nav_job_tag', $args) );
		if ($terms) :
			echo '<ul class="job_tags">';
		
			foreach($terms as $term)
				echo '<li><a href="'.get_term_link( (int)$term->term_id, 'job_tag' ).'">'.$term->name.'</a></li>';
			
			echo '</ul>';
		endif;
	?>
</div></div>
	<script type="text/javascript">
		/* <![CDATA[ */
			jQuery('ul.widgets li.widget.widget-nav div ul li ul, ul.widgets li.widget.widget-nav div').hide();
			jQuery('.widget-nav div.tabbed_section:eq(0), .widget-nav div.tabbed_section:eq(0) .contents').show();
			jQuery('.widget-nav ul.display_section li:eq(0)').addClass('active');
			
			// Tabs
			jQuery('.widget-nav ul.display_section li a').click(function(){
				
				jQuery('.widget-nav div.tabbed_section .contents').fadeOut();
				jQuery('.widget-nav div.tabbed_section').hide();
				
				jQuery(jQuery(this).attr('href')).show();
				jQuery(jQuery(this).attr('href') + ' .contents').fadeIn();
				
				jQuery('.widget-nav ul.display_section li').removeClass('active');
				jQuery(this).parent().addClass('active');
				
				return false;
			});

			// Sliding
			jQuery('ul.widgets li.widget.widget-nav div ul li a.top').click(function(){
				jQuery(this).parent().find('ul').slideToggle();
			});

		/* ]]> */
	</script>
</li>
