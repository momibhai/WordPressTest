<div class="wrap about-wrap directorypress-admin-wrap">
	<?php DirectoryPress_Admin_Panel::listing_dashboard_header(); ?>
	<div class="directorypress-plugins directorypress-theme-browser-wrap">
		<div class="theme-browser rendered">
			<div class="directorypress-box">
				<div class="directorypress-box-head">
					<h1><?php _e('DirectoryPress Address Settings', 'directorypress-extended-locations'); ?></h1>
					<p><?php _e('You can create unlimited Levels of Address', 'directorypress-extended-locations'); ?></p>
					<?php echo '<a class="dp-admin-btn dp-success create" data-toggle="modal" data-target="#create_new_locationlevel" href="#">' . __('Create New Location Level', 'directorypress-extended-locations') . '</a>'; ?>
				</div>
				<div id="locations_list"  class="directorypress-box-content wp-clearfix">
				<?php echo $items_list; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="create_new_locationlevel" class="modal fade directorypress-admin-modal" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="topline"></div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary new-locationlevel-action-button"><?php echo esc_html__('Create', 'directorypress-extended-locations'); ?></button>
				<button type="button" class="btn btn-default cancel-btn" data-dismiss="modal"><?php echo esc_html__('Cancel', 'directorypress-extended-locations'); ?></button>
			</div>
		</div>
	</div>
</div>
<div id="locationlevel_configure" class="modal fade directorypress-admin-modal" role="dialog">
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