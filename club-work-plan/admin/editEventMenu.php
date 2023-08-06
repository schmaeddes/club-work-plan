<?php

function cwp_event_edit() {

    get_edit_event_table_style();

    $eventID = $_GET['eventID'];
    $eventData = getEventData($eventID);
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

    echo '<div class="wrap">';
    echo '<h1>Dutys of ' . $eventData->name . '</h1>';
    $table = new DutysTable();
    $table->prepare_items($eventID);
    $table->display();
    $totalPagesOfTable = $table->_pagination_args['total_pages'];
    echo '</div>';

    echo '';

    ?>
    <div class="wrap">
        <h1>Add dutys to <?php echo $eventData->name; ?></h1>
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
                    <td><input name="number_of_dutys" type="number" /></td>
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
