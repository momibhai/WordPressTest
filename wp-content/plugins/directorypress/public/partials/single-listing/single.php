
<?php
    // Check if the form is submitted
    if (isset($_POST['mybutton'])) {
        $price = $_POST['price'];
        $description = $_POST['description'];
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];

        // Validate input fields
        if (!empty($price) && !empty($description) && !empty($image_name)) {
            // Set the target directory to store the uploaded image
            $target_dir = 'wp-content/uploads/image/';	
            $target_file = $target_dir . basename($image_name);

            // Move the uploaded file to the target directory
            if (move_uploaded_file($image_tmp, $target_file)) {
                echo "Image uploaded successfully.";

                // Connect to the database (replace with your database credentials)
                $conn = new mysqli("localhost", "root", "", "classiads");

                // Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Prepare the INSERT statement
                $sql = "INSERT INTO buyer_offer (Product_id, Buyer_id, Price, Image, Description) VALUES ('1', '1', '$price', ' $image_name', '$description')";

                // Execute the INSERT statement
                if ($conn->query($sql) === TRUE) {
                    echo "Record inserted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                // Close the database connection
                $conn->close();
            } else {
                echo "Error uploading the image.";
            }
        } else {
            echo "All fields are required.";
        }
    }
?>



<?php
global $wpdb, $DIRECTORYPRESS_ADIMN_SETTINGS, $current_user, $post, $directorypress_dynamic_styles;
$directorypress_styles = '';
$id = uniqid();
$field_ids = $wpdb->get_results('SELECT id, type, slug, group_id FROM '.$wpdb->prefix.'directorypress_fields');

?>		

<div class="single-listing directorypress-content-wrap">
	<?php if ($public_handler->listings):
		while ($public_handler->query->have_posts()):
		
			
			$public_handler->query->the_post();
			$listing = $public_handler->listings[get_the_ID()];
			$GLOBALS['listing_id'] = $public_handler->listings[get_the_ID()];
			$authorID = get_the_author_meta( 'ID' );
			$GLOBALS['authorID2'] = $authorID;
			$GLOBALS['hash'] = $public_handler;
			$single_style_class = 'default';
			$custom_layout = 0;
			?>
			<?php if($custom_layout): ?>
				<?php 
				do_action('directorypress_vc_css_fix', 3722);
				echo do_shortcode(get_post_field('post_content', 3722)); 
				
				?>
			<?php else: ?>
			<div id="<?php echo esc_attr($listing->post->post_name); ?>" itemscope itemtype="http://schema.org/LocalBusiness">
				<article id="post-<?php the_ID(); ?>" class="directorypress-listing directorypress-single-content-area <?php echo esc_attr($single_style_class); ?>">
					<?php do_action('directorypress-breadcrumb', $listing, $public_handler); ?>
					<div class="listing-main-content clearfix">
						<div class="listing-metas-single clearfix">	
							<?php do_action('single-listing-date-published', $listing); ?>		
							<?php do_action('single-listing-views', $listing); ?>
							<?php do_action('single-listing-id', $listing); ?>
						</div>
						<?php do_action('single-listing-title', $listing); ?>
						<div class="single-listing-btns clearfix">
							<ul>
								<?php 
										if ( is_user_logged_in() && $current_user->ID == $listing->post->post_author){
											echo '<li>';
												do_action('directorypress-edit-listing-button', $listing->post->ID, true, 2);
											echo '</li>';
										}
										
										do_action('directorypress_listing_buttons_list_pre', $listing->post->ID, true, 2);
										if(isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_report_button']) && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_report_button']){
											echo '<li>';
												do_action('single-listing-report', $listing, true, 2);
											echo '</li>';
										}
										if($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pdf_button']){
											echo '<li>';
												do_action('single-listing-pdf', $listing, true, 2);
											echo '</li>';
										}
										if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_print_button']){
											echo '<li>';
												do_action('single-listing-print', $listing, true, 2);
											echo '</li>';
										}
										if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_favourites_list']){
											echo '<li>';
												do_action('single-listing-bookmark', $listing, true, 2);
											echo '</li>';
										}
										if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_share_buttons']['enabled']){
											echo '<li>';
												do_action('single-listing-share', $listing, true, 2);
											echo '</li>';
										}
										
										do_action('directorypress_listing_buttons_list_post', $listing->post->ID, true, 2);
										
									?>
							</ul>
						</div>
					</div>	
					
					<?php do_action('single-listing-slider', $listing, true); ?>
					<div class="directorypress-single-listing-text-content-wrap">
						<?php do_action('directorypress_listing_pre_content_html', $listing); ?>
						<?php
							$has_field = 0;
							foreach( $field_ids as $field_id ) {
								if($field_id->group_id == 0){
									$singlefield_id = $field_id->id;	
									if (isset($listing->fields[$singlefield_id]) && $listing->fields[$singlefield_id]->is_field_not_empty($listing)){
										$has_field = 1;
									}
								}
							}
							if($has_field){
								echo '<div class="single-filed-wrapper clearfix">';
									$listing->display_content_fields(true);
								echo '</div>';
							}
						?>
						<?php $listing->display_content_fields_ingroup(true); ?>
						<?php do_action('directorypress_listing_post_content_html', $listing); ?>
					</div>
					
					
					<?php $hash = $public_handler->hash; ?>
					<?php do_action('single-listing-tabs', $listing, $hash); ?>
					<?php do_action('single-listing-videos', $listing); ?>
					<!-- Php -->
					

					
								<!-- Custom UI  -->

 		<!-- <div style="width: 370px; background-color: #f1f1f1; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); padding: 20px; margin-left: 800px; ">
        <img src="" alt="Product Image" style="width: 100%; max-height: 300px; object-fit: cover; margin-bottom: 10px;">
        <h2 style="font-size: 24px; margin-bottom: 10px;">Product Price: $123</h2>
        <p style="font-size: 16px; margin-bottom: 20px;">Product Description Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
    </div>  -->


	<?php
    // Connect to the database (replace with your database credentials)
    $conn = new mysqli("localhost", "root", "", "classiads");

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the data from the database
    $sql = "SELECT * FROM buyer_offer LIMIT 3";
    $result = $conn->query($sql);

    // Get the URL for the 'image' directory in WordPress
	$image_base_url = home_url();

	// Construct the image URL
	$image_url = $image_base_url . '/wp-content/uploads/image/';
	
	// echo $image_url;


    // Loop through the fetched data and display it
    while ($row = $result->fetch_assoc()) {
    $image = $image_url . trim($row['image']);
        $price = $row['Price'];
        $description = $row['Description'];
?>
<div style="width: 370px; background-color: #f1f1f1; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); padding: 20px; margin-left: 800px;">
    <img src="<?php echo $image; ?>" alt="Product Image" style="width: 100%; max-height: 300px; object-fit: cover; margin-bottom: 10px;">
    <h2 style="font-size: 24px; margin-bottom: 10px;">Product Price: $<?php echo $price; ?></h2>
    <p style="font-size: 16px; margin-bottom: 20px;"><?php echo $description; ?></p>
</div>
<?php
    }

    // Close the database connection
    $conn->close();
