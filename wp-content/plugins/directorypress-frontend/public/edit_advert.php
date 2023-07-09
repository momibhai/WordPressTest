<?php global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object; ?>
<div class="directorypress-submit-listing-wrap edit clearfix">
	<div class="directorypress-submit-heading">
		<h2><?php echo sprintf(esc_html__('Edit listing "%s"', 'directorypress-frontend'), $directorypress_object->current_listing->title()); ?></h2>
	</div>
	<div class="submit-listing-form-wrapper">
			<form action="" method="POST">
				<input type="hidden" name="referer" value="<?php echo esc_url($public_handler->referer); ?>" />
				<input type="hidden" name="listing_id" value="<?php echo esc_attr($directorypress_object->current_listing->post->ID); ?>" />
				<?php wp_nonce_field('directorypress_edit', '_edit_nonce'); ?>
				<div class="directorypress-submit-form-section">
					<div class="directorypress-submit-form-section-label"><?php esc_html_e('General Details', 'directorypress-frontend'); ?></div>
					<div class="directorypress-submit-form-section-content">
						<div class="field-wrap">
							<p class="directorypress-submit-section-label directorypress-submit-field-title"><?php esc_html_e('Title', 'directorypress-frontend'); ?><span class="lable label-danger"><?php echo esc_html__('*', 'directorypress-frontend') ?></span></p>
							<input type="text" name="post_title" style="width: 100%" class="form-control" value="<?php if ($directorypress_object->current_listing->post->post_title != __('Auto Draft', 'directorypress-frontend')) echo esc_attr($directorypress_object->current_listing->post->post_title); ?>" />
						</div>
								<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['message_system'] == 'email_messages' && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_custom_contact_email']): ?>
									<div class="field-wrap">
										<p class="contact-email-meta directorypress-submit-field-title">
											<?php _e('Contact email ', 'directorypress-frontend'); ?>
											<?php do_action('directorypress_listing_submit_user_info', esc_attr__('When field is empty contact messages from contact form will be sent directly to author email', 'directorypress-frontend')); ?>
										</p>
										<?php $directorypress_object->listings_handler_property->listing_contact_metabox($directorypress_object->current_listing->post); ?>
									</div>
								<?php endif; ?>
								
								<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_tags']): ?>
									<div class="field-wrap">
										<p class="directorypress-submit-section-label directorypress-submit-field-title">
											<?php echo esc_html($directorypress_object->fields->get_field_by_slug('listing_tags')->name); ?>
											<?php do_action('directorypress_listing_submit_user_info', esc_attr__('select existing tags or type new', 'directorypress-frontend')); ?>
										</p>
										<?php directorypress_tags_selectbox($directorypress_object->current_listing->post->ID); ?>
										<?php if ($directorypress_object->fields->get_field_by_slug('listing_tags')->description): ?><p class="description"><?php echo esc_html($directorypress_object->fields->get_field_by_slug('listing_tags')->description); ?></p><?php endif; ?>
									</div>
								<?php endif; ?>
								<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_status_field']): ?>
									<?php if ($directorypress_object->fields->is_this_field_slug('status')): ?>
										<?php $directorypress_object->fields_handler_property->directorypress_fields_metabox_by_slug_type('status', 'status', $directorypress_object->current_listing->post); ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if (!$directorypress_object->current_listing->package->package_no_expiry && ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_change_expiration_date'] || current_user_can('manage_options'))): ?>
									<div class="field-wrap">
										<p class="directorypress-submit-section-label directorypress-submit-field-title"><?php _e('Listing expiration date', 'directorypress-frontend'); ?></p>
										<?php $directorypress_object->listings_handler_property->listing_expiry_metabox($directorypress_object->current_listing->post); ?>
									</div>
								<?php endif; ?>
								
								<?php do_action('frontend_listing_details_before_category_metabox', $directorypress_object->current_listing); ?>
								
								<?php if ($directorypress_object->current_listing->package->category_number_allowed > 0): ?>
									<p class="directorypress-submit-section-label directorypress-submit-field-title"><?php echo esc_html($directorypress_object->fields->get_field_by_slug('categories_list')->name); ?><?php if ($directorypress_object->fields->get_field_by_slug('categories_list')->is_required): ?><span class="directorypress-red-asterisk">*</span><?php endif; ?></p>
									<?php if ($directorypress_object->fields->get_field_by_slug('categories_list')->is_multiselect): ?>
										<div class="directorypress-categories-tree-panel directorypress-editor-class" id="<?php echo esc_attr(DIRECTORYPRESS_CATEGORIES_TAX); ?>-all">
											<?php directorypress_terms_checklist($directorypress_object->current_listing,  $directorypress_object->fields->get_field_by_slug('categories_list')->allow_listing_in_parent); ?>
										</div>
									<?php else: ?>
										<?php directorypress_category_selectbox($directorypress_object->current_listing); ?>
									<?php endif; ?>
									<?php if ($directorypress_object->fields->get_field_by_slug('categories_list')->description): ?><p class="description"><?php echo esc_html($directorypress_object->fields->get_field_by_slug('categories_list')->description); ?></p><?php endif; ?>
								<?php endif; ?>
						<?php do_action('frontend_listing_details_after_category_metabox', $directorypress_object->current_listing); ?>		
					</div>
				</div>
				<div class="directorypress-submit-form-section">
					<div class="directorypress-submit-form-section-label"><?php _e('Extra Details', 'directorypress-frontend'); ?></div>
					<div class="directorypress-submit-form-section-content">
						<?php if ($directorypress_object->fields->is_this_not_core_field() && !$directorypress_object->current_listing->post->price): ?>
							<?php $directorypress_object->fields_handler_property->directorypress_fields_metabox('', '', $directorypress_object->current_listing->post); ?>
						<?php endif; ?>
						<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_social_links']): ?>
							<?php $directorypress_object->listings_handler_property->listing_social_profile_metabox($directorypress_object->current_listing->post); ?>
						<?php endif; ?>
						<?php if (post_type_supports(DIRECTORYPRESS_POST_TYPE, 'editor')): ?>
							<div class="field-wrap content-field">
								<p class="directorypress-submit-field-title">
									<?php echo esc_html($directorypress_object->fields->get_field_by_slug('content')->name); ?>
									<?php if ($directorypress_object->fields->get_field_by_slug('content')->is_required): ?>
										<span class="lable label-danger"><?php echo esc_html__('Required', 'directorypress-frontend') ?></span>
									<?php endif; ?>
									<?php wp_kses_post(do_action('directorypress_listing_submit_user_info', $directorypress_object->fields->get_field_by_slug('content')->description)); ?>
								</p>
								<?php wp_editor($directorypress_object->current_listing->post->post_content, 'post_content', array('media_buttons' => false, 'editor_class' => 'directorypress-editor-class')); ?>
							</div>
						<?php endif; ?>
						
						<?php if (post_type_supports(DIRECTORYPRESS_POST_TYPE, 'excerpt')): ?>
							<div class="field-wrap">
								<p class="directorypress-submit-field-title">
									<?php echo esc_html($directorypress_object->fields->get_field_by_slug('summary')->name); ?>
									<?php if ($directorypress_object->fields->get_field_by_slug('summary')->is_required): ?>
										<span class="lable label-danger"><?php echo esc_html__('Required', 'directorypress-frontend') ?></span>
									<?php endif; ?>
									<?php wp_kses_post(do_action('directorypress_listing_submit_user_info', $directorypress_object->fields->get_field_by_slug('summary')->description)); ?>
								</p>
								<textarea name="post_excerpt" class="directorypress-editor-class form-control" rows="4"><?php echo esc_textarea($directorypress_object->current_listing->post->post_excerpt)?></textarea>
							</div>
						<?php endif; ?>
						
						<?php echo wp_kses_post(do_action('directorypress_frontend_submission_faqs_metabox', $directorypress_object->current_listing->post)); ?>	
					</div>
				</div>
				
				<?php do_action('directorypress_create_listing_metaboxes_pre', $directorypress_object->current_listing); ?>
		
				<?php if ($directorypress_object->current_listing->package->images_allowed > 0 || $directorypress_object->current_listing->package->videos_allowed > 0): ?>
				<div class="directorypress-submit-form-section">
					<div class="directorypress-submit-form-section-label"><?php _e('Media', 'directorypress-frontend'); ?></div>
					<div class="directorypress-submit-form-section-content">
						<div class="directorypress-submit-images-wrap clearfix">
							<div class="directorypress-submit-section-inside field-wrapper clearfix">
								<?php $directorypress_object->media_handler_property->mediaMetabox(); ?>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				
				<?php if ($directorypress_object->current_listing->package->location_number_allowed > 0): ?>
					<div class="directorypress-submit-form-section">
						<div class="directorypress-submit-form-section-label"><?php _e('locations', 'directorypress-frontend'); ?><?php if ($directorypress_object->fields->get_field_by_slug('address')->is_required): ?><span class="directorypress-red-asterisk">*</span><?php endif; ?></div>
						<div class="directorypress-submit-form-section-content">
							<div class="field-wrap">
								<?php $directorypress_object->locations_handler->listing_locations_metabox($directorypress_object->current_listing->post); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php do_action('directorypress_create_listing_metaboxes_post', $directorypress_object->current_listing); ?>
				
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_recaptcha']): ?>
					<div class="field-wrap directorypress-security">
						<?php echo directorypress_has_recaptcha(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if (isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_tospage']) && !empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_tospage'])) : ?>
					<div class="field-wrap directorypress-tos">
						<div class="input-checkbox">
							<label>
								<input type="checkbox" name="directorypress_tospage" value="0" />
								<span class="checkbox-item-name">
									<?php echo esc_html__('I have read and agreed to the ', 'directorypress-frontend'); ?><a href="<?php echo esc_url(get_permalink($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_tospage'])); ?>" target="_blank"><?php echo esc_html__('Terms of Services', 'directorypress-frontend'); ?></a>
								</span>
								<span class="input-checkbox-item"></span>
							</label>
						</div>
					</div>
				<?php endif; ?>
				<?php require_once(ABSPATH . 'wp-admin/includes/template.php'); ?>
				<?php submit_button(__('Save Listing', 'directorypress-frontend'), 'submit-listing-button edit')?>
			</form>
			<div class="directorypress-notifications"></div>
	</div>
</div>