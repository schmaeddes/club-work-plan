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

function club_workplan() {
    if (isset($_GET['eventID'])) {
        cwp_event_edit();
    } else {
        cwp_create_event();
    }
}

function cwp_create_event() {
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
                    <td><input name="new_event_name" type="text" />
                </tr>
                <tr>
                    <th scope="row"><label for="blogdescription">Description</label></th>
                    <td><input name="new_event_description" type="text" />
                </tr>
                <tr>
                    <th scope="row"><label for="blogdescription">Date of event</label></th>
                    <td><input name="new_event_date" type="text" />
                </tr>        
            </table>

            <?php submit_button( __( 'Add new event' ), 'primary', 'createevent', true ); ?>
        </form>
    </div>

<?php 
}

function cwp_event_edit() {
    echo'<h1>Edit Event</h1>';

    echo'Tach!';


    echo'<h1>Dutys</h1>';

    $table = new DutysTable();

    // Prepare table
    $table->prepare_items($_GET['eventID']);
    // Display table
    $table->display();

    echo'<h1>Add dutys</h1>';
}

?>