<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

add_action( 'admin_notices_delete-duty', 'duty_delete_notice__info', 1, 1);
function duty_delete_notice__info($dutyData) {
	$class = 'notice notice-info';
	$message = __( $dutyData->duty . ' was deleted.', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

class Duties_Table extends WP_List_Table {
    private $table_data;

    function prepare_items($eventID = '') {
        if (isset($_GET['action']) && $_GET['page'] == "club-workplan" && $_GET['action'] == "delete_duty") {
            global $wpdb;
            $dutyID = $_GET['duty'];
            $dutyData = get_duty($dutyID);

            if ($dutyData->duty != null) {
                $wpdb->delete(CWP_DUTY_TABLE, array('ID' => $dutyID));
                do_action('admin_notices_delete-duty', $dutyData);
            }
        }

        $this->table_data = $this->get_table_data($eventID);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'id';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil($total_items / $per_page) // use ceil to round up
        ));

        $this->items = $this->table_data;
    }

    private function get_table_data($eventID = '') {
        global $wpdb;

        $table = CWP_DUTY_TABLE;

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

    function usort_reorder($a, $b) {
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
        $order = (!empty($_GET['id'])) ? $_GET['id'] : 'asc';
        $result = strcmp($a[$orderby], $b[$orderby]);

        return ($order === 'asc') ? $result : -$result;
    }

    function get_columns() {
        return array(
            'cb'            => '<input type="checkbox" />',
            'id'            => __('ID', 'supporthost-cookie-consent'),
            'event_id'      => __('Event ID', 'supporthost-cookie-consent'),
            'duty'          => __('Duty', 'supporthost-cookie-consent'),
            'start_time'    => __('Start Time', 'supporthost-cookie-consent'),
            'end_time'      => __('End Time', 'supporthost-cookie-consent'),
            'member'        => __('Member', 'supporthost-cookie-consent')
        );
    }

    /**
     * Row actions
     */
    function column_duty($item) {
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&eventID=%s&action=%s&duty=%s">' . __('Edit', CWP_DUTY_TABLE) . '</a>', 'club-workplan', $item['event_id'], 'edit_duty', $item['id']),
            'delete'    => sprintf('<a href="?page=%s&eventID=%s&action=%s&duty=%s">' . __('Delete', CWP_DUTY_TABLE) . '</a>', 'club-workplan', $item['event_id'], 'delete_duty', $item['id']),
        );

        return sprintf('<strong>%1$s</strong> %2$s', $item['duty'], $this->row_actions($actions));
    }
}
