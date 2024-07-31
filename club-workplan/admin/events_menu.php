<?php

require_once(CWP_PLUGIN_PATH . 'admin/classes/Events_Table.php');

add_action( 'admin_notices_empty-fields', 'event_empty_fields__info', 1, 2);
function event_empty_fields__info(): void {
	$class = 'notice notice-info';
	$message = __( 'Please fill all fields for the event', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

add_action('admin_post_create-event', 'submit_create_event');
function submit_create_event() {
	global $wpdb;

	$newEventName = $_POST['new_event_name'];
	$newEventDescription = $_POST['new_event_description'];
	$newEventDate = $_POST['new_event_date'];

	if ($newEventName == "" || $newEventDescription == "" || $newEventDate == "") {
		$redirectUrl = sprintf("/wp-admin/admin.php?page=%s&message=%s", "club-workplan", "empty-fields");
		return wp_redirect(esc_url_raw(site_url($redirectUrl)));
	}

	$data = array('event_name' => $newEventName, 'event_description' => $newEventDescription, 'date_of_event' => $newEventDate);
	$wpdb->insert(CWP_EVENT_TABLE, $data);

	wp_safe_redirect(esc_url(site_url('/wp-admin/admin.php?page=club-workplan')));
}

function cwp_create_event() {
    get_event_table_stlye();

	if ($_GET['message'] == "empty-fields") {
		do_action('admin_notices_empty-fields');
	}

    echo '<div class="wrap">';
    echo '<h1>Events</h1>';
    $table = new Events_Table();
    $table->prepare_items();
    $table->display();
    echo '</div>';
    ?>
    <div class="wrap">
        <h1>Create new event</h1>
        <form method="post" action="<?php echo admin_url("admin-post.php"); ?>">
            <input type="hidden" name="action" value="create-event" />

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label>Event Name</label></th>
                    <td><input name="new_event_name" type="text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Description</label></th>
                    <td><input name="new_event_description" size="40" type="text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label>Date of event</label></th>
                    <td><input name="new_event_date" type="text" /></td>
                </tr>
            </table>

            <?php submit_button(__('Add new event'), 'primary', 'create-event', true); ?>
        </form>
    </div>

    <?php
}

function get_event_table_stlye(): void {
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-event_name { width: 30%; }';
    echo '.wp-list-table .column-event_description { width: 50%; }';
    echo '.wp-list-table .column-date_of_event { width: 15%; }';
    echo '</style>';
}
?>
