<?php

class JR_Blog_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-blog.php', __( 'Blog', APP_TD ) );

		add_action( 'appthemes_before_blog_post_content', array($this, 'blog_featured_image') );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	public function blog_featured_image() {
		if ( has_post_thumbnail() ) {
			echo html('a', array(
				'href' => get_permalink(),
				'title' => the_title_attribute(array('echo'=>0)),
				), get_the_post_thumbnail( get_the_ID(), array( 420, 150 ), array( 'class' => 'alignleft' ) ) );
		}
	}
}

class JR_Contact_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-contact.php', __( 'Contact', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

}

class JR_User_Profile_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-profile.php', __( 'My Profile', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

}

class JR_Dashboard_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-dashboard.php', __( 'My Dashboard', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

}

class JR_Date_Archive_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-jobs-by-date.php', __( 'Job Date Archive', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

}

class JR_Terms_Conditions_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-terms-conditions.php', __( 'Terms & Conditions', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

}

class JR_Resume_Edit_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-edit-resume.php', __( 'Edit Resume', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {

		if ( 'yes' == get_option('jr_html_allowed') ) {

			wp_enqueue_script(
				'tiny_mce', 
				home_url().'/wp-includes/js/tinymce/tiny_mce.js'
			);

			wp_enqueue_script(
				'tiny_mce-wp-langs-en', 
				home_url().'/wp-includes/js/tinymce/langs/wp-langs-en.js'
			);

		}

	}

}

class JR_Job_Submit_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-submit.php', __( 'Submit Job', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {

		jr_job_submit_enqueue_js();

		add_filter( 'body_class', array( $this, 'body_class' ), 99 );

	}
	
	function body_class($classes) {
		$classes[] = 'jr_job_submit';
		return $classes;
	}

}

class JR_Job_Edit_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-edit-job.php', __( 'Edit Job', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {

		jr_job_submit_enqueue_js();

		add_filter( 'body_class', array( $this, 'body_class' ), 99 );

	}

	function body_class($classes) {
		$classes[] = 'jr_job_edit';
		return $classes;
	}

}

class JR_Packs_Purchase_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-purchase-pack.php', __( 'Purchase Job Pack', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function body_class($classes) {
		$classes[] = 'jr_packs_purchase';
		return $classes;
	}

}

class JR_Resume_Plans_Purchase_Page extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-purchase-resume-subscription.php', __( 'Purchase Resume Subscription', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function body_class($classes) {
		$classes[] = 'jr_subscribe_resumes';
		return $classes;
	}

}

class JR_Date_Archive extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var( 'jobs_by_date' );
	}

	function condition() {
		return get_query_var('jobs_by_date');
	}

	function parse_query( $wp_query ) {
		$wp_query->is_archive = true;
	}
}

class JR_Job_Submit extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var('job_id');
		$wp->add_query_var('order_id');
		$wp->add_query_var('step');

		$this->handle_form();
	}

	function condition() {
		return get_query_var('pagename') && ( is_page( JR_Job_Submit_Page::get_id() ) || is_page( JR_Job_Edit_Page::get_id() ) );
	}

	function parse_query( $wp_query ) {

		if ( is_user_logged_in() && ! current_user_can('can_submit_job') ) {
			wp_safe_redirect( jr_get_dashboard_url() );
			exit();
		}

	}

	function handle_form() {
		$actions = array( 'edit-job', 'new-job', 'relist-job' );
		if ( empty($_POST['action']) || !in_array( $_POST['action'], $actions ) )
			return;

		if ( ! wp_verify_nonce( $_POST['nonce'], 'submit_job' ) ) {
			$errors = jr_get_listing_error_obj();
			$errors->add( 'submit_error', __( '<strong>ERROR</strong>: Sorry, your nonce did not verify.', APP_TD ) );
			return;
		}
	}
}

class JR_Job_Relist extends JR_Job_Submit_Page {

	function init() {
		global $wp;

		$wp->add_query_var('job_relist');
	}

	function condition() {
		return (bool) get_query_var( 'job_relist' );
	}

	function parse_query( $wp_query ) {
		$job_id = $wp_query->get( 'job_relist' );

		if ( is_user_logged_in() && ! current_user_can('can_submit_job') ) {
			wp_redirect( home_url() );
			exit();
		}

		if ( ! jr_allow_relist() )
			redirect_myjobs();

		$wp_query->set( 'job_id', $job_id );
	}

