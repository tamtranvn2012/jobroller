<?php

function appthemes_menu_sprite_css( $icons ) {
	$sprite_url = get_template_directory_uri() . '/images/admin-menu.png';

	echo '<style type="text/css">';

	foreach ( $icons as $i => $selector ) {
		$sprite_x = 30 * $i;

		echo <<<EOB

$selector div.wp-menu-image {
	background-image: url('$sprite_url');
	background-position: -{$sprite_x}px -33px !important;
}

$selector div.wp-menu-image img {
	display: none;
}

$selector:hover div.wp-menu-image,
$selector.wp-has-current-submenu div.wp-menu-image {
	background-position: -{$sprite_x}px -1px !important;
}
EOB;
	}

	echo '</style>';
}

