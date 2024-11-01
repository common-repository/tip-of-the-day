<?php

function totd_ajax_get_tip() {
	$exclude_post_id=$_REQUEST['exclude_id'];
	$args['post__not_in']=(array)$exclude_post_id;
	$args['showposts']=1;

	totd_display_tips($args);
}

function totd_ajax_answer_tip_question() {
	global $current_user;
	$user_id = $current_user->ID;
	$post_id=$_REQUEST['post_id'];
	$answer = $_REQUEST['answer_value'];

	if (totd_do_answer_tip_question($post_id,$user_id,$answer)) {
		echo "true";
	}

}

function totd_ajax_hide_tip_forever() {
	global $current_user;

	$post_id=$_REQUEST['post_id'];
	$user_id=$current_user->ID;
	
	if (totd_do_hide_tip_forever($post_id,$user_id)) {
		echo "true";
	}

}

add_action( 'wp_ajax_totd_next_tip', 'totd_ajax_get_tip' );
add_action( 'wp_ajax_totd_answer_tip_question', 'totd_ajax_answer_tip_question' );
add_action( 'wp_ajax_totd_hide_tip_forever', 'totd_ajax_hide_tip_forever' );

?>