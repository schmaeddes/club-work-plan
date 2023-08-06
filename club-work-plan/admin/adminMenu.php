<?php

include(plugin_dir_path(__DIR__) . 'admin/eventsMenu.php');
include(plugin_dir_path(__DIR__) . 'admin/dutiesMenu.php');
include(plugin_dir_path(__DIR__) . 'admin/editEvent.php');
include(plugin_dir_path(__DIR__) . 'admin/editDuty.php');

include(plugin_dir_path(__DIR__) . 'includes/EventsTable.php');
include(plugin_dir_path(__DIR__) . 'includes/DutysTable.php');

add_action('admin_menu', 'add_workplan_setup_menu');
function add_workplan_setup_menu() {
    add_menu_page('Club Workplan', 'Club Workplan', 'manage_options', 'club-workplan', 'club_workplan');
}

add_action('admin_post_create-event', 'submit_create_event');
function submit_create_event() {
    global $wpdb;
    $newEventName = $_POST['new_event_name'];
    $newEventDescription = $_POST['new_event_description'];
    $newEventDate = $_POST['new_event_date'];
    $data = array('event_name' => $newEventName, 'event_description' => $newEventDescription, 'date_of_event' => $newEventDate);
    $wpdb->insert($wpdb->prefix . 'cwp_events', $data);

    wp_safe_redirect(esc_url(site_url('/wp-admin/admin.php?page=club-workplan')));
}

function club_workplan() {
    if (isset($_GET['action']) && isset($_GET['eventID']) && $_GET['action'] == "edit_event") {
        cwp_event_edit();
    } else if (isset($_GET['action']) && isset($_GET['duty']) && $_GET['action'] == "edit_duty") {
        cwp_duty_edit();
    } else if (isset($_GET['eventID'])) {
        cwp_duties_view();
    } else {
        cwp_create_event();
    }
}
