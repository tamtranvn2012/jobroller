<?php
/**
 * AppThemes common functions.
 *
 * @version 1.0
 * @author AppThemes
 *
 * DO NOT UPDATE WITHOUT UPDATING ALL OTHER THEMES!
 *
 * Add new functions to the /framework/ folder and move existing functions there as well, when you need to modify them.
 *
 */


// contains the reCaptcha anti-spam system. Called on reg pages
function appthemes_recaptcha() {
    global $app_abbr;

    // process the reCaptcha request if it's been enabled
    if ( get_option($app_abbr.'_captcha_theme') ) :
?>
        <script type="text/javascript">
        // <![CDATA[
         var RecaptchaOptions = {
            custom_translations : {
                instructions_visual : "<?php _e('Type the two words:',APP_TD) ?>",
                instructions_audio : "<?php _e('Type what you hear:',APP_TD) ?>",
                play_again : "<?php _e('Play sound again',APP_TD) ?>",
                cant_hear_this : "<?php _e('Download sound as MP3',APP_TD) ?>",
                visual_challenge : "<?php _e('Visual challenge',APP_TD) ?>",
                audio_challenge : "<?php _e('Audio challenge',APP_TD) ?>",
                refresh_btn : "<?php _e('Get two new words',APP_TD) ?>",
                help_btn : "<?php _e('Help',APP_TD) ?>",
                incorrect_try_again : "<?php _e('Incorrect. Try again.',APP_TD) ?>",
            },
            theme: "<?php echo get_option($app_abbr.'_captcha_theme') ?>",
            lang: "en",
            tabindex: 5
         };
        // ]]>
        </script>

        <p>
        <?php
        // let's call in the big boys. It's captcha time.
        require_once (TEMPLATEPATH . '/includes/lib/recaptchalib.php');
        echo recaptcha_get_html(get_option($app_abbr.'_captcha_public_key'));
        ?>
        </p>

<?php
    endif;  // end reCaptcha

}


// get the page view counters and display on the page
function appthemes_get_stats($post_id) {
	global $posts, $app_abbr;

	$daily_views = get_post_meta($post_id, $app_abbr.'_daily_count', true);
	$total_views = get_post_meta($post_id, $app_abbr.'_total_count', true);

	if(!empty($total_views) && (!empty($daily_views)))
		echo number_format($total_views) . '&nbsp;' . __('total views', APP_TD). ',&nbsp;' . number_format($daily_views) . '&nbsp;' . __('today', APP_TD);
	else
		echo __('no views yet', APP_TD);
}


// tinyMCE text editor
function appthemes_tinymce($width=540, $height=400) {
?>
<script type="text/javascript">
    <!--
    tinyMCE.init({
        mode : "exact",
        theme : "advanced",
        skin : "default",
        plugins : "media",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,cleanup,code,|,forecolor,backcolor,|,media",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        theme_advanced_resize_horizontal : false,
        content_css : "<?php echo get_stylesheet_directory_uri(); ?>/style.css",
        languages : 'en',
        disk_cache : true,
        width : "<?php echo $width; ?>",
        height : "<?php echo $height; ?>",
        // update validation status on change hack to work with jquery validation
        onchange_callback: function(editor) {
          tinyMCE.triggerSave();
          $("#" + editor.id).valid();
        }
    });
    -->
</script>

<?php
}


// give us either the uploaded profile pic, a gravatar, or a placeholder
function appthemes_get_profile_pic($author_id, $author_email, $avatar_size) {
//    if(function_exists('userphoto_exists')) {
//        if(userphoto_exists($author_id))
//			//if the size of userphoto called is less then 32px, it must be looking for the thumbnail
//			if($avatar_size <= 32)
//            	userphoto_thumbnail($author_id);
//			else
//				userphoto($author_id);
//        else
//            echo get_avatar($author_email, $avatar_size);
//      } else {
         echo get_avatar($author_email, $avatar_size);
//     }
}


// change the author url base permalink
// not using quite yet. need to
function appthemes_author_permalink() {
    global $wp_rewrite, $app_abbr;

	$author_base = trim(get_option($app_abbr.'_author_url'));

	// don't waste resources if the author base hasn't been customized
	// MAKE SURE TO CHECK IF VAR IS EMPTY OTHERWISE THINGS WILL BREAK
	if($author_base <> 'author') {
		$wp_rewrite->author_base = $author_base;
		$wp_rewrite->flush_rules();
	}
}

// don't load on admin pages
// if(!is_admin())
	// add_action('init', 'appthemes_author_permalink');


/**
 *
 * Helper functions
 *
 */

// round to the nearest value used in pagination
function appthemes_round($num, $tonearest) {
   return floor($num/$tonearest)*$tonearest;
}


// for the price field to make only numbers, periods, and commas
function appthemes_clean_price($string) {
    $string = preg_replace('/[^0-9.,]/', '', $string);
    return $string;
}


