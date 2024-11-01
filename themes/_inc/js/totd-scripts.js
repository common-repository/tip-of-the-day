jQuery(document).ready(function($){  

	//remove no-js classes
	totd_tip_init();
	
	//mask tip
	$('.type-totd.hentry .hide_tip').livequery('click', function(event) {  //The event of clicking on titles
		var tip=$(this).parents('.hentry');
		tip.slideUp(200);
		return false;
	});
	
	//load next tip
	$('.type-totd.hentry a.next_tip').livequery('click', function(event) { 
		var link = $(this);
		var postId=link.parents('.hentry').attr("id").replace(/^post-(.*)$/,'$1'); //Get entry ID
		
		totd_tip_loading_class(link,true);

		jQuery.post( ajaxurl, {
			action: 'totd_next_tip',
			'cookie': encodeURIComponent(document.cookie),
			'exclude_id': postId
		},
		function(response)
		{
			if (response){
				totd_tip_loading_class(link,false);
				if (response!='-1') {
					totd_tip_switch(link,response);
				}
			}
		});
		return false;
	});
	
	//answer question
	$('.type-totd.hentry a.answer_tip').livequery('click', function(event) { 
		var link = $(this);
		var postId=link.parents('.hentry').attr("id").replace(/^post-(.*)$/,'$1'); //Get entry ID
		var answer_value=link.attr("rel");

		totd_tip_loading_class(link,true);

		jQuery.post( ajaxurl, {
			action: 'totd_answer_tip_question',
			'cookie': encodeURIComponent(document.cookie),
			'answer_value': answer_value,
			'post_id':postId
		},
		function(response)
		{
			if (response){
				totd_tip_loading_class(link,false);
				if (response!='-1') {
					link.addClass('selected');
				}
			}
		});
		return false;

	});
	
	//hide tip forever
	$('.type-totd.hentry a.hide_tip_forever').livequery('click', function(event) { 
		var link = $(this);
		var tip=$(this).parents('.hentry');
		var postId=link.parents('.hentry').attr("id").replace(/^post-(.*)$/,'$1'); //Get entry ID
		totd_tip_loading_class(link,true);

		jQuery.post( ajaxurl, {
			action: 'totd_hide_tip_forever',
			'cookie': encodeURIComponent(document.cookie),
			'post_id':postId
		},
		function(response)
		{
			if (response){
				totd_tip_loading_class(link,false);
				if (response!='-1') {
					tip.slideUp(200);
				}
			}
		});
		return false;

	});
	
});

function totd_tip_init(){
	//remove no-js classes
	jQuery('.type-totd.hentry .no-js').removeClass('no-js');
}

function totd_tip_switch(el,response) {
	if (!response) return false;
	var tip = el.parents('.type-totd.hentry');
	tip.after(response);
	totd_tip_init();
	tip.remove();


	
}

function totd_tip_loading_class(el,bool) {

	var tip = el.parents('.type-totd.hentry');
	if (tip.length>0) {
		if (bool) {
			tip.addClass('loading');
		}else{
			tip.removeClass('loading');
		}
	}

}