
<?php 

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class DutysTable extends WP_List_Table {

    private $table_data;

    // Here we will add our code
    function prepare_items($eventID = '') {
        $this->table_data = $this->get_table_data($eventID);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'id';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        
        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items, // total number of items
                'per_page'    => $per_page, // items to show on a page
                'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));

        $this->items = $this->table_data;
    }

    // Get table data
    private function get_table_data($eventID = '') {
        global $wpdb;

        $table = $wpdb->prefix . 'cwp_dutys';

        return $wpdb->get_results(
            "SELECT * from {$table} WHERE event_id='{$eventID}'",
            ARRAY_A
        );
    }

    function column_default($item, $column_name) {
          switch ($column_name) {
                case 'id':
                case 'event_id':
                case 'duty':
                case 'start_time':
                case 'end_time':
                case 'member':
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
            'event_id'    => array('event_id', true),
            'duty'    => array('duty', true),
            'start_time'    => array('start_time', true),
            'end_time'    => array('end_time', true),
            'member'    => array('member', true)
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
            'event_id'        => __('Event ID', 'supporthost-cookie-consent'),
            'duty' => __('Duty', 'supporthost-cookie-consent'),
            'start_time'     => __('Start Time', 'supporthost-cookie-consent'),
            'end_time'     => __('End Time', 'supporthost-cookie-consent'),
            'member'     => __('Member', 'supporthost-cookie-consent')
        );
    }

    // function column_event_name($item) {
    //     // Replace 'column3_data_key' with the actual key representing the data you want to link to
    //     // $link = get_edit_post_link($item['event_name_data_key']); // Example: use get_edit_post_link() function
        
    //     $link = (site_url('/wp-admin/admin.php?page=cwp-event-edit&eventID=' . $item['id']));
    //     $element = $item['event_name'];

    //     return sprintf('<a href="%s">%s</a>', $link, $element);
    // }
}

?>