	function title_parts( $parts ) {
		return array( sprintf( __( 'Relisting "%s"', APP_TD ), get_the_title( get_query_var('job_id') ) ) );
	}

	function body_class($classes) {
		$classes[] = 'jr_job_relist';
		return $classes;
	}

}

class JR_Job_Edit extends JR_Job_Edit_Page {

	function init() {
		global $wp;

		$wp->add_query_var('job_edit');
	}

	function condition() {
		return (bool) get_query_var( 'job_edit' );
	}

	function parse_query( $wp_query ) {
		$job_id = $wp_query->get( 'job_edit' );

		if ( ! current_user_can('can_submit_job') ) {
			wp_redirect( home_url() );
			exit();
		}

		$post = get_post( $job_id );

		if ( ! current_user_can('manage_options') && $post->post_author != get_current_user_id() ) {
			wp_die( __('Sorry, you do not have permission to edit this job.', APP_TD ) );
		}

		if ( ! jr_allow_editing() ) {
			wp_die( __('Sorry, job editing is not allowed.', APP_TD ) );
		}

		$wp_query->set( 'job_id', $job_id );
	}

	function title_parts( $parts ) {
		return array( sprintf( __( 'Editing "%s"', APP_TD ), get_the_title( get_queried_object_id() ) ) );
	}

}

class JR_Packs_Purchase extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var('job_id');
		$wp->add_query_var('order_id');
		$wp->add_query_var('step');
		$wp->add_query_var('plan_type');

	}

	function condition() {
		return get_query_var('pagename') && is_page( JR_Packs_Purchase_Page::get_id() );
	}

	function parse_query( $wp_query ) {

		$wp_query->set( 'plan_type', APPTHEMES_PRICE_PLAN_PTYPE );

		if ( ! current_user_can('can_submit_job') ) {
			wp_redirect( wp_login_url( get_permalink(JR_Packs_Purchase_Page::get_id()) ) );
			exit();
		}

	}

	function title_parts( $parts ) {
		return array( __( 'Purchasing Job Pack Plan', APP_TD ) );
	}
}

class JR_Resumes_Plans_Purchase extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var('job_id');
		$wp->add_query_var('order_id');
		$wp->add_query_var('step');
		$wp->add_query_var('plan_type');

	}

	function condition() {
		return get_query_var('pagename') && is_page( JR_Resume_Plans_Purchase_Page::get_id() );
	}

	function parse_query( $wp_query ) {

		$wp_query->set( 'plan_type', APPTHEMES_RESUMES_PLAN_PTYPE );

		if ( ! jr_current_user_can_subscribe_for_resumes() ) {
			wp_redirect( home_url() );
			exit();
		}

		if ( jr_resume_valid_subscr() && ! jr_resume_valid_trial() ) {
			$errors = jr_get_listing_error_obj();
			$errors->add( 'resumes_error', __( 'You\'re already subscribed to Resumes.', APP_TD ) );
			return;
		}

	}

	function title_parts( $parts ) {
		return array( __( 'Subscribing to Resumes', APP_TD ) );
	}
}

class JR_Order_Go_Back extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var( 'referer' );

		$this->handle_form();
	}

	function condition() {
		return (bool) ( ! empty($_POST['action']) && $_POST['action'] == 'select-gateway' );
	}

	function handle_form() {

		if ( empty($_POST['action']) || $_POST['action'] != 'select-gateway' || empty($_POST['referer']) )
			return;

		if ( empty($_POST['step']) || empty($_POST['order_id']) )
			return;

		if ( ! empty($_POST['goback']) && 'select-gateway' == $_POST['action'] ) {
			$args = array( 
				'step' => intval($_POST['step'])-1,
				'order_id' => $_POST['order_id']
			);

			if ( ! empty($_POST['ID'] )) {
				$args['job_id'] = intval($_POST['ID']);

				if ( ! empty($_POST['relist'] )) {
					$args['job_relist'] = intval($_POST['ID']);
				}

			}

			wp_safe_redirect( add_query_arg( $args, $_POST['referer'] ) );
			exit();
		}

	}
}

