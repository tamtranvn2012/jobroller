<?php
/**
 * JobRoller Forgot Password Form
 * Function outputs the forgotten password form
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */
?>

	<div class="section">

    	<div class="section_content">

			<h1><?php _e('Password Recovery', APP_TD); ?></h1>

			<?php do_action( 'appthemes_notices' ); ?>

		    <p><?php _e('Please enter your username or email address. A new password will be emailed to you.', APP_TD) ?></p>
		    <form action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" method="post" class="main_form">

		        <p><label for="login_username"><?php _e('Username/Email', APP_TD); ?></label><input type="text" class="text" name="user_login" id="login_username" /></p>

		        <p><?php do_action('lostpassword_form'); ?><input type="submit" class="submit" name="login" value="<?php _e('Get New Password',APP_TD); ?>" /></p>

		    </form>

			<div class="clear"></div>

    	</div><!-- end section_content -->

		<div class="clear"></div>

	</div><!-- end section -->

    <div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('page'); ?>
