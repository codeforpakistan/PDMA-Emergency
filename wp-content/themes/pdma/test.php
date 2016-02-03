<?php  

	/* Template Name: Test */

	get_header(); ?>

	<div class="container">
	<div class="title">
		<h3>Direcotr Generals Portfolio</h3>
	</div>
<div class="row dgs_portfolio">
	<?php $p = new WP_Query(array('post_type' => 'report')); 
		if($p->have_posts()): 
		while($p->have_posts()): $p->the_post(); ?>
			<a href="<?php echo get_field('report')['url'] ?>"><?php echo the_title(); ?></a>
		
<?php endwhile; endif;  wp_reset_postdata();?>
</div>
	
	<?php get_footer(); ?>