class JR_Search extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var( 'ptype' );
		$wp->add_query_var( 'location' );
	}

	function condition() {
		return ( ( isset( $_GET['location'] ) || isset( $_GET['s'] ) ) && ! empty( $_GET['ptype'] ) && in_array( $_GET['ptype'], array( APP_POST_TYPE, APP_POST_TYPE_RESUME ) ) );
	}

	function parse_query( $wp_query ) {
		$wp_query->set( 'location', trim( get_query_var( 'location' ) ) );
		$wp_query->set( 'post_type', $_GET['ptype'] );

		$wp_query->is_home = false;
		$wp_query->is_archive = false;
		$wp_query->is_search = true;
	}
}

class JR_Job_Single extends APP_View {

	function init() {
		global $wp;
		$wp->add_query_var('updated');
		$wp->add_query_var('star');
	}

	function condition() {
		return is_singular( APP_POST_TYPE );
	}

	function notices() {
		$status = get_post_status( get_queried_object() );

		switch( $status ){
			case 'pending' :
				appthemes_display_notice( 'success-pending', __( 'This job is currently pending and must be approved by an administrator.', APP_TD ) );
				break;
			case 'draft' :
				appthemes_display_notice( 'draft-pending', __( 'This is a draft job and must be approved by an administrator.', APP_TD ) );
				break;
			default:
				break;
		}

		switch( get_query_var('updated') ){
			case 1 :
				appthemes_display_notice( 'success', __( 'The job has been successfully updated.', APP_TD ) );
				break;
		}

		switch ( get_query_var('star') ) {
			case 'true' :
				appthemes_display_notice( 'success', __( 'The job has was Starred.', APP_TD ) );
				break;
			case 'false' :
				appthemes_display_notice( 'success', __( 'The job has was un-Starred.', APP_TD ) );
				break;
		}

	}

	function template_redirect() {
		add_action( 'wp_footer', array( $this, 'script_init' ), 99 );
	}

	function script_init() {
		jr_geolocation_scripts();
?> 
		<script type="text/javascript">
			/* <![CDATA[ */
				jQuery(document).ready(function($) {

					if ( $('.notice.error').html() != undefined && $('.apply_online').html() != undefined ) {
						$('html, body').animate({ scrollTop: $(".notice.error").offset().top }, "slow");
					}

				});
			/* ]]> */
		</script>
<?php
	}
}

class JR_Lister_Dashboard extends APP_View {

	function init() {
		global $wp;

		$wp->add_query_var('tab');
		$wp->add_query_var('order_cancel');
		$wp->add_query_var('job_end');
		$wp->add_query_var('cancel');
		$wp->add_query_var('confirm');
		$wp->add_query_var('confirm_order_cancel');
		$wp->add_query_var('order_status');
	}

	function condition() {
		return get_query_var('pagename') && is_page( JR_Dashboard_Page::get_id() ) && current_user_can( 'can_submit_job' );
	}

	function parse_query( $wp_query ) {

		if ( ! current_user_can('can_submit_job') ) {
			wp_redirect( home_url() );
			exit();
		}

		if ( get_query_var('order_cancel') || get_query_var('order_status') ) {
			$wp_query->set( 'tab', 'orders' );
		}

		if ( get_query_var('order_status') ) {
			$wp_query->set( 'order_status', array_map( 'wp_strip_all_tags', get_query_var('order_status') ) );
		}

		if ( get_query_var('order_cancel') ) {

			$order = appthemes_get_order( intval(get_query_var('order_cancel')) );

			if ( get_current_user_id() != $order->get_author() ) {
				$wp_query->set( 'order_cancel_msg', -1 );
				return;
			}

			if ( APPTHEMES_ORDER_COMPLETED == $order->get_status() ) {
				$wp_query->set( 'order_cancel_msg', -2 );
				return;
			}

			if ( !empty($order) && get_query_var('confirm_order_cancel') ) {
				$order->failed();
				$wp_query->set( 'order_cancel_success', 1 );
			}

		} elseif ( get_query_var('job_end') && get_query_var('confirm') ) { 

				$job_id = intval( get_query_var('job_end') );
				$job = get_post( $job_id );

				if ( $job->ID != $job_id || $job->post_author != get_current_user_id() ) :
					$wp_query->set( 'job_action', -1 );
					return;
				endif;

				if ( get_query_var('cancel') ) {
					$order = appthemes_get_order_connected_to( $job_id );
					if ( $order && ! in_array( $order->get_status(), array( APPTHEMES_ORDER_ACTIVATED, APPTHEMES_ORDER_COMPLETED ) ) ) {
						$order->failed(); // job will be canceled with the order
					} else{
						_jr_end_job( $job_id, $cancel = true );
					}

					$wp_query->set( 'job_action', 1 );
				} else {
					_jr_end_job( $job_id );

					$wp_query->set( 'job_action', 2 );
				}
		}


	}

