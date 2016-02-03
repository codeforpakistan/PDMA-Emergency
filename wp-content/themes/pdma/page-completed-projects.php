<?php get_header(); ?>

<div class="container">
	<?php 
		if(have_posts()):
			while (have_posts()): the_post(); ?>
			
			<div class="title">
				<?php echo the_title(); ?>
			</div>

			<?php the_content(); ?>
	<?php endwhile;	endif;  ?>
</div> <!-- .container -->

<?php get_footer(); ?>