<?php
function totd_get_option($option_name=false) {
	$options = get_option('totd_options');

	if ($option_name) {
		return $options[$option_name];
	}else {
		return $options;
	}

}
function totd_get_default_settings() {
	$options=array(
		'hidable'=>true
	);

	return $options;
}
function totd_set_default_settings($force=false) {
	$options_default = totd_get_default_settings();
	$current_options = totd_get_option();

	if (($current_options) && (!$force)) {
		return true;
	}
	
	if ((!$current_options) || ($force)) {
		if (update_option('totd_options', $options_default )) {
			return true;
		}
	}
}

add_action('totd_activate','totd_set_default_settings');

function totd_get_the_tips_query($args=false) {
	global $totd_posts;
	global $current_user;
	$totd_posts = new WP_Query();
	$defaults = array(
		'orderby' => 'rand',
		'showposts'=>1
	);
	
	//get excluded posts
	if (is_user_logged_in()) {
		$defaults['post__not_in'] = get_user_meta($current_user->ID, 'totd_hidden_tips', true);
	}

	if (($args) && (!is_array($args))) {
		parse_str($args, $args_arr);
		$args=http_build_query($args_arr);
	}

	if ($args) {
		//merge post__not_in args with user settings
		if (($args['post__not_in']) && ($defaults['post__not_in'])) {
			$args['post__not_in']=array_merge((array)$defaults['post__not_in'],(array)$args['post__not_in']);
			$args['post__not_in']=array_unique($args['post__not_in']);
			unset($defaults['post__not_in']);
		}
		$args = wp_parse_args( (array)$args, $defaults );
	}else {
		$args=$defaults;
	}

	$args = apply_filters('totd_the_tips_query_args',$args);

	$args['post_type']='totd';

	$totd_posts->query($args);
}

//remove polls for visitors
function totd_query_args_remove_polls($args) {
	
	if (is_user_logged_in()) return $args;
	
	$args['meta_key']='totd_question';
	$args['meta_compare']='!=';
	$args['meta_value']='yes';
	
	return $args;
	
}
add_filter('totd_the_tips_query_args','totd_query_args_remove_polls');

function totd_the_tip() {
	global $post;

	if (!$post) return false;
	$template_name = 'totd-item.php';
	$template_name=apply_filters('totd_template_name',$template_name);
	echo totd_get_template_html($template_name);
	?>
<?php
}

function totd_display_tips($args=false,$frequency=false) {
	
	//display frequency
	if ($frequency) {
		if (!(mt_rand(1, 100) <= $frequency)) return false; // 5% chance 
	}

	global $totd_posts;
	totd_get_the_tips_query($args);
	
	while ($totd_posts->have_posts()) {
		$totd_posts->the_post();
		totd_the_tip();
		
	}
}

function totd_tip_is_hidable($post=false) {

	if (!is_user_logged_in()) return false;

	if(!$post){
		global $post;
	}
	$meta = get_post_meta($post->ID,'totd_hidable',true);

	if ($meta=='yes') {
		$hidable=true;
	}
	return apply_filters('totd_tip_is_hidable',$hidable,$post);
}

function totd_do_hide_tip_forever($post_id,$user_id) {
	if (!$post_id) return false;

	//tip is hidable
	$meta = get_post_meta($post_id,'totd_hidable',true);
	if ($meta!='yes') return false;

	$old_hidden = get_user_meta($user_id, 'totd_hidden_tips', true);
	$new_hidden = $old_hidden;
	$new_hidden[]=$post_id;
	$new_hidden=array_unique($new_hidden);

	if (update_user_meta($user_id, 'totd_hidden_tips', $new_hidden,$old_hidden)){
		return true;
	}
}

function totd_tip_is_question($post_id=false) {
	if(!$post_id){
		global $post;
		$post_id=$post->ID;
	}
	$meta = get_post_meta($post->ID,'totd_question',true);
	
	if ($meta=='yes') $is_question=true;

	return apply_filters('totd_tip_is_question',$is_question,$post);
}

function totd_do_answer_tip_question($post_id,$user_id,$answer) {
	if(!$post_id) return false;
	if (!$user_id) return false;
	if(!$answer) return false;

	//check answer exists
	$answers = totd_tip_get_question_answers($post_id);
	if(!in_array($answer,$answers)) return false;
	
	if (update_user_meta($user_id, 'totd_'.$post_id.'_answer', $answer)){
		return true;
	}
}

function totd_tip_get_question_answers($post_id=false){
	if(!$post){
		global $post;
		$post_id=$post->ID;
	}
	$answers = get_post_meta($post_id,'question_answer');
	
	if (!$answers){
		$answers[]='Yes';
		$answers[]='No';
	}
	return $answers;
}

function totd_get_question_stats($post=false){
	global $wpdb;
	
	if(!$post){
		global $post;
	}
	
	$answers = totd_tip_get_question_answers($post->ID);
	foreach($answers as $answer) {
		$formatted_answers[]="'".$answer."'";
	}
	$answers_str=implode(',',$formatted_answers);
	
	$query = "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key='totd_{$post->ID}_answer' AND meta_value IN ($answers_str)";

	$usermetas = $wpdb->get_col($query);
	
	foreach($answers as $answer) {
		foreach ($usermetas as $usermeta) {
			if ($usermeta==$answer) {
				$answer_stats[$answer]++;
			}
		}
	}
	
	$total_count = count($usermetas);
	
	if (!$total_count) return false;
	
	foreach ($answer_stats as $answer=>$answer_count) {
		$answers_percents[$answer] = round(($answer_count/$total_count)*100);
	}
	
	arsort($answers_percents);

	$stats=array('count'=>$total_count,'stats'=>$answers_percents);
	
	return $stats;
	
	
}

function totd_question_get_user_answer($post=false,$user_id=false) {
	if (!$post) {
		global $post;
	}
	if (!$user_id) {
		global $current_user;
		$user_id=$current_user->ID;
	}
	return get_user_meta($user_id,'totd_'.$post->ID.'_answer',true);
}


?>