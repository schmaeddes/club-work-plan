<?php

function get_event_data($eventID) {
	global $wpdb;
	$eventData = $wpdb->get_row("SELECT * FROM " . CWP_EVENT_TABLE . " WHERE id = '$eventID'");

	return new Event($eventData);
}

function get_duties($eventID) {
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM " . CWP_DUTY_TABLE . " WHERE event_id = '$eventID'", ARRAY_N);
}

function get_duty($dutyID) {
	global $wpdb;
	$dutyData = $wpdb->get_row("SELECT * FROM " . CWP_DUTY_TABLE . " WHERE id = '$dutyID'");

	return Duty::from_row($dutyData);
}

function get_unique_list_of_dutyNames($dutyData) {
	$arr = array();
	foreach ($dutyData as $duty) {
		$arr[] = $duty[2];
	}
	$unique_data = array_unique($arr);

	return $unique_data;
}

function get_dutys_from_dutyName($dutyName, $dutyData) {
	$dutys = array();

	foreach ($dutyData as $duty) {
		if ($duty[2] == $dutyName) {
			array_push($dutys, $duty);
		}
	}

	return $dutys;
}