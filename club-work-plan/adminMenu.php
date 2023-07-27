<?php 

include 'EventsTable.php';

function add_workplan_setup_menu(){
	add_menu_page('Club Workplan', 'Club Workplan', 'manage_options', 'club-workplan', 'club_workplan');
}
add_action('admin_menu', 'add_workplan_setup_menu');

function club_workplan(){
    echo "<h1>Hello World!</h1>";

    // Creating an instance
    $table = new EventsTable();

    echo '<div class="wrap"><h2>SupportHost List Table</h2>';
    // Prepare table
    $table->prepare_items();
    // Display table
    $table->display();
    echo '</div>';
}

?>