<?php

require_once CWP_PLUGIN_PATH . 'admin/events_menu.php';
require_once CWP_PLUGIN_PATH . 'admin/duties_menu.php';
require_once CWP_PLUGIN_PATH . 'admin/edit_event.php';
require_once CWP_PLUGIN_PATH . 'admin/edit_duty.php';

add_action('admin_menu', 'add_workplan_setup_menu');
function add_workplan_setup_menu() {
    add_menu_page('Club Workplan', 'Club Workplan', 'manage_options', 'club-workplan', 'club_workplan');
}

function club_workplan() {
    if (isset($_GET['action']) && isset($_GET['eventID']) && $_GET['action'] == "edit_event") {
        cwp_event_edit();
    } else if (isset($_GET['action']) && isset($_GET['duty']) && $_GET['action'] == "edit_duty") {
        cwp_duty_edit();
    } else if (isset($_GET['action']) && isset($_GET['duty']) && $_GET['action'] == "delete_duty") {
        cwp_duties_view();
    } else if (!isset($_GET['action']) && isset($_GET['eventID'])) {
        cwp_duties_view();
    } else {
        cwp_create_event();
    }
}
