<?php

function totd_uninstall() {
	$uninstall=true;
	if (!delete_option('totd_options')) {
		$uninstall=false;
	}
}

function totd_is_plugin_page() {
	if($_REQUEST['page']==TOTD_PLUGIN_NAME) return true;
	if($_REQUEST['option_page']=='totd_options') return true;
}


function totd_settings_init(){
	if(!totd_is_plugin_page()) return false;

	$options = totd_get_option();

	register_setting( 'totd_options', 'totd_options', 'totd_options_validate' );
	if (!$options['donated']) {
		add_settings_section('totd_options_donate', __('Donate','totd'), 'totd_section_donate_text', 'totd_options');
		add_settings_field('donate',false, 'totd_option_donate_text', 'totd_options', 'totd_options_donate');
	}
	
	add_settings_section('totd_options_main', __('Plugin Options','totd'), 'totd_section_main_text', 'totd_options');
	
	add_settings_field('hidable', __('Hide Tips Definitely','totp'), 'totd_option_hidable_text', 'totd_options', 'totd_options_main');

	//add_settings_section('totd_options_display', __('Display','totd'), 'totd_section_display_text', 'totd_options');

	add_settings_section('totd_options_system', __('System','totd'), 'totd_section_system_text', 'totd_options');
	
	add_settings_field('reset', __('Reset Options','totd'), 'totd_option_reset_text', 'totd_options', 'totd_options_system');
	add_settings_field('uninstall', __('Uninstall plugin','totd'), 'totd_option_uninstall_text', 'totd_options', 'totd_options_system');


	do_action('totd_settings_init');
	
}


function totd_options_validate($options) {

	//UNINSTALL
	if ($options['uninstall']) {
		unset($options['uninstall']);
		if (!totd_uninstall()) {
			$message=sprintf(__( "There were errors while trying to uninstall the plugin.", 'totd' ));
			add_settings_error('totd_options','uninstall',$message,'error');
		}
		return false;
	}

	//RESET
	if ($options['reset']) {
		unset($options['reset']);
		$options = totd_get_default_settings();
		return $options;
	}
	return $options;
}



function totd_section_donate_text(){
	totd_admin_paypal_form();
}

function totd_option_donate_text() {
	?>
	<input name="totd_options[donated]" type="checkbox"/>
	<label for="totd_options[donated]"><?php _e('I have donated to help contribute for the development of this plugin.', 'totd'); ?></label>
	<?php
}

