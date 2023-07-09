<?php
//session_start();
//$_SESSION['test_id'] = 'mysession';
 global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object; 
 //var_dump($public_handler);
 ?>

<div class="directorypress-content directorypress-submit-block">
	<?php directorypress_renderMessages(); ?>
	
	<div class="directorypress-submit-section-adv pricing-plan-<?php echo esc_attr($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pricing_plan_style']); ?>">
		<?php $max_columns_in_row = $public_handler->args['columns']; ?>
		<?php $packages_counter = count($public_handler->packages); ?>
		<?php if ($packages_counter > $max_columns_in_row) $packages_counter = $max_columns_in_row; ?>
		<?php $cols_width = floor(12/$packages_counter); ?>
		<?php $cols_width_percents = (100-1)/$packages_counter; ?>

		<?php $counter = 0; ?>
		<?php $tcounter = 0; ?>
		<?php
			$packages = $public_handler->packages;
			
		?>
		<?php foreach ($packages AS $package): ?>
		<?php $tcounter++; ?>
		<?php if ($counter == 0): ?>
		
		<div class="row" style="text-align: center;">
		<?php endif; ?>
			<div class="col-md-<?php echo esc_attr($cols_width); ?> col-sm-6 col-xs-12 directorypress-plan-column  <?php if($package->featured_package && ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pricing_plan_style'] == 'pplan-style-3' || $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pricing_plan_style'] == 'pplan-style-4')): ?> feature-plan-col <?php endif; ?>">
				<div class="directorypress-panel directorypress-panel-default directorypress-text-center directorypress-choose-plan <?php if($package->featured_package && ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pricing_plan_style'] == 'pplan-style-3' || $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pricing_plan_style'] == 'pplan-style-4')): ?> feature-plan-scale <?php endif; ?>">
					<div class="directorypress-panel-heading <?php if ($package->featured_package): ?>directorypress-has_featured<?php endif; ?>">
						<?php if ($package->featured_package && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_pricing_plan_style'] == 'pplan-style-2'): ?>
							<span class="popular-package"><?php _e('most popular', 'classiadspro'); ?></span>	
						<?php endif; ?>
						<?php echo pacz_directorypress_package_duration($package); ?>
						<h3>
							<?php echo esc_html($package->name); ?>
							<?php if ($package->description): echo '<i class="fas fa-angle-down"></i>'; endif; ?>
						</h3>
						<?php if ($package->description): ?>
							<div class="directorypress-package_description" style="display:none;">
								<?php echo esc_html($package->description); ?>
							</div>
						<?php endif; ?>
						<?php do_action('directorypress_submitlisting_package_name', $package); ?>
						
						<?php if ($directorypress_object->listings_packages->submitlisting_package_message($package, $public_handler->template_args['directorytype'])): ?>
							<div class="directorypress-choose-plan-package-number">
								<?php echo wp_kses_post($directorypress_object->listings_packages->submitlisting_package_message($package, $public_handler->template_args['directorytype'])); ?>
							</div>
						<?php elseif ($package->number_of_listings_in_package > 1): ?>
						<div class="directorypress-choose-plan-package-number">
							<?php printf(__("for <strong>%d</strong> %s in the package", "classiadspro"), $package->number_of_listings_in_package, _n($public_handler->template_args['directorytype']->single, $public_handler->template_args['directorytype']->plural, $package->number_of_listings_in_package, 'classiadspro')); ?>
						</div>
						<?php endif; ?>
					</div>
					<?php echo apply_filters('directorypress_submitlisting_package_price', null, $package); ?>
					<?php if (!empty($directorypress_object->submit_pages_all)): ?>
						<?php if(in_array($package->id, directorypress_package_purchase_limit($packages))): ?>
							<div class="directorypress-list-group-item pp-button" data-toggle="tooltip" title="<?php echo esc_attr__('You have already submitted maximum listings in this package.', 'classiadspro'); ?>">
								<a href="#" class="btn pricing-button disabled"><?php echo esc_html__('Select This Plan', 'classiadspro'); ?></a>
							</div>
						<?php else: ?>
							<div class="directorypress-list-group-item pp-button">
								<a data-package-id="<?php echo esc_attr($package->id); ?>" data-directorytype="<?php echo esc_attr($public_handler->template_args['directorytype']->id); ?>" href="<?php echo directorypress_submitUrl(array('package' => $package->id, 'directorytype' => $public_handler->template_args['directorytype']->id)); ?>" class="pricing-button"><?php echo esc_html__('Select This Plan', 'classiadspro'); ?></a>	
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<ul class="directorypress-list-group">
						<?php do_action('directorypress_submitlisting_packages_rows_before', $package, '<li class="directorypress-list-group-item pp-price">', '</li>'); ?>
						<?php dpfl_renderTemplate(array(DPFL_TEMPLATES_PATH, 'package_details.php'), array('args' => $public_handler->args, 'package' => $package)); ?>
						<?php do_action('directorypress_submitlisting_packages_rows_after', $package, '<li class="directorypress-list-group-item directorypress-choose-plan-option">', '</li>'); ?>
						
					</ul>
				</div>          
			</div>

		<?php $counter++; ?>
		<?php if ($counter == $max_columns_in_row || $tcounter == $packages_counter || $tcounter == count($public_handler->packages)): ?>
		</div>
		<?php endif; ?>
		<?php if ($counter == $max_columns_in_row) $counter = 0; ?>
		<?php endforeach; ?>
	</div>
</div>
