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
 * Add custom css
 */
add_action( 'wp_enqueue_scripts', 'callbackForSettingUpScripts' );
function callbackForSettingUpScripts(): void {
	wp_enqueue_style( 'your-stylesheet-name', plugins_url( 'css/workplan.css', __FILE__ ), false, '1.0.0', 'all' );
}

add_action( 'admin_post_add-member', 'submitAddMember' );
add_action( 'admin_post_nopriv_add-member', 'submitAddMember' );
function submitAddMember(): void {
	$dutyID        = $_POST['duty_id'];
	$slug          = $_POST['page_id'];
	$newMemberName = $_POST['member'];

	$duty = get_duty($dutyID);
	if ( $duty->member == "" && $duty->member != $newMemberName ) {
		update_member($dutyID, $newMemberName);
	}

	wp_safe_redirect( get_permalink( $slug ) );
}

function get_workplan_for_event( $eventID ) {
	$eventDto = get_event_data( $eventID );
	$dutyData = get_duties( $eventID );

	$krasserString = '<h2>' . $eventDto->name . '</h2>';
	$krasserString .= '<h3>' . $eventDto->description . '</h3>';
	$krasserString .= '<h4>' . $eventDto->date . '</h4>';

	$dutyNames = get_unique_list_of_duty_names( $dutyData );

	foreach ( $dutyNames as $dutyName ) {
		$duties        = get_dutys_from_dutyName( $dutyName, $dutyData );
		$krasserString .= create_duty_list_for_dutyName( $dutyName, $duties );
	}

	return $krasserString;
}

function create_duty_list_for_dutyName( $dutyName, $duties ) {
	global $post;
	$slug = $post->ID;

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
			$krasserString .= '<div class="duty-box booked">';
		} else {
			$krasserString .= '<div class="duty-box">';
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
			$krasserString .=
				'
					<form action="'.  admin_url( "admin-post.php" ) . '" method="post">
						<input type="hidden" name="action" value="add-member" />
						<input type="hidden" name="duty_id" value="' . $dutyDto->id . '">
						<input type="hidden" name="page_id" value="' . $slug . '">
						<input type="text" name="member" class="member-input-field" size="25" maxlength="30">
						<input type="submit" name="add-member" class="add-member" class="button button-primary" value="Add" />
					</form>
				';

		}
		$krasserString .= '</div>';
	}
	$krasserString .= '</div>';

	return '<p>' . $krasserString . '</p>';
}
