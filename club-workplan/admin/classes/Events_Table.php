<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

add_action( 'admin_notices_delete-event', 'event_delete_notice__info', 1, 2);
function event_delete_notice__info($eventData, $numberOfDeletedRows): void {
	$class = 'notice notice-info';
	$message = __( $eventData->name . ' was deleted and with it ' . $numberOfDeletedRows . ' duties.', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

class Events_Table extends WP_List_Table {
    private $table_data;

    function prepare_items() {
        if (isset($_GET['action']) && $_GET['page'] == "club-workplan" && $_GET['action'] == "delete_event") {
            global $wpdb;
            $eventID = $_GET['eventID'];
            $eventData = get_event_data($eventID);

            if ($eventData->name != null) {
                $wpdb->delete(CWP_EVENT_TABLE, array('ID' => $eventID));
                $numberOfDeletedRows = $wpdb->delete(CWP_DUTY_TABLE, array('EVENT_ID' => $eventID));
                do_action('admin_notices_delete-event', $eventData, $numberOfDeletedRows);
            }
        }

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

        $table = CWP_EVENT_TABLE;

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
            'id'                => __('ID', CWP_EVENT_TABLE),
            'event_name'        => __('Event Name', CWP_EVENT_TABLE),
            'event_description' => __('Description', CWP_EVENT_TABLE),
            'date_of_event'     => __('Date of event', CWP_EVENT_TABLE)
        );
    }

    function column_event_name($item) {
        $link = (site_url('/wp-admin/admin.php?page=club-workplan&eventID=' . $item['id']));
        $eventName = $item['event_name'];
        $eventID = $item['id'];

        $actions = array(
            'edit'    => sprintf('<a href="?page=%s&eventID=%s&action=%s">' . __('Edit', CWP_EVENT_TABLE) . '</a>', 'club-workplan', $eventID, 'edit_event'),
            'delete'  => sprintf('<a href="?page=%s&eventID=%s&action=%s">' . __('Delete', CWP_EVENT_TABLE) . '</a>', 'club-workplan', $eventID, 'delete_event'),
        );

        return sprintf('<strong><a href="%s">%s</a></strong> %s', $link, $eventName, $this->row_actions($actions));
    }
}
