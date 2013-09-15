
<div id="topNav">

	<div class="inner">

		<?php wp_nav_menu( array( 'theme_location' => 'top', 'sort_column' => 'menu_order', 'container' => 'menu-header', 'fallback_cb' => 'default_top_nav' ) ); ?>

		<div class="clear"></div>

	</div><!-- end inner -->

</div><!-- end topNav -->

<div id="header">

	<div class="inner">

		<div class="logo_wrap">

			<?php if (is_front_page()) { ?><h1 id="logo"><?php } else { ?><div id="logo"><?php } ?>

			<?php if (get_option('jr_use_logo') != 'no') { ?>

					<?php if (get_option('jr_logo_url')) { ?>

						<a href="<?php echo esc_url( home_url() ); ?>"><img class="logo" src="<?php echo esc_url( get_option('jr_logo_url') ); ?>" alt="<?php esc_attr( bloginfo('name') ); ?>" /></a>

					<?php } else { ?>

							<a href="<?php echo esc_url( home_url() ); ?>"><img class="logo" src="<?php echo esc_url( get_template_directory_uri()  ); ?>/images/logo.png" alt="<?php esc_attr( bloginfo('name') ); ?>" /></a>

					<?php } ?>

			<?php } else { ?>

				<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo('name'); ?></a> <small><?php bloginfo('description'); ?></small>

			<?php } ?>

			<?php if (is_front_page()) { ?></h1><?php } else { ?></div><?php } ?>

			<?php if (get_option('jr_enable_header_banner')=='yes') : ?>
				<div id="headerAd"><?php echo stripslashes(get_option('jr_header_banner')); ?></div>
			<?php else : ?>
				<div id="mainNav"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => '', 'depth' => 1, 'fallback_cb' => 'default_primary_nav' ) );?></div>
			<?php endif; ?>

			<div class="clear"></div>

		</div><!-- end logo_wrap -->

	</div><!-- end inner -->

</div><!-- end header -->
