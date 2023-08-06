<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class EventsTable extends WP_List_Table {
    private $table_data;

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

    function get_columns() {
        return array(
            'cb'                => '<input type="checkbox" />',
            'id'                => __('ID', 'cwp_events'),
            'event_name'        => __('Event Name', 'cwp_events'),
            'event_description' => __('Description', 'cwp_events'),
            'date_of_event'     => __('Date of event', 'cwp_events')
        );
    }

    function column_event_name($item) {
        $link = (site_url('/wp-admin/admin.php?page=club-workplan&eventID=' . $item['id']));
        $eventName = $item['event_name'];
        $eventID = $item['id'];

        $actions = array(
            'edit'    => sprintf('<a href="?page=%s&eventID=%s&action=%s">' . __('Edit', 'wp_cwp_dutys') . '</a>', 'club-workplan', $eventID, 'edit_event'),
        );

        return sprintf('<strong><a href="%s">%s</a></strong> %s', $link, $eventName, $this->row_actions($actions));
    }
}
