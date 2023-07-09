<?php directorypress_renderMessages(); ?>
<script>
	(function($) {
	"use strict";

		
		$(function() {
			$('[data-popup-open]').on('click', function(event)  {
				//alert(targeted_popup_class);
				var targeted_popup_class = $(this).attr('data-popup-open');
				$('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
				
				event.preventDefault();
			});
			$('[data-popup-close]').on('click', function(event)  {
				var targeted_popup_class = $(this).attr('data-popup-close');
				$('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
				event.preventDefault();
			});
		});
		$(function() {
			$('[data-popup-open]').on('click', function(event)  {
				//alert(targeted_popup_class);
				var targeted_package_id = $(this).attr('data-package-id');
				$('form.upgrade').prepend('<input type="hidden" name="package_id" value="'+targeted_package_id+'">');
				
				
				event.preventDefault();
			});
		});
	})(jQuery);
</script>
<div class="wrap about-wrap directorypress-admin-wrap">
	<?php DirectoryPress_Admin_Panel::listing_dashboard_header(); ?>
	<div class="directorypress-plugins directorypress-theme-browser-wrap">
		<div class="theme-browser rendered">
			<div class="directorypress-box">
				<div class="directorypress-box-head">
					<h1><?php _e('DirectoryPress Packages', 'directorypress-payment-manager'); ?></h1>
					<p><?php _e('You can create unlimited packages based on available features', 'directorypress-payment-manager'); ?></p>
					<?php echo '<a class="dp-admin-btn dp-success create" href="#" data-toggle="modal" data-target="#create_new_package">' . __('Create New', 'directorypress-payment-manager') . '</a>'; ?>
				</div>
				<div id="packages_list" class="directorypress-box-content wp-clearfix">
					<?php _e('You may order listings packages by drag & drop rows in the table.', 'directorypress-payment-manager'); ?>
					<form method="POST" action="<?php echo admin_url('admin.php?page=directorypress_packages'); ?>">
						<div class="order-response"></div>
						<input type="hidden" id="packages_order" name="packages_order" value="" />
						<div class="packages_list_wrapper"><?php echo $items_list; ?></div>
						<?php //submit_button(__('Save order', 'directorypress-payment-manager')); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="create_new_package" class="modal fade directorypress-admin-modal" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="topline"></div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary new-package-action-button"><?php echo esc_html__('Create', 'directorypress-extended-locations'); ?></button>
				<button type="button" class="btn btn-default cancel-btn" data-dismiss="modal"><?php echo esc_html__('Cancel', 'directorypress-extended-locations'); ?></button>
			</div>
		</div>
	</div>
</div>
<div id="package_configure" class="modal fade directorypress-admin-modal" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="topline"></div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default cancel-btn" data-dismiss="modal"><?php echo esc_html__('Cancel', 'directorypress-extended-locations'); ?></button>
			</div>
		</div>
	</div>
</div>