<?php

add_action('admin_post_update-event', 'submit_update_event');
function submit_update_event() {
    global $wpdb;
    $eventID = $_POST['event_id'];

    $newEventName = $_POST['edit_event_name'];
    $newEventDescription = $_POST['edit_event_description'];
    $newEventDate = $_POST['edit_event_date'];
    $data = array('event_name' => $newEventName, 'event_description' => $newEventDescription, 'date_of_event' => $newEventDate);
    $wpdb->update(CWP_EVENT_TABLE, $data, array('ID' => $eventID));

    //wp_redirect(esc_url_raw(site_url("/wp-admin/admin.php?page=club-workplan&eventID=" . $eventID . "&action=edit")));
    wp_safe_redirect(esc_url(site_url('/wp-admin/admin.php?page=club-workplan')));
}

function cwp_event_edit() {
    $eventID = $_GET['eventID'];
    $eventData = get_event_data($eventID);

    ?>

    <div class="wrap">
        <h1>Edit Event</h1>
        <form method="post" action="<?php echo admin_url("admin-post.php"); ?>">
            <input type="hidden" name="action" value="update-event" />
            <input type="hidden" name="event_id" value="<?php echo $eventData->id; ?>" />

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label>Event Name</label></th>
                    <td><input name="edit_event_name" type="text" value="<?php echo $eventData->name; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Description</label></th>
                    <td><input name="edit_event_description" type="text" size="40" value="<?php echo $eventData->description; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Date of event</label></th>
                    <td><input name="edit_event_date" type="text" value="<?php echo $eventData->date; ?>" /></td>
                </tr>
            </table>

            <?php submit_button(__('Update event'), 'primary', 'update-event', true); ?>
        </form>
    </div>
    <?php
}
?>