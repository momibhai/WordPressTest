<?php
$output = '';
$output .= '<div class="swiper-slide">';
$output .= '<div class="pacz-inner-grid">';
$output .= wpb_js_remove_wpautop($content);
$output .= '</div>';
$output .= '</div>';
wp_enqueue_style('pacz-slideshow');
echo '<div>'.$output.'</div>';