?>

								
	

<!-- Form -->
<h1 style="margin-top:20px">Seller Offer</h1>
<form style="margin-top:50px" action="" method="post" enctype="multipart/form-data">
    <label for="price">Product Price:</label>
    <input type="number" name="price" id="price" required><br><br>
    
    <label for="image">Select Product Image:</label>
    <input style="margin-top:20px" type="file" name="image" id="image" required><br><br>
    
    <label for="description">Product Description:</label><br><br>
    <textarea name="description" id="description" cols="40" rows="5" required></textarea><br><br>
    
    <input style="background-color:blue; color:white" type="submit" value="Send Now" name="mybutton">
</form>






					<?php do_action('single-listing-map', $listing, $hash); ?>					
					<?php do_action('single-listing-review-form', $listing); ?>
					<?php do_shortcode('[dpar_list]'); ?>
					<?php do_action('dpar_review_form', $listing); ?>	
					</article>
					
				</div>
				
				<?php endif; ?>
				<?php do_action('single-listing-similar', $listing); ?>
				<?php 
					echo '<div class="directorypress-custom-popup" data-popup="single_shedule_form">';
						echo '<div class="directorypress-custom-popup-inner single-shedule">';
							echo '<div class="directorypress-popup-title">'.esc_html__('Shedule a Test Drive', 'DIRECTORYPRESS').'<a class="directorypress-custom-popup-close" data-popup-close="single_shedule_form" href="#"><i class="far fa-times-circle"></i></a></div>';
							echo '<div class="directorypress-popup-content">';
								echo do_shortcode('[dhvc_form id="2578"]');
							echo'</div>';
						echo'</div>';
					echo'</div>';
					
					echo '<div class="directorypress-custom-popup" data-popup="single_tradein_form">';
						echo '<div class="directorypress-custom-popup-inner single-tradein">';
							echo '<div class="directorypress-popup-title">'.esc_html__('Apply For TradeIn With Us', 'DIRECTORYPRESS').'<a class="directorypress-custom-popup-close" data-popup-close="single_tradein_form" href="#"><i class="far fa-times-circle"></i></a></div>';
							echo '<div class="directorypress-popup-content">';
								echo do_shortcode('[dhvc_form id="2578"]');
							echo'</div>';
						echo'</div>';
					echo'</div>';
				?>
				<?php do_action('single_listing_bidding', $listing); ?>
				<?php do_action('single_listing_contact', $listing); ?>
			<?php endwhile; endif; ?>
</div>