	function notices() {
		switch( get_query_var('order_cancel_success') ){
			case 1 :
				appthemes_display_notice( 'success', __( 'The Order was successfully canceled.', APP_TD ) );
				break;
			case -1:
				appthemes_display_notice( 'error', __( 'You do not have permission to cancel this Order.', APP_TD ) );
				break;
			case -2:
				appthemes_display_notice( 'error',  __( 'This Order cannot be canceled. It\'s already completed..', APP_TD ) );
				break;
		}

		switch( get_query_var('job_action') ){
			case -1 :
				appthemes_display_notice( 'error', __( 'Invalid action.', APP_TD ) );
				break;
			case 1 :
				appthemes_display_notice( 'success', __( 'Job listing was successfully canceled.', APP_TD ) );
				break;
			case 2 :
				appthemes_display_notice( 'success', __( 'Job listing was ended early.', APP_TD ) );
				break;
		}
	}

	function template_redirect() {
		add_action( 'wp_footer', array( $this, 'script_init' ), 99 );
	}

	function script_init() {
?>
<script type="text/javascript">
	/* <![CDATA[ */
		jQuery(function() {

			jQuery('a.delete').click(function(){
				var answer = confirm("<?php _e('Are you sure you want to cancel this job listing? This action cannot be undone.', APP_TD); ?>")
				if (answer){
					jQuery(this).attr('href', jQuery(this).attr('href') + '&confirm=true');
					return true;
				}
				else{
					return false;
				}
			});

			jQuery('a.end').click(function(){
				var answer = confirm("<?php _e('Are you sure you want to expire this job listing? This action cannot be undone.', APP_TD); ?>")
				if (answer){
					jQuery(this).attr('href', jQuery(this).attr('href') + '&confirm=true');
					return true;
				}
				else{
					return false;
				}
			});

			jQuery('a.order-cancel-link').click(function(){
				var answer = confirm("<?php _e( 'Are you sure you want to cancel this Order? This action cannot be undone.', APP_TD ); ?>")
				if (answer){
					jQuery(this).attr('href', jQuery(this).attr('href') + '&confirm_order_cancel=true');
					return true;
				}
				else{
					return false;
				}
			});

			jQuery('.myjobs ul.display_section li a').click(function(){
				jQuery('.myjobs div.myjobs_section').hide();
				jQuery(jQuery(this).attr('href')).show();
				jQuery('.myjobs ul.display_section li').removeClass('active');
				jQuery(this).parent().addClass('active');
				return false;
			});
			jQuery('.myjobs ul.display_section li a:eq(0)').click();

			// trigger the selected tab
			<?php if ( get_query_var('tab') ): ?>
					jQuery('.myjobs ul.display_section li a[href="#<?php echo get_query_var('tab'); ?>"]').trigger('click');
			<?php endif; ?>

		});
	/* ]]> */
</script>

<?php
	}
}

class JR_Contact extends APP_View {

	function init() {
		global $wp;
		$wp->add_query_var('contact_form');
		$wp->add_query_var('contact_success');
	}

	function condition() {
		return (bool) get_query_var( 'contact_form' );
	}

	function parse_query( $wp_query ) {

		$errors = $this->error_obj();

		// Form Processing Script
		if ( isset($_POST['submit-form']) ) {

			$data = $this->validate_data();
			if ( ! is_wp_error( $data ) ) {
				$result = jr_contact_site_email( $data );
				if ( ! is_wp_error( $data ) ) {
					$wp_query->set( 'contact_success', 1 );
				}
			}

		}

	}

