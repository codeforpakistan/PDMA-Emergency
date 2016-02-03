<?php  get_header(); /* Template Name: Contacts*/?>

<div class="container generic_page">

<div class="title">
	PDMA CONTACTS
</div>

<table class="table table-striped">
	<head>
		<tr>
			<th>Name</th>
			<th>Designation</th>
			<th>Contact</th>
			<th>Email</th>
			<th>Mobile Number</th>
			<th>Division</th>
			<th>District</th>
		</tr>
	</head>
	<tbody>
			<?php  
		$contacts = new WP_Query(array('post_type' => 'contact'));
	if($contacts->have_posts()):
		while ($contacts->have_posts()) : $contacts->the_post(); ?>
	<tr>
		<td><?php the_title(); ?></td>
		<td><?php echo get_field('designation'); ?></td>
		<td><?php echo get_field('contact'); ?></td>
		<td><?php echo get_field('email'); ?></td>
		<td><?php echo get_field('mobile_no'); ?></td>
		<td><?php echo get_field('division'); ?></td>
		<td><?php echo get_field('district'); ?></td>
	</tr>
		<?php	endwhile; endif; wp_reset_postdata(); ?>
	</tbody>
</table> <!-- .table .table-striped -->
</div>

<?php get_footer(); ?>