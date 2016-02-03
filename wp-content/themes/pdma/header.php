<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title>Pdma</title>
	<?php $home_url = get_bloginfo('url'); ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?php echo $home_url; ?>/favicon.png" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $home_url; ?>/favicon.png" type="image/x-icon" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- =============================================================== 
	 TOP NAVIGATION BAR
     =============================================================== -->
<nav class="nav-top">
	<div class="container">
		<!-- // TO DO  -->
		<span class="donate"><a href="#">Donate Now</a></span>
		<span class="helpline">
			<span class="title">Helplines</span>
			<?php  
				$helplines_str = "";
				$helplines = new WP_Query(array('post_type' => 'helpline'));
				if($helplines->have_posts()){
					while ($helplines->have_posts()) {
						$helplines->the_post();
						$helplines_str .= "<span>" . get_field("helpline_number") . "</span> <span class='meta'>,</span> ";
					}
				}
			?>
			<span class="numbers"><?php echo rtrim($helplines_str, "<span class='meta'>,</span>"); ?></span>
		</span>
<!-- =============================================================== 
	 TOP NAVIGATION MENU
     =============================================================== -->
<?php 
	$args = array(
			'menu_class' => 'list-inline pull-right top-nav-list clearfix',
			'theme_location' => 'top-nav',
			'container' => '',
		);
	wp_nav_menu($args); ?>
	</div> <!-- .container -->
</nav> <!-- .nav-top -->

<!-- =============================================================== 
	 PRIMARY NAVIGATION MENU
     =============================================================== -->
<div class="nav-primary">
	<nav class="navbar navbar-default">
	  <div class="container">
	    <div class="navbar-header">
			<a href="<?php echo $home_url; ?>">
				<img src="<?php echo get_template_directory_uri(); ?>/media/logo.png" id="logo"> 
			</a>
	    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
    </button>
	    </div>
	    <a href="<?php echo $home_url; ?>">
	    <p class="brand-text">
	    	<span class="focus">PDMA</span>
	    	<span class="province">
	    	    Khyber Pakhtunkhwa
	    	</span> <br>
	    	<span class="abbr">Provencial Disaster Management Authority</span>
	    </p>
	    </a>
	    
	    <?php 
	    	$args = array(
	    		'menu_class' => 'nav navbar-nav navbar-right',
	    		'theme_location' => 'primary',
	    		'container' => '',
	    		'walker' => new Walker_Pdma()
	    	);
	    wp_nav_menu($args); ?>

	  </div>
	</nav>	
</div>

