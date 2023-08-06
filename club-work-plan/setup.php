<?php

/**
 * Add template to dropdown
 */

add_filter( 'theme_page_templates', 'add_page_template_to_dropdown' );
function add_page_template_to_dropdown($templates) {
   $templates[plugin_dir_path( __FILE__ ) . 'templates/workplanTemplate.php'] = __( 'Club Workplan', 'text-domain' );

   return $templates;
}

add_filter( 'template_include', 'change_page_template', 99 );
function change_page_template($template) {
    if (is_page()) {
        $meta = get_post_meta(get_the_ID());

        if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
            $template = $meta['_wp_page_template'][0];
        }
    }

    return $template;
}

/**
 * Add custom css
 */

add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
	wp_enqueue_style( 'your-stylesheet-name', plugins_url('css/workplan.css', __FILE__), false, '1.0.0', 'all');
}
