<?php

include(plugin_dir_path(__DIR__) . 'admin/eventsMenu.php');
include(plugin_dir_path(__DIR__) . 'admin/editEventMenu.php');

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

add_action('admin_post_update-event', 'submit_update_event');
function submit_update_event() {
    global $wpdb;
    $eventID = $_POST['event_id'];

    $newEventName = $_POST['edit_event_name'];
    $newEventDescription = $_POST['edit_event_description'];
    $newEventDate = $_POST['edit_event_date'];
    $data = array('event_name' => $newEventName, 'event_description' => $newEventDescription, 'date_of_event' => $newEventDate);
    $wpdb->update($wpdb->prefix . 'cwp_events', $data, array('ID' => $eventID));

    wp_redirect(esc_url_raw(site_url("/wp-admin/admin.php?page=club-workplan&eventID=" . $eventID)));
}

add_action('admin_post_create-duty', 'submit_create_duty');
function submit_create_duty() {
    global $wpdb;
    $totalPagesOfTable = $_POST['total_pages'];
    $numberOfDutys = (int)$_POST['number_of_dutys'];

    $newDutyEventID = $_POST['new_duty_event_id'];
    $newDutyName = $_POST['new_duty_name'];
    $newStartTime = $_POST['new_start_time'];
    $newEndTime = $_POST['new_end_time'];

    for ($i = 1; $i <= $numberOfDutys; $i++) {
        $data = array(
            'event_id' => $newDutyEventID,
            'duty' => $newDutyName,
            'start_time' => $newStartTime,
            'end_time' => $newEndTime
        );
        $wpdb->insert($wpdb->prefix . 'cwp_dutys', $data);
    }

    $redirectUrl = sprintf("/wp-admin/admin.php?page=%s&eventID=%s&paged=%s", "club-workplan", $newDutyEventID, $totalPagesOfTable);
    wp_redirect(esc_url_raw(site_url($redirectUrl)));
}

function club_workplan() {
    if (isset($_GET['eventID'])) {
        cwp_event_edit();
    } else {
        cwp_create_event();
    }
}