function totd_admin_paypal_form() {
?>
<table style="background-color:#FFE991; border:none; margin: -5px 0;" width="600">
	<tr>
		<td>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHRwYJKoZIhvcNAQcEoIIHODCCBzQCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYA94uZwApQRx+j99QR0Fue+JNlZBcvL3T9oqyFtL2K0pFU2K5RySVfz47r0qi9TaacbHwljTEovi4ANfS/e2D4UI/xRZZM/ddRUJf9a7FnA68VhqNCnTS8yU+POEBjPOsEojuNQ6d3nwytOkEXz4Iw7bpOMDeQo7zdTQuqZ+/FaPzELMAkGBSsOAwIaBQAwgcQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIP40AqmMOG4yAgaCXEMmdSgR/G5lyNainrl1oyVSJcfYBmNHnQUz4SR2pDMpOoGNbW+RPRm4ADdGa36DL6kgwhXg6Equfglqu7yQYKFb6z6gPPEkiuYxKUr0WaC0qD+ruGJz6NHKzpyOvHt9FQNaJLN7QSrmOGIRu+uteP108a3EyRR687Rf/sDjWVLTZMKPQeuemWKbxeWHTVk8WJ51nS1TxBkTIoUFuzz63oIIDhzCCA4MwggLsoAMCAQICAQAwDQYJKoZIhvcNAQEFBQAwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMB4XDTA0MDIxMzEwMTMxNVoXDTM1MDIxMzEwMTMxNVowgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBR07d/ETMS1ycjtkpkvjXZe9k+6CieLuLsPumsJ7QC1odNz3sJiCbs2wC0nLE0uLGaEtXynIgRqIddYCHx88pb5HTXv4SZeuv0Rqq4+axW9PLAAATU8w04qqjaSXgbGLP3NmohqM6bV9kZZwZLR/klDaQGo1u9uDb9lr4Yn+rBQIDAQABo4HuMIHrMB0GA1UdDgQWBBSWn3y7xm8XvVk/UtcKG+wQ1mSUazCBuwYDVR0jBIGzMIGwgBSWn3y7xm8XvVk/UtcKG+wQ1mSUa6GBlKSBkTCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQCBXzpWmoBa5e9fo6ujionW1hUhPkOBakTr3YCDjbYfvJEiv/2P+IobhOGJr85+XHhN0v4gUkEDI8r2/rNk1m0GA8HKddvTjyGw/XqXa+LSTlDYkqI8OwR8GEYj4efEtcRpRYBxV8KxAW93YDWzFGvruKnnLbDAF6VR5w/cCMn5hzGCAZowggGWAgEBMIGUMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbQIBADAJBgUrDgMCGgUAoF0wGAYJKoZIhvcNAQkDMQsGCSqGSIb3DQEHATAcBgkqhkiG9w0BCQUxDxcNMTAxMTExMTUwNzMyWjAjBgkqhkiG9w0BCQQxFgQUQmDJPRQcfziMmBUsK7Ko1rqgSFEwDQYJKoZIhvcNAQEBBQAEgYBwAk3KGLoPUaQWNFtMwNTDXs1zOskdf8t7wNRvg8+XRxh7oO4btvypbjuRzJF6Dn8fVEHVIZbx9MBISPkcCEG5XGHTayTR4LYWUgn3ZhmAo9+foF8/1wbPrxE5t1s1X/MruHxPY1d4itaT56+UdML+OlxD8N/cUFJlzFCXFpFRCQ==-----END PKCS7-----
			">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
			<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
			</form>
		</td>
			<td>
			<?php _e('I spend a lot of time working on this plugin.  If you use it, please make a donation !', 'totd'); ?>
			</td>
	</tr>
</table>
<?php
}

function totd_section_main_text() {
}

function totd_option_hidable_text() {
	$option =  totd_get_option('hidable');
	?>
	<input type="checkbox" name="totd_options[hidable]" value="1"<?php if ($option) echo " CHECKED"; ?>><?php _e('By default, a user can choose to hide definitely a tip','totd');?>
	<?php
}


function totd_section_display_text() {
}

function totd_section_system_text() {
}

function totd_option_reset_text() {
?>
	<input type="checkbox" name="totd_options[reset]">
<?php
}
function totd_option_uninstall_text() {
?>
	<input type="checkbox" name="totd_options[uninstall]">
<?php
}


