<?php get_header(); ?>

<div class="container">
	<?php 
		if(have_posts()): 
			while(have_posts()): the_post(); ?>
				<div class="title">Photo Gallery ( <?php echo the_title(); ?> ) </div>
		<?php		the_content();
			endwhile;
		endif;
	 ?>
</div>

<?php get_footer(); ?>