<?php

/**
 * Add footer elements via the wp_footer hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the footer of your theme
 *
 * @since 1.0
 * @uses jr_footer_actions
 *
 */
  
// insert the google analytics tracking code in the footer
function jr_google_analytics_code() {

    echo "\n\n" . '<!-- start wp_footer -->' . "\n\n";

    if (get_option('jr_google_analytics') <> '')
        echo stripslashes(get_option('jr_google_analytics'));

    echo "\n\n" . '<!-- end wp_footer -->' . "\n\n";

}

add_action('wp_footer', 'jr_google_analytics_code');
