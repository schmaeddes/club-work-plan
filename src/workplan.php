<?php

/**
 * Plugin Name: Club Work Plan
 * Plugin URI: https://github.com/schmaeddes/club-work-plan
 * Description: A WordPress plugin to create a work schedule for a local festival so that the club can plan the responsibilities for members.
 * Version: 0.1
 * Author: schmaeddes
 * Author URI: https://www.schmaeddes.de/
 **/

use includes\Duty;

define( 'CWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CWP_EVENT_TABLE', $wpdb->prefix . 'cwp_events' );
define( 'CWP_DUTY_TABLE', $wpdb->prefix . 'cwp_dutys' );

require_once CWP_PLUGIN_PATH . 'includes/Duty.php';
require_once CWP_PLUGIN_PATH . 'includes/Event.php';
require_once CWP_PLUGIN_PATH . 'includes/utils.php';
require_once CWP_PLUGIN_PATH . 'admin/admin_menu.php';

/**
 * Create custom table at activation of plugin
 */
register_activation_hook( __FILE__, 'create_workplan_tables' );
function create_workplan_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = $wpdb->prefix . 'cwp_events';

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		event_name VARCHAR(100) NOT NULL,
		event_description VARCHAR(500) NULL,
		date_of_event DATETIME NOT NULL,
		date_of_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	) $charset_collate;";

	dbDelta( $sql );

	$table_name = $wpdb->prefix . 'cwp_dutys';

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		event_id mediumint(9) NOT NULL,
		duty VARCHAR(100) NOT NULL,
		start_time TIME NULL,
		end_time TIME NULL,
		member VARCHAR(100) NULL,
		date_of_entry TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	) $charset_collate;";

	dbDelta( $sql );
}


add_filter( 'the_content', 'babedibu' );
function babedibu( $content ) {
	if ( is_page() ) {
		$customId = get_post_custom_values( 'cwp_event_id', get_the_ID() )[0];
	}

	if ( ! empty( $customId ) ) {
		return $content . get_workplan_for_event( 3 );
	}

	return $content;
}

/**
 * Add template to dropdown
 */
//add_filter('theme_page_templates', 'add_page_template_to_dropdown');
//function add_page_template_to_dropdown($templates) {
//	$templates[CWP_PLUGIN_PATH . 'templates/workplan_template.php'] = __('Club Workplan', 'text-domain');
//
//	return $templates;
//}
//
//add_filter('template_include', 'change_page_template', 99);
//function change_page_template($template) {
//	if (is_page()) {
//        $customId = get_post_custom_values('cwp_event_id', get_the_ID())[0];
//
//		if (!empty($customId)) {
//            $meta = get_post_meta(get_the_ID());
//			$template = $meta['_wp_page_template'][0];
//		}
//	}
//
//	return $template;
//}

/**
 * Add custom css
 */
add_action( 'wp_enqueue_scripts', 'callback_for_setting_up_scripts' );
function callback_for_setting_up_scripts() {
	wp_enqueue_style( 'your-stylesheet-name', plugins_url( 'css/workplan.css', __FILE__ ), false, '1.0.0', 'all' );
}

add_action( 'admin_post_add-member', 'submit_add_member' );
add_action( 'admin_post_nopriv_add-member', 'submit_add_member' );
function submit_add_member() {
	global $wpdb;

	$dutyID        = $_POST['duty_id'];
	$slug          = $_POST['page_id'];
	$newMemberName = $_POST['member'];
	$data          = array( 'member' => $newMemberName );

	$getMember = $wpdb->get_row( "SELECT `member` FROM " + CWP_DUTY_TABLE + " WHERE `id` = '$dutyID'" );
	if ( $getMember == "" && $getMember != $newMemberName ) {
		$wpdb->update( CWP_DUTY_TABLE, $data, array( 'ID' => $dutyID ) );
	}

	wp_safe_redirect( site_url( '/' . $slug ) );
}

function get_workplan_for_event( $eventID ) {


	$eventDto = get_event_data( $eventID );
	$dutyData = get_duties( $eventID );

	$krasserString = '<div class="daemmerSchoppen">' . $eventDto->name . '</div>';
	$krasserString .= '<div class="daemmerSchoppen">' . $eventDto->description . '</div>';
	$krasserString .= '<div class="daemmerSchoppen">' . $eventDto->date . '</div>';

	$dutyNames = get_unique_list_of_dutyNames( $dutyData );

	foreach ( $dutyNames as $dutyName ) {
		$duties        = get_dutys_from_dutyName( $dutyName, $dutyData );
		$krasserString .= create_duty_list_for_dutyName( $dutyName, $duties );
	}

	return $krasserString;
}

function create_duty_list_for_dutyName( $dutyName, $duties ) {
	global $post;
	$slug = $post->post_name;

	$hasNewStartTime     = true;
	$startTimeOfLastDuty = "";

	$krasserString = '<br><div class="dienstBar">' . $dutyName . '</div><br>';

	foreach ( $duties as $duty ) {
		$dutyDto = Duty::from_array( $duty );

		if ( $hasNewStartTime ) {
			$krasserString       .= '<div class="zeitBoxNachZeit">';
			$startTimeOfLastDuty = $dutyDto->startTime;
			$hasNewStartTime     = false;
		} elseif ( $dutyDto->startTime != $startTimeOfLastDuty ) {
			$krasserString       .= '</div><div class="zeitBoxNachZeit">';
			$startTimeOfLastDuty = $dutyDto->startTime;
		}

		if ( $dutyDto->member != "" ) {
			$krasserString .= '<div class="zeitBoxVoll">';
		} else {
			$krasserString .= '<div class="zeitBoxLeer">';
		}

		$krasserString .= '<div class="artDesDienstes">' . $dutyName . '</div><div class="zeitDesDienstes">';
		if ( $dutyDto->endTime != "" ) {
			$krasserString .= sprintf( "%.5s - %.5s", $dutyDto->startTime, $dutyDto->endTime );
		} else {
			$krasserString .= sprintf( "ab %.5s", $dutyDto->startTime );
		}
		$krasserString .= '</div>';

		if ( $dutyDto->member != "" ) {
			$krasserString .= '<div class="mitgliedsName">' . $dutyDto->member . '</div>';
		} else {

			$krasserString .= '<div class="eingabeFeld">
				<form action="' . admin_url( "admin-post.php" ) . '" method="post">
					<input type="hidden" name="action" value="add-member" />
					<input type="hidden" name="duty_id" value="' . $dutyDto->id . '">
					<input type="hidden" name="page_id" value="' . $slug . '">
					<input type="text" name="member" size="25" maxlength="30">
					<input type="submit" name="add-member" id="add-member" class="button button-primary" value="Add Member" />
				</form>
			</div>';

		}
		$krasserString .= '</div>';
	}
	$krasserString .= '</div>';

	return '<p>' . $krasserString . '</p>';
}
