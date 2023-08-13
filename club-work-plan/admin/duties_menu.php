<?php

require_once(CWP_PLUGIN_PATH . 'admin/classes/Duties_Table.php');

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
        $wpdb->insert(CWP_DUTY_TABLE, $data);
    }

    $redirectUrl = sprintf("/wp-admin/admin.php?page=%s&eventID=%s&paged=%s", "club-workplan", $newDutyEventID, $totalPagesOfTable);
    wp_redirect(esc_url_raw(site_url($redirectUrl)));
}

function cwp_duties_view() {
    get_edit_event_table_style();

    $eventID = $_GET['eventID'];
    $eventData = get_event_data($eventID);

    echo get_event_title($eventData);

    echo '<div class="wrap">';
    echo '<h1>Dutys</h1>';
    $table = new Duties_Table();
    $table->prepare_items($eventID);
    $table->display();
    $totalPagesOfTable = $table->_pagination_args['total_pages'];
    echo '</div>';
    echo '';

    ?>
    <div class="wrap">
        <h1>Add dutys</h1>
        <form method="post" action="<?php echo admin_url("admin-post.php"); ?>">
            <input type="hidden" name="action" value="create-duty" />
            <input type="hidden" name="new_duty_event_id" value="<?php echo $eventID; ?>" />
            <input type="hidden" name="total_pages" value="<?php echo $totalPagesOfTable; ?>" />

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label>Duty name</label></th>
                    <td><input name="new_duty_name" type="text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Start Time</label></th>
                    <td><input name="new_start_time" type="time" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>End Time</label></th>
                    <td><input name="new_end_time" type="time" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>How many?</label></th>
                    <td><input name="number_of_dutys" type="number" value="1" /></td>
                </tr>
            </table>

            <?php submit_button(__('Add duty'), 'primary', 'create-duty', true); ?>
        </form>
    </div>
<?php
}

function get_edit_event_table_style() {
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-event_id, .column-start_time, .column-end_time { width: 15%; }';
    echo '.wp-list-table .column-duty, column-member { width: 25%; }';
    echo '</style>';
}

function get_event_title($eventData) {
    return sprintf('<h1>%s (%s)</h1>%s', $eventData->name, $eventData->date, $eventData->description);
}
