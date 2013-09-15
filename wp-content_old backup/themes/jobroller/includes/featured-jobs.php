<?php
	$my_query = jr_get_featured_jobs();
	
	ob_start();
?>
<?php if ( $my_query && $my_query->have_posts() ) : $alt = 1; echo '<div class="section"><h2 class="pagetitle"><small class="rss"><a href="'.jr_get_featured_jobs_rss_url().'"><img src="'.get_bloginfo('template_url').'/images/feed.png" title="'.__('Featured Jobs RSS Feed',APP_TD).'" alt="'.__('Featured Jobs RSS Feed',APP_TD).'" /></a></small> '.__('Featured Jobs',APP_TD).'</h2><ol class="jobs">'; while ($my_query->have_posts()) : $my_query->the_post(); 

	$post_class = array( 'job', 'job-featured' );

	$found = true;

	$alt=$alt*-1; 

	if ($alt==1) $post_class[] = 'job-alt';
	$post_class[] = 'job-featured';
?>

	<li class="<?php echo implode(' ', $post_class); ?>"><dl>
		<dt><?php _e('Type',APP_TD); ?></dt>
		<dd class="type"><?php
			$job_types = get_terms( 'job_type', array( 'hide_empty' => '0' ) );
			if ($job_types && sizeof($job_types) > 0) {
				foreach ($job_types as $type) {
					if ( is_object_in_term( $my_query->post->ID, 'job_type', array( $type->term_id ) ) ) {
						echo '<span class="ftype '.$type->slug.'">'.$type->name.'</span>';
						break;
					}
				}
			}
		?>&nbsp;</dd>
		<dt><?php _e('Job',APP_TD); ?></dt>
		<dd class="title"><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
			<?php $company = get_post_meta( $post->ID, '_Company', true ); ?>
			<?php if ( $company ) : ?>
						
				<?php if ( $compurl = get_post_meta( $my_query->post->ID, '_CompanyURL', true ) ) { ?>
					<a href="<?php echo esc_url( $compurl ); ?>" rel="nofollow"><?php echo wptexturize( $company ); ?></a>
				<?php } else { ?>
					<?php echo wptexturize( $company ); ?>
				<?php } ?>
				
				<?php 
					$author = get_user_by('id', $my_query->post->post_author);
					if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) echo sprintf( __(' &ndash; Posted by <a href="%s">%s</a>', APP_TD), $link, $author->display_name );
				?> 
			
			<?php else : ?>
			
				<?php 
					$author = get_user_by('id', $my_query->post->post_author);
					if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) echo sprintf( __('<a href="%s">%s</a>', APP_TD), $link, $author->display_name );
				?> 
			
			<?php endif; ?>
			
		</dd>
		<dt><?php _e('Location', APP_TD); ?></dt>
		<dd class="location"><strong><?php if ($address = get_post_meta($my_query->post->ID, 'geo_short_address', true)) echo wptexturize($address); else _e('Anywhere',APP_TD); ?></strong> <?php echo wptexturize(get_post_meta($my_query->post->ID, 'geo_short_address_country', true)); ?></dd>
		<dt><?php _e('Date Posted',APP_TD); ?></dt>
		<dd class="date"><strong><?php echo date_i18n(__('j M',APP_TD), strtotime($my_query->post->post_date)); ?></strong> <span class="year"><?php echo date_i18n(__('Y',APP_TD), strtotime($my_query->post->post_date)); ?></span></dd>
	</dl></li>
	
<?php 
	endwhile; 
		echo '</ol></div><!-- End section -->';
	endif; 
	
	// Prevents empty list
	if ( ! empty($found) ) {
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
	} else {
		ob_end_clean();
	}
