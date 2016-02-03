<?php  

	/* Template Name: DG's Portfolio */

	get_header(); ?>

	<div class="container">
	<div class="title">
		<h3>Direcotr Generals Portfolio</h3>
	</div>
<div class="row dgs_portfolio">
	<?php $p = new WP_Query(array('post_type' => 'dgs_portfolio')); 
		if($p->have_posts()): 
		while($p->have_posts()): $p->the_post(); ?>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading"><span class="text-danger"><?php echo the_title(); ?></span></div>
			<div class="panel-body"><img src="<?php echo get_field('image')['sizes']['medium']; ?>" alt=""></div>
			<div class="panel-footer"> <span class="tenure">Tenure</span> <span class="text-info"><?php echo get_field('start') ?></span>  <span class="to">to</span> <span class="text-info"><?php echo get_field('end'); ?></span></div>
		</div>
	</div>
<?php endwhile; endif;  wp_reset_postdata();?>
</div>
	
	<?php get_footer(); ?>
