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
        $sortable = $this->get_sortable_columns();
        $primary  = 'event_name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        
        usort($this->table_data, array(&$this, 'usort_reorder'));

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
                case 'event_description':
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

    protected function get_sortable_columns() {
        $sortable_columns = array(
            'id'            => array('id', true),
            'event_name'    => array('event_name', true),
            'date_of_event' => array('date_of_event', true)
        );

        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b) {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';

        // If no order, default to asc
        $order = (!empty($_GET['id'])) ? $_GET['id'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Define table columns
    function get_columns() {
        return array(
            'cb'                => '<input type="checkbox" />',
            'id'                => __('ID', 'supporthost-cookie-consent'),
            'event_name'        => __('Event Name', 'supporthost-cookie-consent'),
            'event_description' => __('Description', 'supporthost-cookie-consent'),
            'date_of_event'     => __('Date of event', 'supporthost-cookie-consent')
        );
    }

    function column_event_name($item) {
        // Replace 'column3_data_key' with the actual key representing the data you want to link to
        // $link = get_edit_post_link($item['event_name_data_key']); // Example: use get_edit_post_link() function
        
        $link = (site_url('/wp-admin/admin.php?page=club-workplan&eventID=' . $item['id']));
        $element = $item['event_name'];

        return sprintf('<a href="%s">%s</a>', $link, $element);
    }
}

?>