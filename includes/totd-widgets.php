<?php
class TOTD_Widget extends WP_Widget {
	var $options=array(
		'frequency'=>100
	);

	function totd_widget() {
		$widget_ops = array( 'description' => __( 'Displays the Tip of the Day','totd') );
		$this->WP_Widget('totd_widget', __('Tip of the Day','totd'), $widget_ops);
		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action('wp_print_styles','totd_wp_styles');
			add_action('wp_print_styles','totd_wp_scripts');
			add_action('wp_head','totd_wp_head');
		}
	}
	
	function output($query_args=false,$frequency=false) {
		totd_display_tips($query_args,$frequency);
	}


	function widget( $args, $instance ) {
		extract($args);

		if ( !empty($instance['title']) )
			$title = $instance['title'];
			
		if ( !empty($instance['query_args']) )
			$query_args = $instance['query_args'];
			
		if ( !empty($instance['frequency']) )
			$frequency = $instance['frequency'];

		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
			
			self::output($query_args,$frequency);

		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['query_args'] = strip_tags(stripslashes($new_instance['query_args']));
		$instance['frequency'] = strip_tags(stripslashes($new_instance['frequency']));

		return $instance;
	}
	
	function form( $instance ) {
		if (!$instance['frequency']) 
			$instance['frequency']=$this->options['frequency'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('query_args'); ?>"><?php _e('Query args','yclads') ?> <small>(<a href="http://codex.wordpress.org/Template_Tags/query_posts#Parameters" target="_blank"><?php _e('Help','totd');?></a>)</small>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('query_args'); ?>" name="<?php echo $this->get_field_name('query_args'); ?>" value="<?php if (isset ( $instance['query_args'])) {echo esc_attr( $instance['query_args'] );} ?>" />
			<div><?php _e('Defaults','totd');?><code>'orderby=rand&showposts=1'</code></div>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('frequency'); ?>">
				<?php _e('Display frequency (%)','totd') ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('frequency'); ?>" name="<?php echo $this->get_field_name('frequency'); ?>" value="<?php if (isset ( $instance['frequency'])) {echo esc_attr( $instance['frequency'] );} ?>" />
		</p>
		<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("TOTD_Widget");'));
?>