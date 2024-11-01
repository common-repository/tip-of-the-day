<?php

function totd_locate_template($template_names, $load = false, $require_once = true ) {
	if ( !is_array($template_names) )
		return '';

	$located = '';
	foreach ( $template_names as $template_name ) {
		if ( !$template_name )
			continue;

		$style_file = STYLESHEETPATH . '/' . $template_name;
		$template_file = TEMPLATEPATH . '/' . $template_name;
		$plugin_file = TOTD_PLUGIN_DIR . 'themes/' . $template_name;

		if ( file_exists($style_file)) {
			$located = $style_file;
			break;
		} else if ( file_exists($template_file) ) {
			$located = $template_file;
			break;
		} else if ( file_exists($plugin_file) ) {
			$located = $plugin_file;
			break;
		}

	}

	if ( $load && '' != $located )
		load_template( $located, $require_once );

	return $located;
}

function totd_get_template_html($file) {

	$template_names[]=$file;

	$filename = totd_locate_template($template_names,false);

    if (is_file($filename)) {

        ob_start();
		
        include $filename;
		
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
    return false;
}

function totd_theme_file_url($template_name) {
	$template_names[]=$template_name;
	$file = totd_locate_template($template_names, false );
	
	if (!$file) return false;
	$array = explode(ABSPATH,$file);
	
	$url_split = $array[1];
	if (!$url_split) return false;
	$url=get_bloginfo('wpurl').'/'.$url_split;
	return $url;
}

function totd_wp_styles() {
	$template_name=apply_filters('totd_stylesheet','_inc/css/totd-style.css');
	$file = totd_theme_file_url($template_name);
	if ($file) {
		wp_enqueue_style('totd', $file);
	}
}

function totd_wp_scripts() {
	//LIVEQUERY
	$template_name='_inc/js/jquery.livequery.js';
	$file = totd_theme_file_url($template_name);
	if ($file) {
		wp_enqueue_script( 'jquery.livequery', $file,array('jquery'), '1.0.3' );
	}

	$template_name=apply_filters('totd_scripts','_inc/js/totd-scripts.js');
	$file = totd_theme_file_url($template_name);
	if ($file) {
		wp_enqueue_script( 'totd', $file,array('jquery','jquery.livequery'), TOTD_VERSION );
	}
}

function totd_wp_head() {
	//ajax url is defined in BP but not in WP
	if (!defined( 'BP_VERSION' )) {
		?>
		<script type="text/javascript">
		<!--
		var ajaxurl = "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php";
		//--></script>
		<?php
	}
}

?>