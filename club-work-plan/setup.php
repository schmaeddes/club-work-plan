<?php

/**
 * Create custom table at activation of plugin 
 */

function create_workplan_event_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	
    $table_name = $wpdb->prefix . 'cwp_events';

    $sql = "CREATE TABLE " . $table_name . " (
	id int(11) NOT NULL AUTO_INCREMENT,
	event_name VARCHAR(100) NOT NULL,
	description VARCHAR(500) NULL,
	date_of_event datetime NOT NULL,
    date_of_creation timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_workplan_event_table');

function create_workplan_duty_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	
    $table_name = $wpdb->prefix . 'cwp_dutys';

    $sql = "CREATE TABLE " . $table_name . " (
	id int(11) NOT NULL AUTO_INCREMENT,
	event_id int(11) NOT NULL,
	duty VARCHAR(100) NOT NULL,
	start_time TIME NULL,
	end_time TIME NULL,
    member VARCHAR(100) NULL,
    date_of_entry TIMESTAMP NULL,
	PRIMARY KEY (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_workplan_duty_table');

/**
 * Add template to dropdown
 */

add_filter( 'theme_page_templates', 'add_page_template_to_dropdown' );
function add_page_template_to_dropdown($templates) {
   $templates[plugin_dir_path( __FILE__ ) . 'template.php'] = __( 'Workplan', 'text-domain' );

   return $templates;
}

add_filter( 'template_include', 'change_page_template', 99 );
function change_page_template($template)
{
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
	wp_enqueue_style( 'your-stylesheet-name', plugins_url('workplan.css', __FILE__), false, '1.0.0', 'all');
}

?>