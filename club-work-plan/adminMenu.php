<?php 

add_action('admin_menu', 'add_workplan_setup_menu');

function add_workplan_setup_menu(){
	add_menu_page('Club Workplan', 'Club Workplan', 'manage_options', 'club-workplan', 'club_workplan');
}

// function club_workplan(){
//     echo "<h1>Hello World!</h1>";

//     global $wpdb, $_wp_column_headers;
//     $screen = get_current_screen();
 
//     /* -- Preparing your query -- */
//          $query = "SELECT * FROM $wpdb->cwp_events";
 
//     /* -- Ordering parameters -- */
//         //Parameters that are going to be used to order the result
//         $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
//         $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : ’;
//         if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
 
//     /* -- Pagination parameters -- */
//          //Number of elements in your table?
//          $totalitems = $wpdb->query($query); //return the total number of affected rows
//          //How many to display per page?
//          $perpage = 5;
//          //Which page is this?
//          $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : ’;
//          //Page Number
//          if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; } //How many pages do we have in total? $totalpages = ceil($totalitems/$perpage); //adjust the query to take pagination into account if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; } /* -- Register the pagination -- */ $this->set_pagination_args( array(
//           "total_items" => $totalitems,
//           "total_pages" => $totalpages,
//           "per_page" => $perpage,
//        ) );
//        //The pagination links are automatically built according to those parameters
 
//     /* -- Register the Columns -- */
//        $columns = $this->get_columns();
//        $_wp_column_headers[$screen->id]=$columns;
 
//     /* -- Fetch the items -- */
//        $this->items = $wpdb->get_results($query);
// }


?>