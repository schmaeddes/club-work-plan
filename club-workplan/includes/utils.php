<?php

use cwp\includes\Duty;

function get_event_data($eventID): Event {
	global $wpdb;
	$eventData = $wpdb->get_row("SELECT * FROM " . CWP_EVENT_TABLE . " WHERE id = '$eventID'");

	return new Event($eventData);
}

function get_duties($eventID): array {
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM " . CWP_DUTY_TABLE . " WHERE event_id = '$eventID'", ARRAY_N);
}

function get_duty($dutyID): Duty {
	global $wpdb;
	$dutyData = $wpdb->get_row("SELECT * FROM " . CWP_DUTY_TABLE . " WHERE id = '$dutyID'");

	return Duty::from_row($dutyData);
}

function update_member($dutyId, $memberName): void {
	global $wpdb;
	$data = array( 'member' => $memberName );
	$wpdb->update( CWP_DUTY_TABLE, $data, array( 'id' => $dutyId ) );
}

function get_unique_list_of_duty_names($dutyData): array {
	$arr = array();
	foreach ($dutyData as $duty) {
		$arr[] = $duty[2];
	}
	return array_unique($arr);
}

function get_dutys_from_dutyName($dutyName, $dutyData): array {
	$duties = array();

	foreach ($dutyData as $duty) {
		if ($duty[2] == $dutyName) {
			$duties[] = $duty;
		}
	}

	return $duties;
}
