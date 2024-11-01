<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<div class="entry-header">
		<span class="entry-title"><?php the_title(); ?></span>
		<a href="#" class="no-js hide next_tip" href="test" title="<?php _e('Next Tip','totd');?>"><img src="<?php echo totd_theme_file_url('_inc/images/arrow_refresh_small.png');?>"/></a>
		<span class="entry-meta">
			<a href="#" class="no-js hide hide_tip" title="<?php _e('Hide Tip','totd');?>"><img src="<?php echo totd_theme_file_url('_inc/images/cross.png');?>"/></a>
			<?php if (totd_tip_is_hidable()) {?>
				<a href="#" class="no-js hide hide_tip_forever" title="<?php _e('Never show this tip again','totd');?>"><img src="<?php echo totd_theme_file_url('_inc/images/delete.png');?>"/></a>
			<?php } ?>
		</span>
		<div class="totd-loading"></div>
	</div>
	
		
		
	
	<div class="entry-summary">
		<?php the_excerpt(); ?>
		<span class="totd-buttons">
			<?php if (totd_tip_is_question()) {
				$previous_answer=totd_question_get_user_answer();
				$question_answers = totd_tip_get_question_answers();
				foreach ($question_answers as $answer) {
					unset($classes);
					if ($previous_answer==$answer) {
						$classes=' class="selected"';
					}
					?>
					<a<?php echo $classes;?> href="#" rel="<?php echo $answer?>" class="no-js hide answer_tip"><span><?php echo $answer?></span></a>
				<?php
				}
				?>
			<?php }?>
		</span>
	</div><!-- .entry-summary -->

	
</div><!-- #post-## -->