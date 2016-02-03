<?php get_header(); /* Template Name: Messages*/?>

<div class="container">
	<?php 
		if(have_posts()): 
			while(have_posts()): the_post(); ?>
				<div class="title"><?php echo the_title(); ?> </div>
				<div class="pull-right" style="    margin-left: 11px; margin-bottom: 11px;">
				<?php the_post_thumbnail(); ?>
					
				</div>
		<?php		the_content();
			endwhile;
		endif;
	 ?>
</div>

<?php get_footer(); ?>