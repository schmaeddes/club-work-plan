<?php

add_action('admin_post_update-duty', 'submit_update_duty');
function submit_update_duty() {
    global $wpdb;
    $dutyID = $_POST['duty_id'];
    $eventID = $_POST['event_id'];

    $newDutyName = $_POST['edit_duty_name'];
    $newStartTime = $_POST['edit_start_time'];
    $newEndTime = $_POST['edit_end_time'];
    $newMember = $_POST['edit_member'];
    $data = array('duty' => $newDutyName, 'start_time' => $newStartTime, 'end_time' => $newEndTime, 'member' => $newMember);
    $wpdb->update($wpdb->prefix . 'cwp_dutys', $data, array('ID' => $dutyID));

    $redirectUrl = sprintf("/wp-admin/admin.php?page=%s&eventID=%s", "club-workplan", $eventID);

    //wp_redirect(esc_url_raw(site_url("/wp-admin/admin.php?page=club-workplan&eventID=" . $eventID . "&action=edit")));
    wp_safe_redirect(site_url($redirectUrl));
}

function cwp_duty_edit() {
    $dutyID = $_GET['duty'];
    $dutyData = getDuty($dutyID);

?>

    <div class="wrap">
        <h1>Edit Duty</h1>
        <form method="post" action="<?php echo admin_url("admin-post.php"); ?>">
            <input type="hidden" name="action" value="update-duty" />
            <input type="hidden" name="duty_id" value="<?php echo $dutyData->id; ?>" />
            <input type="hidden" name="event_id" value="<?php echo $dutyData->eventID; ?>" />

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label>Duty Name</label></th>
                    <td><input name="edit_duty_name" type="text" value="<?php echo $dutyData->duty; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Start Time</label></th>
                    <td><input name="edit_start_time" type="time" value="<?php echo $dutyData->startTime; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>End Time</label></th>
                    <td><input name="edit_end_time" type="time" value="<?php echo $dutyData->endTime; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Member</label></th>
                    <td><input name="edit_member" type="text" value="<?php echo $dutyData->member; ?>" /></td>
                </tr>
            </table>

            <?php submit_button(__('Update duty'), 'primary', 'update-duty', true); ?>
        </form>
    </div>

<?php

}

?>