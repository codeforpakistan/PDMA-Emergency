<!-- =============================================================== 
	 SITE FOOTER
     =============================================================== -->
<section class="footer">


<?php 
	$vision = $mission = "";
	$vis_mission = new WP_Query(array('post_type' => 'vision_mission') );
	if($vis_mission->have_posts()):
		while ($vis_mission->have_posts()): $vis_mission->the_post(); 
				$mission = get_field('mission');
				$vision = get_field('vision');
		endwhile; endif; wp_reset_postdata(); ?>

<!-- 	<div class="vision">
	<div class="container">
		<span>Our Vision</span> <?php echo $vision; ?>
	</div>
</div>
 -->
	<div class="container" style="margin-top: 23px;">

		<div class="col-sm-4 no-padding-left">
			<h3>VISION</h3> <hr>
			<p><?php echo $vision; ?></p>
			<h3>MISSION</h3> <hr>
			<p><?php echo $mission; ?></p>
		</div>

		<!-- =============================================================== 
			 EXTERNAL LINKS
		     =============================================================== -->
		<div class="col-sm-3 no-padding-left col-sm-offset-1">
			<h3>EXTERNAL LINKS</h3> <hr>
			<div class="list-group">
			<?php 
				$ext_links = new WP_Query(array('post_type' => 'external_link') );
				if($ext_links->have_posts()):
					while ($ext_links->have_posts()): $ext_links->the_post(); ?>
						<a href="<?php echo get_field('url'); ?>" class="list-group-item"><?php echo the_title(); ?></a>
			<?php	endwhile; endif; wp_reset_postdata(); ?>
			</div>
		</div>

		<div class="col-sm-3 no-padding-left col-sm-offset-1">
			<h3>CONTACT US</h3> <hr>

			<div class="row">
				<div class="col-sm-6">
					<h6>Exchange Numbers</h6>
						<div class="list-group">
							<a href="#" class="list-group-item">091 9211854 </a> 
							<a href="#" class="list-group-item">091 9214095</a> 
							<a href="#" class="list-group-item">091 5274340</a> 
						</div>
				</div>

				<div class="col-sm-6">
			<h6>Fax Numbers</h6>
						<div class="list-group">
				<a href="#" class="list-group-item">091 9214025</a> 
				<a href="#" class="list-group-item">091 9212167</a> 
			</div>
				</div>
			</div>
			

		
		</div>
	</div>

	<div class="container">
	<hr>
		<footer>
			<p>copyright &copy; 2015 PDMA, KPK</p>
		</footer>
	</div>
</section>
<?php wp_footer(); ?>
</body>
</html>