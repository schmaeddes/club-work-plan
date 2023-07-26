<?php

class EventsTable extends WP_List_Table {

/**
 * Constructor, we override the parent to pass our own arguments
 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
 */
 function __construct() {
    parent::__construct( array(
   'singular'=> 'wp_cwp_event', //Singular label
   'plural' => 'wp_cwp_events', //plural label, also this well be one of the table css class
   'ajax'   => false //We won't support Ajax for this table
   ) );
 }



 /**
 * Add extra markup in the toolbars before or after the list
 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
 */
function extra_tablenav( $which ) {
   if ( $which == "top" ){
      //The code that goes before the table is here
      echo"Hello, I'm before the table";
   }
   if ( $which == "bottom" ){
      //The code that goes after the table is there
      echo"Hi, I'm after the table";
   }
}}

/**
 * Define the columns that are going to be used in the table
 * @return array $columns, the array of columns to use with the table
 */
function get_columns() {
    return $columns= array(
       'id'=>__('ID'),
       'event_name'=>__('Name'),
       'description'=>__('Description'),
       'dat_of_event'=>__('Date of event'),
       'date_of_creation'=>__('Date of creation')
    );
 }

?>