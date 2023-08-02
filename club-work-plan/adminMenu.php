<?php

include 'EventsTable.php';
include 'DutysTable.php';

add_action('admin_menu', 'add_workplan_setup_menu');
function add_workplan_setup_menu() {
    add_menu_page('Club Workplan', 'Club Workplan', 'manage_options', 'club-workplan', 'club_workplan');
}

add_action('admin_post_create-event', 'submit_create_event');
function submit_create_event() {
    global $wpdb;
    $newEventName=$_POST['new_event_name'];
    $newEventDescription=$_POST['new_event_description'];
    $newEventDate=$_POST['new_event_date'];
    $data=array('event_name'=>$newEventName, 'event_description'=>$newEventDescription, 'date_of_event'=>$newEventDate);
    $wpdb->insert( $wpdb->prefix . 'cwp_events', $data);   
    
    wp_safe_redirect( esc_url( site_url('/wp-admin/admin.php?page=club-workplan') ) );
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
        $data=array('event_id'=>$newDutyEventID, 'duty'=>$newDutyName, 'start_time'=>$newStartTime, 'end_time'=>$newEndTime);
        $wpdb->insert( $wpdb->prefix . 'cwp_dutys', $data);
    }
    
    wp_redirect( esc_url_raw( site_url("/wp-admin/admin.php?page=club-workplan&eventID=" . $newDutyEventID . "&paged=" . $totalPagesOfTable) ) );
}

function club_workplan() {
    if (isset($_GET['eventID'])) {
        cwp_event_edit();
    } else {
        cwp_create_event();
    }
}

function cwp_create_event() {

    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-event_name { width: 30%; }';
    echo '.wp-list-table .column-event_description { width: 50%; }';
    echo '.wp-list-table .column-date_of_event { width: 15%; }';
    echo '</style>';

    echo '<h1>Events</h1>';

    $table = new EventsTable();
    $table->prepare_items();
    $table->display();

    echo '</div>';
    echo "<h1>Create new event</h1>";
    ?>

    <div class="wrap">
        <form method="post" action="<?php echo admin_url("admin-post.php"); ?>">
            <input type="hidden" name="action" value="create-event" />    

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label>Event Name</label></th>
                    <td><input name="new_event_name" type="text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogdescription">Description</label></th>
                    <td><input name="new_event_description" type="text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogdescription">Date of event</label></th>
                    <td><input name="new_event_date" type="text" /></td>
                </tr>        
            </table>

            <?php submit_button( __( 'Add new event' ), 'primary', 'create-event', true ); ?>
        </form>
    </div>

<?php 
}

function cwp_event_edit() {

    $eventID = $_GET['eventID'];
    $eventData = getEventData($eventID);
    
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-event_id, .column-start_time, .column-end_time { width: 15%; }';
    echo '.wp-list-table .column-duty, column-member { width: 25%; }';
    echo '</style>';

    echo'<h1>Edit Event</h1>';

    echo'Tach!';

    echo'<h1>Dutys of ' . $eventData->name . '</h1>';

    $table = new DutysTable();

    // Prepare table
    $table->prepare_items($eventID);
    // Display table
    $table->display();

    $totalPagesOfTable = $table->_pagination_args['total_pages'];


    echo '<h1>Add dutys to ' . $eventData->name . '</h1>';

    ?>
    <div class="wrap">
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

            <?php submit_button( __( 'Add duty' ), 'primary', 'create-duty', true ); ?>
        </form>
    </div>
    
<?php
}

?>