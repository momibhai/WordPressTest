<?php 

/**
 * @package    DirectoryPress
 * @subpackage DirectoryPress/public/single-listing
 * @author     Designinvento <developers@designinvento.net>
*/

$address = array();
foreach ($listing->locations AS $location){
	$address[] = $location->get_location();
}
$output = implode(', ', $address);
?>
<?php if($listing->locations && !empty($output)): ?>
	<div class="single-location-address">
		<i class="fas fa-map-marker-alt"></i>
		<?php echo wp_kses_post($output); ?>
	</div>
<?php endif; ?>

 