	function validate_data() {

		$errors = $this->error_obj();

		$required = array('your_name', 'email', 'message');

		// Identify exploits
		$inpt_expl = "/(content-type|to:|bcc:|cc:|document.cookie|document.write|onclick|onload)/i";
		
		// Get post data 
		$posted = array();

		$posted['your_name'] = $_POST['your_name'];
		$posted['email'] = $_POST['email'];
		$posted['message'] = $_POST['message'];
		$posted['spam-trap'] = $_POST['honeypot'];

		$loc_keys = array( 
			'your_name' => __( 'Name', APP_TD ),
			'email' => __( 'Email', APP_TD ),
			'message' => __( 'Message', APP_TD ),
			'spam-trap' => __( 'Spam-Trap', APP_TD ),
		);

		// Clean post data & validate fields
		foreach ($posted as $key => $val) {
			$val = strip_tags(stripslashes(trim($val)));
			
			if (in_array($key, $required)) {
				if ( empty($val) ) $errors->add( 'submit_error_'.$key, sprintf( __('Required field "%s" missing', APP_TD ), $loc_keys[$key] ) );
			}

			if ( $key == 'email' && !empty($val) ) {
				if( !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $posted['email']) ) {
					$errors->add( 'submit_error_invalid_email', __( 'Invalid email address.', APP_TD ) );
				}
			}

			if ( !empty($posted['spam-trap']) ) {
				$errors->add( 'submit_error_spam', __('Possible spam: You filled the honeypot spam-trap field!', APP_TD) );
			}

			if ( preg_match($inpt_expl, $val) ) {
				$errors->add( 'submit_error_exploit', __( 'Injection Exploit Detected: It seems that you&#8217;re possibly trying to apply a header or input injection exploit in our form. If you are, please stop at once! If not, please go back and check to make sure you haven&#8217;t entered <strong>content-type</strong>, <strong>to:</strong>, <strong>bcc:</strong>, <strong>cc:</strong>, <strong>document.cookie</strong>, <strong>document.write</strong>, <strong>onclick</strong>, or <strong>onload</strong> in any of the form inputs. If you have and you&#8217;re trying to send a legitimate message, for security reasons, please find another way of communicating these terms.', APP_TD ) );
			}
		}

		// process the reCaptcha request if it's been enabled
		$errors_recaptcha = jr_validate_recaptcha('app-recaptcha-contact');
		if ( $errors_recaptcha && sizeof($errors_recaptcha)>0 ) {
			$errors->errors = array_merge( $errors->errors, $errors_recaptcha->errors );
		}

		if ( $errors->get_error_code() ) return $errors;
		return $posted;
	}

	function error_obj(){
		static $errors;

		if ( ! $errors ){
			$errors = new WP_Error();
		}
		return $errors;
	}

	function notices() {
		$errors = $this->error_obj();
		if ( $errors->get_error_code() ) {
			jr_show_errors( $errors );
		} elseif( get_query_var('contact_success') ) {
			appthemes_display_notice( 'success', __( 'Thank you. Your message has been sent.', APP_TD ) );
		}
	}
}

// views common javascript enqueue
function jr_job_submit_enqueue_js() {

		 if ( 'yes' == get_option('jr_html_allowed') ) {

				wp_enqueue_script(
					'tiny_mce',
					home_url().'/wp-includes/js/tinymce/tiny_mce.js'
				);

				wp_enqueue_script(
					'tiny_mce-wp-langs-en', 
					home_url().'/wp-includes/js/tinymce/langs/wp-langs-en.js'
				);
		}

		wp_register_script(
			'jquery-validate',
			get_template_directory_uri() . '/includes/js/jquery.validate.min.js',
			array( 'jquery' ),
			'1.9.0',
			true
		);

		wp_enqueue_script(
			'jr-job-form',
			get_template_directory_uri() . '/includes/js/job-form-scripts.js',
			array( 'jquery-validate', 'jquery-ui-sortable' ),
			JR_VERSION,
			true
		);

		wp_localize_script(
			'jr-job-form',
			'JR_i18n',
			array(
				'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
				'clear'	  		=> __( 'Clear', APP_TD ),
				'loading_img' 	=> get_template_directory_uri() . '/images/loading.gif',
				'loading_msg' 	=> __( 'Please wait, loading additional category related fields...', APP_TD ),
				'required_msg'	=> __( 'This field is required.', APP_TD ),
			)
		);

}
