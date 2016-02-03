<?php get_header();
	/* Template Name: Media*/ ?>
<div class="container media">

	<div class="row">
	<div class="col-sm-12">
		<h3>Photo Gallery</h3> <hr>
	</div>

	<?php  
		$photo_galleries = new WP_Query(array('category' => 'photo-gallery')); if($photo_galleries->have_posts()):
		while($photo_galleries->have_posts()): $photo_galleries->the_post(); ?>
				
			<a href="<?php echo get_permalink(); ?>">
			<div class="col-sm-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4><?php the_title(); ?></h4>
					</div>

					<div class="panel-body">
						<?php the_post_thumbnail(); ?>
					</div>
				</div> <!-- panel root -->
			</div> </a>

	<?php endwhile;	endif; wp_reset_postdata(); ?> </div>

<!-- 	<div class="row">

<div class="col-sm-12">
	<h3>Video Gallery</h3> <hr>
</div>


<?php  
	$video_galleries = new WP_Query(array('category' => 'video-gallery')); if($video_galleries->have_posts()):
	while($video_galleries->have_posts()): $video_galleries->the_post(); ?>
			
		<a href="<?php echo get_permalink(); ?>">
		<div class="col-sm-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4><?php the_title(); ?></h4>
				</div>

				<div class="panel-body">
					<?php the_post_thumbnail(); ?>
				</div>
			</div> panel root
		</div> </a>

<?php endwhile;	endif; wp_reset_postdata(); ?> </div> -->
		<hr>
	<div class="row">

		<!-- =============================================================== 
			 PRESS RELEASE
		     =============================================================== -->
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Press Releases</h4>
				</div>

				<div class="panel-body">
					<div class="list-group">
						<?php $press_release = new WP_Query(array('post_type' => 'press_release')); ?>
						<?php if($press_release->have_posts()): ?>
							<?php while($press_release->have_posts()): $press_release->the_post(); ?>
								<a class="list-group-item" href="<?php echo get_field('press_release')['url']; ?>">
								<span class="glyphicon glyphicon-download"></span>
								<?php the_title(); ?><span class="pull-right text-danger"><?php echo get_field('date'); ?></span></a>
						<?php endwhile; endif; wp_reset_postdata(); ?>
					</div>
				</div> <!-- panel-body -->
			</div> <!-- panel root -->
		</div> <!-- end press release -->

		<!-- =============================================================== 
			 ACTS
		     =============================================================== -->
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Acts</h4>
				</div>

				<div class="panel-body">
					<div class="list-group">
						<?php $act = new WP_Query(array('post_type' => 'act')); ?>
						<?php if($act->have_posts()): ?>
							<?php while($act->have_posts()): $act->the_post(); ?>
								<a class="list-group-item" href="<?php echo get_field('act')['url']; ?>">
								<span class="glyphicon glyphicon-download"></span>
								<?php the_title(); ?><span class="pull-right text-danger"><?php echo get_field('date'); ?></span></a>
						<?php endwhile; endif; wp_reset_postdata(); ?>
					</div>
				</div> <!-- panel-body -->
			</div> <!-- panel root -->
		</div> <!-- end acts -->
	</div> <!-- end row -->

	<div class="row">
		<!-- =============================================================== 
			 REPORTS
		     =============================================================== -->
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Reports</h4>
				</div>

				<div class="panel-body">
					<div class="list-group">
						<?php $report = new WP_Query(array('post_type' => 'report')); ?>
						<?php if($report->have_posts()): ?>
							<?php while($report->have_posts()): $report->the_post(); ?>
								<a class="list-group-item" href="<?php echo get_field('report')['url']; ?>">
								<span class="glyphicon glyphicon-download"></span>
								<?php the_title(); ?><span class="pull-right text-danger"><?php echo get_field('date'); ?></span></a>
						<?php endwhile; endif; wp_reset_postdata(); ?>
					</div>		
				</div>
			</div> <!-- panel root -->
		</div> <!-- end press release -->

		<!-- =============================================================== 
			 TENDERS
		     =============================================================== -->
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Tenders</h4>
				</div>

				<div class="panel-body">
					<div class="list-group">
						<?php $tender = new WP_Query(array('post_type' => 'tender')); ?>
						<?php if($tender->have_posts()): ?>
							<?php while($tender->have_posts()): $tender->the_post(); ?>
								<a class="list-group-item" href="<?php echo get_field('tender')['url']; ?>">
								<span class="glyphicon glyphicon-download"></span>
								<?php the_title(); ?><span class="pull-right text-danger"><?php echo get_field('date'); ?></span></a>
						<?php endwhile; endif; wp_reset_postdata(); ?>
					</div>
				</div>
			</div> <!-- panel root -->
		</div> <!-- end acts -->
	</div> <!-- end row -->

</div> <!-- end container -->
<?php get_footer(); ?>