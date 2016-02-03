<?php get_header(); ?>

<div class="container generic_page">

	<?php  

	if(have_posts()):
		while (have_posts()) { the_post(); ?>
	<div class="title">
		<h3><?php the_title(); ?>
		</h3>
	</div>
<?php			echo the_content();
		}
	endif;
?>
</div>

<hr>


	
	<?php get_footer(); ?>
