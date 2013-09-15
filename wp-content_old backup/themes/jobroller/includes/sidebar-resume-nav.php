<li class="widget widget-nav">
	
<ul class="display_section">
	<li><a href="#browseby" class="noscroll"><?php _e('Browse by&hellip;', APP_TD); ?></a></li>
	<li><a href="#specialities" class="noscroll"><?php _e('Specialty', APP_TD); ?></a></li>
	<li><a href="#groups" class="noscroll"><?php _e('Group', APP_TD); ?></a></li>
</ul>
<div id="browseby" class="tabbed_section"><div class="contents">
    <ul>
		<?php
		
		// By Cat
		$args = array(
		    'hierarchical'       => false,
		    'parent'               => 0
		);
		$terms = get_terms( 'resume_category', $args );
		if ($terms) :
                    echo '<li><a class="top" href="#open">'.__('Job Category', APP_TD).'</a> <ul>';

                    foreach($terms as $term) :
                        echo '<li class="page_item ';
                        if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
                        echo '"><a href="'.get_term_link( $term->slug, 'resume_category' ).'">'.$term->name.'</a></li>';
                    endforeach;

                    echo '</ul></li>';
		endif;


		// By Job Type
		$args = array(
		    'hierarchical'       => false,
		    'parent'               => 0
		);
		$terms = get_terms( 'resume_job_type', $args );
		if ($terms) :
			echo '<li><a class="top" href="#open">'.__('Job Type', APP_TD).'</a> <ul>';
		
			foreach($terms as $term) :
				echo '<li class="page_item ';
				if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
				echo '"><a href="'.get_term_link( $term->slug, 'resume_job_type' ).'">'.$term->name.'</a></li>';
			endforeach;
			
			echo '</ul></li>';
		endif;
		
		// By Spoken Languages
		$args = array(
		    'hierarchical'       => false,
		    'parent'               => 0
		);
		$terms = get_terms( 'resume_languages', $args );
		if ($terms) :
			echo '<li><a class="top" href="#open">'.__('Spoken Languages', APP_TD).'</a> <ul>';
		
			foreach($terms as $term) :
				echo '<li class="page_item ';
				if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
				echo '"><a href="'.get_term_link( $term->slug, 'resume_languages' ).'">'.$term->name.'</a></li>';
			endforeach;
			
			echo '</ul></li>';
		endif;
		
		?>
		
		<?php jr_sidebar_resume_nav_browseby(); ?>
		
		<li><a class="top" href="<?php echo get_post_type_archive_link('resume'); ?>"><?php _e('View all resumes', APP_TD); ?></a></li>
		
    </ul>
</div></div>
<div id="specialities" class="tabbed_section"><div class="contents">
	<?php
		$args = array(
		    'hierarchical'       => false,
		    'parent'               => 0
		);
		$terms = get_terms( 'resume_specialities', $args );
		if ($terms) :
			echo '<ul class="job_tags">';
		
			foreach($terms as $term) :
				echo '<li><a href="'.get_term_link( $term->slug, 'resume_specialities' ).'">'.$term->name.'</a></li>';
			endforeach;
			
			echo '</ul>';
		endif;
	?>
</div></div>
<div id="groups" class="tabbed_section"><div class="contents">
	<?php
		$args = array(
		    'hierarchical'       => false,
		    'parent'               => 0
		);
		$terms = get_terms( 'resume_groups', $args );
		if ($terms) :
			echo '<ul class="job_tags">';
		
			foreach($terms as $term) :
				echo '<li><a href="'.get_term_link( $term->slug, 'resume_groups' ).'">'.$term->name.'</a></li>';
			endforeach;
			
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
