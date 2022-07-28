<?php
if (!function_exists('slider_get_placeholder_image')) {
    function slider_get_placeholder_image()
    {
        return "<img src='" . SLIDER_URL . "assets/images/default.jpg' class='img-fluid wp-post-image' />";
    }
}

if (!function_exists('slider_options')) {
    function slider_options()
    {
        $show_bullets = isset(Slider_Settings::$options['slider_bullets']) && Slider_Settings::$options['slider_bullets'] == 1 ? true : false;

        wp_enqueue_script('slider-options-js', SLIDER_URL . 'vendor/flexslider/flexslider.js', array('jquery'), SLIDER_VERSION, true);
        wp_localize_script('slider-options-js', 'SLIDER_OPTIONS', array(
            'controlNav' => $show_bullets
        ));
    }
}