function totd_options() {
	//some code here is from the Mike Challis's plugins
	?>
	<div class="wrap">
		<div id="main">
			<h2>Tip of The Day</h2>
				<p>
				<a href="<?php echo TOTD_WORDPRESS_URL;?>changelog/" target="_blank"><?php _e('Changelog', 'totd'); ?></a> |
				<a href="<?php echo TOTD_WORDPRESS_URL;?>faq/" target="_blank"><?php _e('FAQ', 'totd'); ?></a> |
				<a href="<?php echo TOTD_WORDPRESS_URL;?>" target="_blank"><?php _e('Rate This', 'totd'); ?></a> |
				<a href="<?php echo TOTD_SUPPORT_URL;?>" target="_blank"><?php _e('Support', 'totd'); ?></a> |
				<a href="<?php echo TOTD_DONATION_URL;?>" target="_blank"><?php _e('Donate', 'totd'); ?></a>
				</p>
				
				<?php
				if (function_exists('get_transient')) {
				  require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

				  // First, try to access the data, check the cache.
				  if (false === ($api = get_transient('oqp_info'))) {
					// The cache data doesn't exist or it's expired.

					$api = plugins_api('plugin_information', array('slug' => TOTD_PLUGIN_NAME ));

					if ( !is_wp_error($api) ) {
					  // cache isn't up to date, write this fresh information to it now to avoid the query for xx time.
					  $myexpire = 60 * 15; // Cache data for 15 minutes
					  set_transient('oqp_info', $api, $myexpire);
					}
				  }
				  if ( !is_wp_error($api) ) {
					  $plugins_allowedtags = array('a' => array('href' => array(), 'title' => array(), 'target' => array()),
												'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
												'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
												'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
												'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
												'img' => array('src' => array(), 'class' => array(), 'alt' => array()));
					  //Sanitize HTML
					  foreach ( (array)$api->sections as $section_name => $content )
						$api->sections[$section_name] = wp_kses($content, $plugins_allowedtags);
					  foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key )
						$api->$key = wp_kses($api->$key, $plugins_allowedtags);

					  if ( ! empty($api->downloaded) ) {
						echo sprintf(__('Downloaded %s times', 'totd'),number_format_i18n($api->downloaded));
						echo '.';
					  }
				?>
					  <?php if ( ! empty($api->rating) ) : ?>
					  <div class="star-holder" title="<?php echo esc_attr(sprintf(__('(Average rating based on %s ratings)', 'totd'),number_format_i18n($api->num_ratings))); ?>">
					  <div class="star star-rating" style="width: <?php echo esc_attr($api->rating) ?>px"></div>
					  <div class="star star5"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php printf(__('%d stars', 'totd'),'5'); ?>" /></div>
					  <div class="star star4"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php printf(__('%d stars', 'totd'),'4'); ?>" /></div>
					  <div class="star star3"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php printf(__('%d stars', 'totd'),'3'); ?>" /></div>
					  <div class="star star2"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php printf(__('%d stars', 'totd'),'2'); ?>" /></div>
					  <div class="star star1"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php printf(__('%d stars', 'totd'),'1'); ?>" /></div>
					  </div>
					  <small><?php echo sprintf(__('(Average rating based on %s ratings)', 'totd'),number_format_i18n($api->num_ratings)); ?> <a target="_blank" href="http://wordpress.org/extend/plugins/<?php echo $api->slug ?>/"> <?php _e('Rate This', 'totd') ?></a></small>
					  <?php endif;
				}// end if (function_exists('get_transient'
				  } // if ( !is_wp_error($api)

				?>
				<h3><?php _e('Usage','bprn');?></h3>
					<p>
						<ol>
							<li><?php _e('Create your tips.','totd');?></li>
							<li><?php printf(__('Add the widget %s or use the function %s.','totd'),'<strong>'.__('Tip of the Day','totd').'</strong>','<strong>totd_display_tips</strong>');?></li>
						</ol>
					</p>

				<form method="post" action="options.php">
				<?php
					settings_fields('totd_options');
					do_settings_sections('totd_options');
				?>
				<div>
				
				<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
				
			</form>

		</div>

	</div>

	<?php
}

function totd_admin_styles() {
	wp_enqueue_style('totd-admin', TOTD_PLUGIN_URL.'admin/_inc/css/style.css');
}
function totd_admin_tabs(){
	/* Register our plugin page */
	$page = add_submenu_page('edit.php?post_type=totd',__('Settings'),__('Settings'), 'manage_options', TOTD_PLUGIN_NAME, 'totd_options' );
	//add_action('admin_print_scripts-' . $page, 'totd_admin_scripts');
	add_action('admin_print_styles-' . $page, 'totd_admin_styles');
}

function totd_admin_init() {
	add_action('admin_menu', 'totd_admin_tabs');
	add_action('admin_init', 'totd_settings_init');
}
add_action("init", "totd_admin_init");

?>