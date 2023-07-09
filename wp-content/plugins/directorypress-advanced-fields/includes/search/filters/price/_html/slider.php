<?php global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object; ?>
<?php $index = $search_field->field->id . '_' . $search_form->form_id; ?>
<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
<?php $min_value = (($search_field->min_max_value['min']) ? $search_field->field->formatPriceSearch($search_field->min_max_value['min']) : __('min', 'directorypress-advanced-fields')); ?>
<?php $max_value = (($search_field->min_max_value['max']) ? $search_field->field->formatPriceSearch($search_field->min_max_value['max']) : __('max', 'directorypress-advanced-fields')); ?>
<?php elseif ($search_field->mode == 'range_slider'): ?>
<?php $min_value = ((isset($search_field->min_max_value['min']) && $search_field->min_max_value['min']) ? $search_field->field->formatPriceSearch($search_field->min_max_value['min']) : __('min', 'directorypress-advanced-fields')); ?>
<?php $max_value = ((isset($search_field->min_max_value['max']) && $search_field->min_max_value['max']) ? $search_field->field->formatPriceSearch($search_field->min_max_value['max']) : __('max', 'directorypress-advanced-fields')); ?>
<?php endif; ?>
<div class="czprice-slider search-element-col field-id-<?php echo $search_field->field->id; ?> field-type-<?php echo $search_field->field->type; ?> field-form-id-<?php echo $search_form->form_id; ?> unique-form-field-id-<?php echo $index; ?> pull-left" style=" width:<?php echo esc_attr($search_field->field_width($search_form)); ?>%; padding:0 <?php echo esc_attr($search_form->args['gap_in_fields']); ?>px;">
	<?php if(!$search_field->field->is_hide_name_on_search || $search_form->form_layout == 'vertical'){ ?>
		<div class="search-content-field-label">
			<label>
				<?php echo $search_field->field->name; ?>
				<span id="slider_label_<?php echo $index; ?>">
					<?php echo $min_value . ' - ' . $max_value; ?>
				</span>
			</label>
		</div>
	<?php } ?>
	<script>
		(function($) {
			"use strict";
		
			$(function() {
				<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
				var slider_params_<?php echo $index; ?> = ['<?php _e('min', 'directorypress-advanced-fields'); ?>', <?php echo implode(',', $search_field->min_max_options); ?>, '<?php _e('max', 'directorypress-advanced-fields'); ?>'];
				var slider_min_<?php echo $index; ?> = 0;
				var slider_max_<?php echo $index; ?> = slider_params_<?php echo $index; ?>.length-1;
				<?php elseif ($search_field->mode == 'range_slider'): ?>
				<?php
				$max_degrees = 10;
				if (($search_field->slider_step_1_max - $search_field->slider_step_1_min) > $max_degrees) {
					$sub = $search_field->slider_step_1_max - $search_field->slider_step_1_min;
					$length = $search_field->slider_step_1_min;
					for ($i=1; $i < $max_degrees; $i++) {
						$length += ($sub / $max_degrees);
						$slider_params[] = floor($length);
						
					}
				} else {
					$slider_params = range($search_field->slider_step_1_min, $search_field->slider_step_1_max);
				} ?>
				var slider_params_<?php echo $index; ?> = ['<?php _e('min', 'directorypress-advanced-fields'); ?>', <?php echo implode(',', $slider_params); ?>, '<?php _e('max', 'directorypress-advanced-fields'); ?>'];
				var slider_min_<?php echo $index; ?> = <?php echo $search_field->slider_step_1_min-1; ?>;
				var slider_max_<?php echo $index; ?> = <?php echo $search_field->slider_step_1_max+1; ?>;
				<?php endif; ?>
				$('#range_slider_<?php echo $index; ?>').slider({
					<?php if (function_exists('is_rtl') && is_rtl()): ?>
					isRTL: true,
					<?php endif; ?>
					min: slider_min_<?php echo $index; ?>,
					max: slider_max_<?php echo $index; ?>,
					range: true,
					<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
					values: [<?php echo ((($min = array_search($search_field->min_max_value['min'], $search_field->min_max_options)) !== false) ? $min+1 : 0); ?>, <?php echo ((($max = array_search($search_field->min_max_value['max'], $search_field->min_max_options)) !== false) ? $max+1 : count($search_field->min_max_options)+1); ?>],
					<?php elseif ($search_field->mode == 'range_slider'): ?>
					values: [<?php echo ((isset($search_field->min_max_value['min']) && $search_field->min_max_value['min']) ? $search_field->min_max_value['min']+1 : $search_field->slider_step_1_min-1); ?>, <?php echo ((isset($search_field->min_max_value['max']) && $search_field->min_max_value['max']) ? $search_field->min_max_value['max']+1 : $search_field->slider_step_1_max+1); ?>],
					<?php endif; ?>
					slide: function(event, ui) {
						if ((ui.values[0]) >= ui.values[1]) {
				            return false;
				        }

						<?php if ($search_field->field->symbol_position == 1): ?>
						var pre_symbol_position = '<?php echo esc_js($search_field->field->currency_symbol); ?>';
						var post_symbol_position = '';
						<?php elseif ($search_field->field->symbol_position == 2): ?>
						var pre_symbol_position = '<?php echo esc_js($search_field->field->currency_symbol); ?> ';
						var post_symbol_position = '';
						<?php elseif ($search_field->field->symbol_position == 3): ?>
						var pre_symbol_position = '';
						var post_symbol_position = '<?php echo esc_js($search_field->field->currency_symbol); ?>';
						<?php elseif ($search_field->field->symbol_position == 4): ?>
						var pre_symbol_position = '';
						var post_symbol_position = ' <?php echo esc_js($search_field->field->currency_symbol); ?>';
						<?php endif; ?>

						<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
						$('#slider_label_<?php echo $index; ?>').html(pre_symbol_position + slider_params_<?php echo $index; ?>[ui.values[0]] + post_symbol_position + ' - ' + pre_symbol_position + slider_params_<?php echo $index; ?>[ui.values[1]] + post_symbol_position);
						<?php elseif ($search_field->mode == 'range_slider'): ?>
						if (ui.values[0] == <?php echo $search_field->slider_step_1_min-1; ?>) {
							var min = '<?php _e('min', 'directorypress-advanced-fields'); ?>';
						} else {
							var min = ui.values[0];
						}
						if (ui.values[1] == <?php echo $search_field->slider_step_1_max+1; ?>) {
							var max = '<?php _e('max', 'directorypress-advanced-fields'); ?>';
						} else {
							var max = ui.values[1];
						}
	
						$('#slider_label_<?php echo $index; ?>').html(pre_symbol_position + min + post_symbol_position + ' - ' + pre_symbol_position + max + post_symbol_position);
						<?php if($search_field->field->is_hide_name_on_search): ?>
							$('.ui-slider-handle:first').html('<div class="tooltip top slider-tip"><div class="tooltip-arrow"></div><div class="tooltip-inner">' +pre_symbol_position + min + post_symbol_position + '</div></div>');
							$('.ui-slider-handle:last').html('<div class="tooltip top slider-tip"><div class="tooltip-arrow"></div><div class="tooltip-inner">' +pre_symbol_position + max + post_symbol_position + '</div></div>');
						<?php endif; ?>
						<?php endif; ?>
					},
					stop: function(event, ui) {
						if ((ui.values[0]) >= ui.values[1]) {
				            return false;
				        }

						<?php if (count($search_field->min_max_options) && $search_field->mode == 'min_max_slider'): ?>
						if (slider_params_<?php echo $index; ?>[ui.values[0]] == '<?php _e('min', 'directorypress-advanced-fields'); ?>')
							$('#field_<?php echo $index; ?>_min').val('');
						else
							$('#field_<?php echo $index; ?>_min').val(slider_params_<?php echo $index; ?>[ui.values[0]]);
						if (slider_params_<?php echo $index; ?>[ui.values[1]] == '<?php _e('max', 'directorypress-advanced-fields'); ?>')
							$('#field_<?php echo $index; ?>_max').val('').trigger("change");
						else
							$('#field_<?php echo $index; ?>_max').val(slider_params_<?php echo $index; ?>[ui.values[1]]).trigger("change");
						<?php elseif ($search_field->mode == 'range_slider'): ?>
						if (ui.values[0] == <?php echo $search_field->slider_step_1_min-1; ?>) {
							$('#field_<?php echo $index; ?>_min').val('');
						} else {
							$('#field_<?php echo $index; ?>_min').val(ui.values[0]);
						}
						if (ui.values[1] == <?php echo $search_field->slider_step_1_max+1; ?>) {
							$('#field_<?php echo $index; ?>_max').val('').trigger("change");
						} else {
							$('#field_<?php echo $index; ?>_max').val(ui.values[1]).trigger("change");
						}
						<?php endif; ?>
					}
				}).each(function() {
					$.each(slider_params_<?php echo $index; ?>, function(index, value) {
						<?php if (!is_rtl()): ?>
						var position = 'left';
						<?php else: ?>
						var position = 'right';
						<?php endif ?>
						if (index % odd_even_label == 0) {
							<?php if ($search_field->field->symbol_position == 1): ?>
							var pre_symbol_position = '<?php echo esc_js($search_field->field->currency_symbol); ?>';
							var post_symbol_position = '';
							<?php elseif ($search_field->field->symbol_position == 2): ?>
							var pre_symbol_position = '<?php echo esc_js($search_field->field->currency_symbol); ?> ';
							var post_symbol_position = '';
							<?php elseif ($search_field->field->symbol_position == 3): ?>
							var pre_symbol_position = '';
							var post_symbol_position = '<?php echo esc_js($search_field->field->currency_symbol); ?>';
							<?php elseif ($search_field->field->symbol_position == 4): ?>
							var pre_symbol_position = '';
							var post_symbol_position = ' <?php echo esc_js($search_field->field->currency_symbol); ?>';
							<?php endif; ?>
						}
					});
				});
			});
		})(jQuery);
	</script>
	<div class="search-field-content-wrapper directorypress-jquery-ui-slider">
		<div id="range_slider_<?php echo $index; ?>"></div>
		<div id="range_slider_<?php echo $index; ?>_scale" class="directorypress-range-slider-scale"></div>
		<input type="hidden" id="field_<?php echo $index; ?>_min" name="field_<?php echo $search_field->field->slug; ?>_min" value="<?php echo (($min_value == __('min', 'directorypress-advanced-fields')) ? '' : $min_value); ?>" />
		<input type="hidden" id="field_<?php echo $index; ?>_max" name="field_<?php echo $search_field->field->slug; ?>_max" value="<?php echo (($max_value == __('max', 'directorypress-advanced-fields')) ? '' : $max_value); ?>" />
	</div>
</div>