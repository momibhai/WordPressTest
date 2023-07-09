<?php if ($field->value): 
	if(!directorypress_is_listing_page()){
		$tootltip_triger_class = 'directorypress_field_tooltip';
		$tooltip_content_class = 'tooltip-content';
	}else{
		$tootltip_triger_class = '';
		$tooltip_content_class = '';
	}

?>
<div class="directorypress-field-item directorypress-field-type-<?php echo $field->type; ?>">
	<span class="field-label">
			<?php
				if(!directorypress_is_listing_page()){
					if($listing->listing_view == 'show_grid_style'){
						if($field->is_hide_name_on_grid == 'show_only_label'){
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_grid == 'show_icon_label'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_grid == 'show_only_icon'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
						}
					}elseif($listing->listing_view == 'show_list_style'){
						if($field->is_hide_name_on_list == 'show_only_label'){
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_list == 'show_icon_label'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
							echo '<span class="directorypress-field-title">'.$field->name.':</span>';
						}elseif($field->is_hide_name_on_list == 'show_only_icon'){
							if ($field->icon_image){
								echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
							}
						}
					}
				}else{
					if ($field->icon_image){
						echo '<span class="directorypress-field-icon '.$field->icon_image.'"></span>';
					}
					if(!$field->is_hide_name){
						echo '<span class="directorypress-field-title">'.$field->name.':</span>';
					}
				}
			?>
		</span>
	<div class="field-content clearfix">
		<span class="listing-status-tag" style="background: <?php echo esc_attr($field->color_codes[$field->value]); ?>"><?php echo $field->selection_items[$field->value]; ?></span>
	</div>
</div>
<?php endif; ?>