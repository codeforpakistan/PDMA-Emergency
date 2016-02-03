<?php get_header(); /* Template Name: Front */?>

<!-- =============================================================== 
	 CONTENT SECTION
     =============================================================== -->
<div class="container">
	<?php 
		if(have_posts()):
			while(have_posts()): the_post();
				the_content();
			endwhile;
		endif;
	?>
</div> <!-- .container -->


<!-- =============================================================== 
	 NEWS AND EVENTS
     =============================================================== -->

<section class="news-events">
	<div class="container">
		<div class="row">

			<!-- =============================================================== 
				 NEWS PANEL
			     =============================================================== -->
			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading">NEWS
						<p class="pull-right navigators">
							<a href="#"><span class="glyphicon glyphicon-chevron-left"></span></a>
							<a href="#"><span class="glyphicon glyphicon-chevron-right"></span></a>
						</p>
					</div>
					<div class="panel-body">
						<div class="news"> <?php
								$news = new WP_Query(array('post_type' => 'news'));
								if($news->have_posts()):
									while ($news->have_posts()): $news->the_post(); ?>
										<div class="row">
											<div class="col-sm-3 no-padding-right adjust-margin-right">
												<div class="thumbnail">
													<img src="<?php echo get_field('image')['sizes']['large']; ?>" alt="News">
												</div>
											</div>
											<div class="col-sm-9">
												<h5><?php echo the_title();?></h5>
												<?php echo get_field('body'); ?> <br>
												<span class="date"><?php echo get_field('news_date'); ?></span>
											</div>
										</div> <!-- row -->
							<?php	endwhile; endif; wp_reset_postdata(); ?>
						</div> <!-- news -->
					</div>
				</div>
			</div> <!-- end news panel -->

			<!-- =============================================================== 
				 EVENTS PANEL
			     =============================================================== -->

			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading">EVENTS 
						<p class="pull-right navigators">
							<a href="#"><span class="glyphicon glyphicon-chevron-left"></span></a>
							<a href="#"><span class="glyphicon glyphicon-chevron-right"></span></a>
						</p>
					</div>
					<div class="panel-body">

					<!-- =============================================================== 
						 ADD EVENTS
					     =============================================================== -->
					<div class="event">
					<?php  
						$events = new WP_Query(array('post_type' => 'event'));
						if($events->have_posts()):
							while ($events->have_posts()): $events->the_post(); ?>
								<div class="row">
									<div class="col-sm-3 no-padding-right adjust-margin-right">
										<div class="thumbnail">
											<img src="<?php echo get_field('image')['sizes']['large']; ?>" alt="News">
										</div>
									</div>
									<div class="col-sm-9">
										<h5><?php echo the_title(); ?></h5>
										<?php echo get_field('body'); ?> <br>
										<span class="date"><?php echo get_field('event_date'); ?></span>
									</div>
								</div> <!-- row -->
					<?php	endwhile; endif; wp_reset_postdata();  ?>
						</div> <!-- event -->
					</div>
				</div>
			</div> <!-- end events panel -->
		
		</div> 
	</div> <!-- .container -->
</section>  <!-- .news-events -->

<!-- =============================================================== 
	 RECENT UPDATES, PROVINCIAL EMERGENCY, RECONSTRUCTION
     =============================================================== -->

<section class="up-em-re">
	<div class="container">
		<div class="row">

			<div class="col-sm-8">

				<!-- =============================================================== 
					 PEOC
				     =============================================================== -->
				<div class="panel panel-default">
					<h3 title="Provincial Emergency Operation Center">
						PROVINCIAL EMERGENCY OPERATION CENTER (PEOC)
					</h3> <hr>
					<div class="panel-body">

						<div class="row">
							<div class="col-sm-5">
									<a href="#">
							<img  style="width: 100%, height='175px' " src="http://www.pdma.gov.pk/sites/default/files/styles/style900_325/public/peoc2.jpg?itok=UibGplo1">
						</a>
							</div>
							<div class="col-sm-7">
								PEOC is aimed  bridge for timely and accurate coordination 
						between provincial government line departments and aimed  bridge for timely and accurate coordination 
						between provincial government line departments and  ... <a href="#">read more</a>
							</div>

						</div>
					

						
					</div>
				</div>
			</div> <!-- col-sm-4 -->

			<div class="col-sm-4">
				<!-- =============================================================== 
					 RECENT UPDATES
				     =============================================================== -->
				<div class="panel panel-default">
					<h3>RECENT UPDATES</h3>
					<hr>
					<div class="panel-body">
						<div class="list-group">
						  <a href="#" class="list-group-item">Cras justo odio</a>
						  <a href="#" class="list-group-item">Dapibus ac facilisis in</a>
						  <a href="#" class="list-group-item">Morbi leo risus</a>
						  <a href="#" class="list-group-item">Porta ac consectetur ac</a>
						  <a href="#" class="list-group-item">Vestibulum at eros</a>
						</div>
					</div>
				</div>
			</div> <!-- col-sm-4 -->

		</div>

		<div class="row">
			<div class="col-sm-4">
				<!-- =============================================================== 
					 E-SERVICES CORNER
				     =============================================================== -->
				<div class="panel panel-default e-services">
						<h3>E-SERVICES CORNER</h3> <hr>
					<div class="panel-body">

					<?php  
						$e_service = new WP_Query(array('post_type' => 'eservice'));
						if($e_service->have_posts()):
							while ($e_service->have_posts()) : $e_service->the_post(); ?>
								<div class="row">
									<div class="col-sm-4 col-sm-offset-2">
										<span><a href="<?php echo get_field('url'); ?>">
											<?php echo the_title(); ?>
										</a></span>
									</div>
									<div class="col-sm-3">
										<img src="<?php echo get_field('image')['sizes']['large']; ?>">
									</div>
								</div>
					<?php endwhile; endif; wp_reset_postdata(); ?>

					</div>
				</div>  <!-- E Services Corner -->

			</div>

			<div class="col-sm-8">
				<div class="panel panel-default">
					<h3>HUMANTARIAN RESPONSE FACILITY ( HRF)</h3>
					<hr>
					<div class="panel-body">
						Lorem ipsum dolor sit amet, consectetur adip
					</div>
				</div>
			</div> <!-- col-sm-4 -->

		</div> <!-- .row -->
	</div> <!-- .container -->
	
</section> <!-- updates-emergency-reconstruction -->



<section class="gcdc">
	<div class="container">
		<div class="row">
			<!-- =============================================================== 
				 GENDER AND CHILD CELL
			     =============================================================== -->
			<div class="col-sm-4">
				<div class="panel panel-default">
					<h3>GENDER &amp; CHILD CELL</h3> <hr>
					<div class="panel-body">
						
						PDMA, Khyber Pakhtunkhwa has to deal with numerous crises since its inception. 
					The children and women are the.. 
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="panel panel-default">
					<h3>CAMP MANAGEMENT</h3> <hr>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="panel panel-default">
					<h3>OPERATION &amp; COORDINATION</h3> <hr>
				</div>
			</div>
		</div>
	</div>
</section> <!-- .gcdc -->

<section class="nvm">
	<div class="row">
		<!-- =============================================================== 
			 NATURAL DISASTERS
		     =============================================================== -->

		     
	</div> <!-- .row -->
</section> <!-- .nvm -->


<!-- =============================================================== 
	 FOOTER SECTION
     =============================================================== -->
<?php get_footer(); ?>