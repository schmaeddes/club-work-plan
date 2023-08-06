<?php

function cwp_create_event() {

    get_event_table_stlye();

    echo '<div class="wrap">';
    echo '<h1>Events</h1>';
    $table = new EventsTable();
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

function get_event_table_stlye() {
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-event_name { width: 30%; }';
    echo '.wp-list-table .column-event_description { width: 50%; }';
    echo '.wp-list-table .column-date_of_event { width: 15%; }';
    echo '</style>';
}

?>