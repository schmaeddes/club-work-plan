<?php 

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class EventsTable extends WP_List_Table {

    private $table_data;

    // Here we will add our code
    function prepare_items() {
        $this->table_data = $this->get_table_data();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary  = 'event_name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        
        $this->items = $this->table_data;
    }

    // Get table data
    private function get_table_data() {
        global $wpdb;

        $table = $wpdb->prefix . 'cwp_events';

        return $wpdb->get_results(
            "SELECT * from {$table}",
            ARRAY_A
        );
    }

    function column_default($item, $column_name) {
          switch ($column_name) {
                case 'id':
                case 'event_name':
                case 'description':
                case 'date_of_event':
                default:
                    return $item[$column_name];
          }
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s" />',
                $item['id']
        );
    }

    // Define table columns
    function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'event_name'    => __('Event Name', 'supporthost-cookie-consent'),
            'description'   => __('Description', 'supporthost-cookie-consent'),
            'date_of_event' => __('Date of event', 'supporthost-cookie-consent')
        );
        return $columns;
    }
}

?>