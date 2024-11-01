<?php

class TOTD_Post_Type {
	
	function totd_post_type()
	{
		// REGISTER CUSTOM POST TYPE
		//http://justintadlock.com/archives/2010/04/29/custom-post-types-in-wordpress
		register_post_type('totd', array(
			'label' => __('Tips of the Day','totd'),
			'singular_label' => __('Tip of the Day','totd'),
			//'description' => __( 'A super duper is a type of content that is the most wonderful content in the world. There are no alternatives that match how insanely creative and beautiful it is.' ),
			'public' => true,
			'show_ui' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'hierarchical'=>false,
			'query_var' => true,
			'capability_type' => 'post', //should be yclad
			/*
			'edit_cap' => 'edit_totd',
			'edit_type_cap' => 'edit_totds',
			'edit_others_cap' => 'edit_others_totds',
			'publish_cap' => 'publish_totds',
			'read_cap' => 'read_totd',
			'read_private_cap' => 'read_private_totds',
			'delete_cap' => 'delete_totd',
			*/
			'supports' => array('title', 'editor', 'author','excerpt','custom-fields'),
			'rewrite' => array( 'slug' => __('totd','yclads-slugs'), 'with_front' => false )
		));

		//tags
		register_taxonomy( 'totd_tag', 'totd', array( 
			'hierarchical' => false,
			'label' => __('Tips Tags','totd'),
			'singular_label' => __('Tip Tag','totd'), 
			'rewrite' => false
		));

		add_post_type_support( 'totd', 'post-thumbnails' );
		// Create thumbnail sizes
		add_image_size( 'totd_large', 500, 500 );
		add_image_size( 'totd_normal', 200, 200 );
		add_image_size( 'totd_thumb', 100,100 ); 


		// Admin interface init
		add_filter("manage_edit-totd_columns", array(&$this, "edit_columns"));
		
		add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
		add_action("admin_init", array(&$this, "admin_init"));

		//TO FIX check query args to fire only when needed
		add_filter('post_class', array(&$this, "post_class"),10,3);
		
		add_filter('add_menu_classes', array(&$this, "show_pending_number"), 8);
		add_filter('pub_priv_sql_capability', array(&$this, "view_cap"));

		//text before the action edition form
		add_action('totd_action_pre_add_form',array(&$this, 'totd_action_pre_add_form'),10,1);
		
		add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
		
	}
	
	function totd_action_pre_add_form($taxonomy) {
		?>
		<span style="color:red">
			<?php _e('Don\'t use nested Actions.  You don\'t need that !','yclads');?>
		</span>
		<?php
	
	}
	
	function post_class($classes, $class, $post_id) {
		global $post;
		
		if ($post->post_type!='totd') return $classes;
		
		//QUESTION
		if (totd_tip_is_question()) {
			$classes[]='totd-question';
		}
		//TAGS
		$tags = get_the_terms( $post->ID, 'totd_tag' );
		if ($tags) {
			foreach ($tags as $tag) {
				$classes[]='totd-tag-'.$tag->slug;
			}
		}
			
		return $classes;
	
	}

	/*
	Adds the pending classifieds count to the menu
	Based upon the plugin "Pending Posts Indicator" (http://www.gudlyf.com/2009/01/05/wordpress-plugin-pending-posts-indicator/);
	*/
	function show_pending_number($menu) {
	
		foreach ($menu as $key=>$menu_item) {
			if ($menu_item[0]!=__('Classified Ads','yclads')) continue;

			$num_posts = wp_count_posts( 'totd', 'readable' );
			$status = "pending";
			$pending_count = 0;
			if ( !empty($num_posts->$status) ) {
				$pending_count = $num_posts->$status;
				// Use 'plugins' classes for now. May add specific ones to this later.
				$menu[$key][0] = sprintf(__('Classified Ads %s','yclads'), "<span class='update-plugins count-$pending_count'><span class='plugin-count'>" . number_format_i18n($pending_count) . "</span></span>" );
			}
			
		}
		return $menu;
	}
	