// error message output function
function appthemes_error_msg($error_msg) {
    $msg_string = '';
    foreach ($error_msg as $value) {
        if(!empty($value))
            $msg_string = $msg_string . '<div class="error">' . $msg_string = $value.'</div><div class="pad5"></div>';
    }
    return $msg_string;
}


// RSS blog feed for the dashboard page
function appthemes_dashboard_appthemes() {
    global $app_rss_feed;
    wp_widget_rss_output($app_rss_feed, array('items' => 10, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1));
}


// RSS twitter feed for the dashboard page
function appthemes_dashboard_twitter() {
    global $app_twitter_rss_feed;
    wp_widget_rss_output($app_twitter_rss_feed, array('items' => 5, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 0));
}


// RSS forum feed for the dashboard page
function appthemes_dashboard_forum() {
    global $app_forum_rss_feed;
    wp_widget_rss_output($app_forum_rss_feed, array('items' => 5, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1));
}


// just places the search term into a js variable for use with jquery
// not being used as of 3.0.5 b/c of js conflict with search results
function appthemes_highlight_search_term($query) {
	if(is_search() && strlen($query) > 0){
    echo '
      <script type="text/javascript">
        var search_query  = "'.$query.'";
      </script>
    ';
  }

}


// insert the first login date once the user has been created
function appthemes_first_login($user_id) {
    update_user_meta($user_id, 'last_login', gmdate('Y-m-d H:i:s'));
}


// insert the last login date for each user
function appthemes_last_login($login) {
    global $user_ID;
    $user =  get_user_by('login', $login);
    update_user_meta($user->ID, 'last_login', gmdate('Y-m-d H:i:s'));
}
add_action('wp_login','appthemes_last_login');


// get the last login date for a user
function appthemes_get_last_login($user_id) {
    $last_login = get_user_meta($user_id, 'last_login', true);
    $date_format = get_option('date_format') . ' ' . get_option('time_format');
    $the_last_login = mysql2date($date_format, $last_login, false);
    echo $the_last_login;
}


// format the user registration date used in the sidebar-user.php template
function appthemes_get_reg_date($reg_date) {
    $date_format = get_option('date_format') . ' ' . get_option('time_format');
    $the_reg_date = mysql2date($date_format, $reg_date, false);
    echo $the_reg_date;
}


// add or remove upload file types
function appthemes_custom_upload_mimes ($existing_mimes=array()) {

// add your ext =&gt; mime to the array
    //$existing_mimes['extension'] = 'mime/type';

    //unset( $existing_mimes['exe'] );

    return $existing_mimes;
}
// add_filter('upload_mimes', 'appthemes_custom_upload_mimes');


// suggest terms on search results
// based off the Search Suggest plugin by Joost de Valk
function appthemes_search_suggest($full = true) {
    global $yahooappid, $s;

    require_once(ABSPATH . 'wp-includes/class-snoopy.php');
    $yahooappid = '3uiRXEzV34EzyTK7mz8RgdQABoMFswanQj_7q15.wFx_N4fv8_RPdxkD5cn89qc-';
    $query 	= "http://search.yahooapis.com/WebSearchService/V1/spellingSuggestion?appid=$yahooappid&query=".$s."&output=php";
    $wpurl 	= site_url();
    $snoopy = new Snoopy;

    $snoopy->fetch($query);
    $resultset = unserialize($snoopy->results);
    if (isset($resultset['ResultSet']['Result'])) {
        if (is_string($resultset['ResultSet']['Result'])) {
            $output = '<a href="'.$wpurl.'?s='.urlencode($resultset['ResultSet']['Result']).'" rel="nofollow">'.$resultset['ResultSet']['Result'].'</a>';
        } else {
            foreach ($resultset['ResultSet']['Result'] as $result) {
                $output .= '<a href="'.$wpurl.'?s='.urlencode($result).'" rel="nofollow">'.$result.'</a>, ';
            }
        }
        if ($full) {
            echo __('Perhaps you meant', APP_TD).'<strong> '.$output.'</strong>?';
        } else {
            return __('Perhaps you meant', APP_TD).'<strong> '.$output.'</strong>?';
        }
    } else {
        return false;
    }
}


// deletes all the theme database tables
function appthemes_delete_db_tables() {
    global $wpdb, $app_db_tables;

	echo '<div class="update-msg">';
    foreach ($app_db_tables as $key => $value) :

        $sql = "DROP TABLE IF EXISTS ". $wpdb->prefix . $value;
        $wpdb->query($sql);

        printf('<div class="delete-item">'.__("Table '%s' has been deleted.", APP_TD). '</div>', $value);

    endforeach;
	echo '</div';

}

// deletes all the theme database options
function appthemes_delete_all_options() {
    global $wpdb, $app_abbr;

    $sql = "DELETE FROM ". $wpdb->options
         ." WHERE option_name LIKE '".$app_abbr."_%'";
	$wpdb->query($sql);

	echo '<div class="update-msg">';
    echo '<div class="delete-item">'. __('All theme options have been deleted.', APP_TD). '</div>';
	echo '</div';
}
