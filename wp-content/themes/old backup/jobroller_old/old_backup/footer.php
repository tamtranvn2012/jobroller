
<?php if(is_front_page()) { ?>

<div class="jobManagement">
<ul>
	<li>
	<h2 class="setting">Customer Job Management</h2>
	<img src="<?php bloginfo (template_directory)?>/images/img1.jpg"/>
	<p>Our customers get their own personalised <u>dashboard</u> where they can view and re-list their jobs once they expire.</p>	
	</li>
	<li>
	<h2 class="mail">Email Alerts </h2>
	<img src="<?php bloginfo (template_directory)?>/images/img2.jpg"/>
	<p>Get instantly notified when a new job listing has been submittd. Our emails also include things like new add approved sent to you once your add is approved.</p>	
	</li>
	<li>
	<h2 class="seo">Search Engine Optimized (SEO)</h2>
	<img src="<?php bloginfo (template_directory)?>/images/img3.jpg"/>
	<p>We understand SEO and how important it is for you Our websites is built to instantly take advantage of SEO best Practices so all you need to worry about is adding your jobs advert.</p>	
	</li>

</ul>
<?php }
else { ?>

<?php } ?>
<div class="clear"></div>
</div>


<div id="footer">

    	<div class="inner">

			<p><?php _e('Copyright &copy;',APP_TD); ?> <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?>. </p>

		</div><!-- end inner -->

</div><!-- end footer -->