	function view_cap($cap) {
		//used by 
		//yclads_count_items_for_users() 
		//yclads_count_items_for_user()
		//to count the posts
		//TO FIX TO CHECK. Should be custom cap.
		 $cap = 'read_private_posts';
		 return $cap;
	}


	
	function edit_columns($default_columns)
	{
		$my_columns = array(
			"cl_description" => "Description",
			"cl_tags" => __('Tags'),
			"cl_hidable" => __('Hidable','totd'),
			"cl_question" => __('Question','totd')
		);	
		
		$columns = wp_parse_args( $my_columns, $default_columns );
		return $columns;
	}
	
	function custom_columns($column)
	{

		global $post;
		switch ($column)
		{
			case "cl_description":
				the_excerpt();
				break;
			case "cl_tags":
				echo get_the_term_list( $post->ID, 'totd_tag','',',');
				break;
			case "cl_hidable":
				$hidable = get_post_meta($post->ID,'totd_hidable',true);
				if ($hidable=='yes'){
					$checked=' CHECKED';
				}				
				$checkbox = '<input type="checkbox" disabled'.$checked.'/>';
				echo $checkbox;
				break;
			case "cl_question":
				$is_question = totd_tip_is_question($post->ID);
				if ($is_question){
					$checked=' CHECKED';
				}
				$checkbox = '<input type="checkbox" disabled'.$checked.'/>';
				echo $checkbox;
				if ($question){
					$stats = totd_get_question_stats();

					if ($stats['count']>0) {
						echo"<br/>";
						foreach ($stats['stats'] as $answer=>$percent) {
							echo $answer.":".$percent."%<br/>";
						}
						echo"----<br/>";
						printf(__('%d users','totd'),$stats['count']);
					}

				}
				break;
		}
	}

	
	// When a post is inserted or updated
	function wp_insert_post($post_id, $post = null)
	{
		if ($post->post_type == "totd")
		{
			if (isset($_POST['totd-hidable'])) {
				update_post_meta($post->ID, 'totd_hidable',$_POST['totd-hidable']);
			}

			if (isset($_POST['totd-question'])) {
				update_post_meta($post->ID, 'totd_question',$_POST['totd-question']);
			}

		}
	}
	
	function admin_init() 
	{
		//Add Custom Meta Box (actions) for edit screen
		add_meta_box("totd_options", __('Tip Options','totd'), array(&$this, "meta_box_actions"), "totd", "normal", "high");
			
		

	}
	
	// Admin post meta contents
	function meta_box_actions()
	{
	
		global $wp_meta_boxes;
		global $post;
		
		
		//HIDABLE
		$hidable_meta = get_post_meta($post->ID,'totd_hidable',true);
		
		if (!$hidable_meta) {
			$option =  totd_get_option('hidable');
			if ($option) {
				$hidable_meta='yes';
			}
		}
		
		if ($hidable_meta=='yes') {
			$checked_h1=' CHECKED';
		}else{
			$checked_h0=' CHECKED';
		}

		
		?>
		<p>
		<label for="hide_tip">
			<?php _e('Logged user can disable this tip','totd');?> :
		</label>
		<input name="totd-hidable" value="yes" type="radio"<?php echo $checked_h1;?>><?php _e('Yes');?>
		<input name="totd-hidable" value="no" type="radio"<?php echo $checked_h0;?>><?php _e('No');?>
		</p>
		<?php
		
		//IS QUESTION
		
		$checked_q='';
		
		$is_question = totd_tip_is_question($post->ID);

		if ($is_question) {
			$checked_q1=' CHECKED';
		}else{
			$checked_q0=' CHECKED';
		}
		
		$answers = totd_tip_get_question_answers();
		$answers_str=implode(', ',$answers);
		
		?>
		<p>
			<label for="hide_tip">
				<?php _e('This tip is a question','totd');?> :
			</label>
			<input name="totd-question" value="yes" type="radio"<?php echo $checked_q1;?>><?php _e('Yes');?>
			<input name="totd-question" value="no" type="radio"<?php echo $checked_q0;?>><?php _e('No');?>
			<?php printf(__('(possible answers : %s).','totd'),'<em>'.$answers_str.'</em>');?>
			  <small><?php printf(__('You can customize the question answers by adding custom fields with the name %s.','totd'),'<strong>question_answer</strong>');?></small>
		</p>
		<?php
		
		do_action('totd_meta_box_options');
	}
}




function totd_post_type_init() {
	$totd_post_type_init = new TOTD_Post_Type();

}
// Initiate the class
add_action("init", "totd_post_type_init");

?>