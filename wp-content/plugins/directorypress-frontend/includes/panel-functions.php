<?php

	add_action('dashboard_panel_html', 'directorypress_dashboard_panel_html');
	function directorypress_dashboard_panel_html(){
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$is_messages_addon = directorypress_is_messages_active();
		$current_user = wp_get_current_user();
		$authorID = $current_user->ID;

		$avatar_id = get_user_meta( $authorID, 'avatar_id', true );
		$author_name = get_the_author_meta('display_name', $authorID);
		$nicename = get_the_author_meta('nicename', $authorID);
		$output = '';
		
		$output .='<div class="author-thumbnail">';
		if(!empty($avatar_id) && is_numeric($avatar_id)) {
			$author_avatar_url = wp_get_attachment_image_src( $avatar_id, 'full' ); 
			$image_src_array = $author_avatar_url[0];
			$params = array( 'width' => 50, 'height' => 50, 'crop' => true );
			$output .= '<img src="' . bfi_thumb($image_src_array, $params) . '" alt="'. esc_attr($author_name) .'" />';
		}else{ 
			$avatar_url = get_avatar_url($authorID, ['size' => '50']);
			$output .='<img src="'. esc_url($avatar_url) .'" alt="author" />';
		}
		$output .='</div>';
		

		$myaccount_page_id = get_option('woocommerce_myaccount_page_id');
		$myaccount_address_page_id = get_option( 'woocommerce_myaccount_edit_address_endpoint' );
		$myaccount_editaccount_page_id = get_option( 'woocommerce_myaccount_edit_account_endpoint' );
		$myaccount_downloads_page_id = get_option( 'woocommerce_myaccount_downloads_endpoint' );
		$myaccount_orders_page_id = get_option( 'woocommerce_myaccount_orders_endpoint' ); 
		$myaccount_payment_method_page_id =  get_option( 'woocommerce_myaccount_payment_methods_endpoint');
		
		if ( $myaccount_page_id ) {
			$myaccount_page_url = get_permalink($myaccount_page_id);
				
		}else{
			$myaccount_page_url = ''; 
		}
		if ( $myaccount_orders_page_id ) {
			$myaccount_orders_page_url = $myaccount_orders_page_id;
				
		}else{
			$myaccount_orders_page_url = ''; 
		}
		if ( $myaccount_address_page_id ) {
			$myaccount_address_page_url = $myaccount_address_page_id;
				
		}else{
			$myaccount_address_page_url = ''; 
		}

		if(is_rtl()){
			$angle = '<i class="fa fa-angle-left pull-left"></i>';
			$angleUp = '<i class="fa fa-angle-up pull-left"></i>';
		}else{
			$angle = '<i class="fa fa-angle-right pull-right"></i>';
			$angleDown = '<i class="fa fa-angle-down pull-right"></i>';
		}
		?>

		<div id="directorypress-panel-sidebar-wrapper" class="jquery-accordion-menu">
			<div class="author-section">
				<div class="author-image" style="">
				  <?php echo wp_kses_post($output); ?>
				</div>
				<div class="author-name-info">
					<span class="author-nicename"><?php echo esc_html($nicename); ?></span>
					<h6><?php echo esc_html($author_name); ?></h6>
				 <span class="author-status"><?php echo esc_html__('online', 'directorypress-frontend'); ?></span>
				</div>
			</div>
			<ul class="panel-menu-wrapper clearfix">
				<li class="">
					<a class="parent-menu-link" href="#"><i class="dicode-material-icons dicode-material-icons-layers-outline"></i><?php _e('Listings', 'directorypress-frontend'); ?></a>
					<ul class="submenu">
						<li class=""><a href="<?php echo directorypress_dashboardUrl(); ?>"><span><?php _e('All', 'directorypress-frontend'); ?></span></a></li>
						<li class=""><a href="<?php echo directorypress_dashboardUrl(array('post_status' => 'publish')); ?>"><span><?php _e('Published', 'directorypress-frontend'); ?></span></a></li>
						<li class=""><a href="<?php echo directorypress_dashboardUrl(array('post_status' => 'private')); ?>"><span><?php _e('Private', 'directorypress-frontend'); ?></span></a></li>
						<li class=""><a href="<?php echo directorypress_dashboardUrl(array('post_status' => 'pending')); ?>"><span><?php _e('Pending', 'directorypress-frontend'); ?></span></a></li>
						<li class=""><a href="<?php echo directorypress_dashboardUrl(array('post_status' => 'draft')); ?>"><span><?php _e('Expired', 'directorypress-frontend'); ?></span></a></li>
					</ul>
				</li>
				<li id="account" class="">
					<a class="parent-menu-link" href="#"><i class="dicode-material-icons dicode-material-icons-account-cog-outline"></i><span><?php _e('Accounts', 'directorypress-frontend'); ?></span></a>
					<ul class="submenu">
						<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_allow_edit_profile']): ?>
							<li class=""><a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'profile')); ?>"><span><?php _e('Edit Profile', 'directorypress-frontend'); ?></span></a></li>		
						<?php endif; ?>
						<?php if(class_exists('WooCommerce') && (isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_woocommerce_frontend_links']) && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_woocommerce_frontend_links'])): ?>
							<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
								<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
									<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</li>
				<?php if($is_messages_addon && ($DIRECTORYPRESS_ADIMN_SETTINGS['message_system'] == 'instant_messages' || $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_listing_bidding'])): ?>
					<li class="">
						<a class="parent-menu-link" href="#"><i class="dicode-material-icons dicode-material-icons-message-text-outline"></i> <span><?php echo esc_html__('Messages', 'directorypress-frontend'); ?> <span class="badge"><?php echo difp_get_user_message_count( 'unread' ); ?></span></span></a>
						<ul class="submenu">
							<li class=""><a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'messages')); ?>" data-target="messages"><i class="fa fa-message"></i><?php echo esc_html__('Inbox', 'directorypress-frontend'); ?></a></li>
						</ul> 
					</li>
				<?php endif; ?>
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_button']): ?>
					<?php echo do_action('directorypress_userpanel_listing_button'); ?>
				<?php endif; ?>
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_favourites_list']): ?>
					<li class=""><a class="parent-menu-link" href="<?php echo esc_url(directorypress_directorytype_url(array('directorypress_action' => 'myfavourites'))); ?>" target="_blank"> <i class="dicode-material-icons dicode-material-icons-heart-outline"></i>  <span><?php echo  esc_html__('Bookmarks', 'directorypress-frontend'); ?></span></a></li>
				<?php endif; ?>
				<?php do_action('directorypress_frontend_panel_menu'); ?>
				<?php if(current_user_can('administrator')): ?>
					<li class=""><a class="parent-menu-link" href="<?php echo esc_url(admin_url('/')); ?>" rel="nofollow"><i class="dicode-material-icons dicode-material-icons-wordpress"></i><span><?php echo esc_html__('WP Admin', 'directorypress-frontend'); ?></span></a></li>
				<?php endif; ?>
			</ul>
		</div>
<?php }