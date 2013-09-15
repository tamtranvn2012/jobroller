<form method="post" action="">

<h3><?php _e( 'AppThemes API Key', 'appthemes-updater' ); ?></h3>

<p><?php printf(
	__( 'Copy the key found at %1$s and paste it in the field below:', 'appthemes-updater' ),
	'<a href="http://www.appthemes.com/api-key/" target="_blank">www.appthemes.com/api-key/</a>'
); ?></p>

<p>
<input type="text" class="regular-text" name="appthemes_key" value="<?php echo esc_attr( APP_Upgrader::get_key() ); ?>" />

<input type="submit" class="button-primary" name="appthemes_submit" value="<?php esc_attr_e( 'Save', 'appthemes-updater' ); ?>" />
</p>

</form>
