<?php

/**
* Plugin Name: Club Work Plan
* Plugin URI: https://github.com/schmaeddes/club-work-plan
* Description: A Wordpress plugin to create a work schedule for a local festival so that the club can plan the responsibilities for members.
* Version: 0.1
* Author: schmaeddes
* Author URI: https://www.schmaeddes.de/
**/

/**
 * Create custom table at activation of plugin 
 */

register_activation_hook(__FILE__, 'create_workplan_tables');
function create_workplan_tables() {
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'cwp_events';

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		event_name VARCHAR(100) NOT NULL,
		event_description VARCHAR(500) NULL,
		date_of_event DATETIME NOT NULL,
		date_of_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	) $charset_collate;";

	dbDelta($sql);

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

	dbDelta($sql);
}

include 'setup.php';
include 'Duty.php';
include 'Event.php';
include 'adminMenu.php';

function eingabeFeld( $id ) {
	echo'<div class="eingabeFeld">
			<form class="arbeitsPlanInput" action="http://mann.schmaeddes.de/submitmitglied" method="get">
				<p><input class="formMitglied" name="mitglied" type="text" size="25" maxlength="30"></p>
				<p><input type="hidden" name="id" value="'. $id .'"></p>
				<input type="image" class="checkmark" src="http://mann.schmaeddes.de/wp-content/themes/frohsinn/images/checkmark.png">
		 	</form>
		 </div>';
}

function get_workplan_for_event($eventID) {
	$eventDto = getEventData($eventID);
	$dutyData = getDutys($eventID);

	echo'<h1>' . $eventDto->name . '</h1>';
	echo'<h1>' . $eventDto->description . '</h1>';
	echo'<h1>' . $eventDto->date . '</h1>';

	$dutyNames = get_unique_list_of_dutyNames($dutyData);

	foreach ($dutyNames as $dutyName) {
		$dutys = get_dutys_from_dutyName($dutyName, $dutyData);
		create_duty_list_for_dutyName($dutyName, $dutys);
	}
}

function create_duty_list_for_dutyName($dutyName, $dutys, $beschreibung = ""){
	$current_user = wp_get_current_user();
	$neueBox = true;

	echo '<br><div class="dienstBar">'. $dutyName . " " . $beschreibung .'</div><br>';

	foreach ($dutys as $duty) {
		$dutyDto = new Duty($duty);
		
		if ($neueBox == true){
			echo '<div class="zeitBoxNachZeit">';
			$alteStartZeit = $dutyDto->startTime;
			$neueBox = false;
		} elseif ($dutyDto->startTime != $alteStartZeit) {
			echo '</div><div class="zeitBoxNachZeit">';
			$alteStartZeit = $dutyDto->startTime;
		}

		if ($dutyDto->member != ""){
			echo '<div class="zeitBoxVoll">';
		} else {
			echo '<div class="zeitBoxLeer">';
		}

		echo '<div class="artDesDienstes">'. $dutyName .'</div>
				<div class="zeitDesDienstes">';
		if ($dutyDto->endTime != "") {
			printf("%.5s - %.5s", $dutyDto->startTime, $dutyDto->endTime);
		} else {
			printf("ab %.5s", $dutyDto->startTime);
		}

		echo'</div>';
		if ($dutyDto->member != ""){
			$splittedName = $dutyDto->member;
			if($dutyDto->member != "Adler Meindorf") {
				$splittedName = substr( $dutyDto->member, 0, stripos( $dutyDto->member, " ") ) . " " . substr( $dutyDto->member, stripos( $dutyDto->member, " "), 2) . ".";
			}
			echo '<div class="mitgliedsName">'. $splittedName .'</div>';
			if (user_can( $current_user, 'administrator' )) {
				echo '<a href="http://mann.schmaeddes.de/deletemitglied?id='. $dutyDto->id .'&mitglied='. $dutyDto->member .'"><div class="deleteButtonAdmin">X</div></a>';
			}
		} else {
			eingabeFeld( $dutyDto->id );
		}
		echo '</div>';
   		
   	}
   	echo '</div>';
}

function submitMitglied() {
	global $wpdb;
	$mitglied = $_GET["mitglied"];
	$id = $_GET["id"];
	$abfrageMitglied = $wpdb->get_results("SELECT `mitglied` FROM `wp_workplan_dutys` WHERE `id` = '$id'", ARRAY_N);
	//$prüfungMitglied = mysql_fetch_row($abfrageMitglied);
	print_r($abfrageMitglied[0][0]);
	if ( $abfrageMitglied[0][0] == "" ) {
		$wpdb->update('wp_arbeitsplan', array( 'mitglied' => $mitglied ), array( 'id' => $id ));
		echo '<div class="dankeUndZurueck">Danke, dass du dich eingetragen hast '. $mitglied .'!<br><a href="http://maennerei-meindorf.de/daemmerschoppen-arbeitsplan/">Hier gehts zurück!</a></div>';
	} else {
		echo '<div class="dankeUndZurueck">Fehler.>>>' . $abfrageMitglied[0].mitglied . '<<< Hier ist schon ein Mitglied eingetragen.<br><a href="http://maennerei-meindorf.de/daemmerschoppen-arbeitsplan/">Hier gehts zurück!</a></div>';
	}
}

function deleteMitglied() {
	global $wpdb;
	$mitglied = $_GET["mitglied"];
	$id = $_GET["id"];
	$wpdb->update('wp_workplan_dutys', array( 'mitglied' => NULL ), array( 'id' => $id));
	echo '<div class="dankeUndZurueck">'. $mitglied .' wurde erfolgreich gelöscht!<br><a href="http://maennerei-meindorf.de/daemmerschoppen-arbeitsplan/">Hier gehts zurück!</a></div>';
}

function getEventData($eventID) {
	global $wpdb;
	$eventData = $wpdb->get_results("SELECT * FROM `wp_cwp_events` WHERE id = '$eventID'", ARRAY_N);

	return new Event($eventData[0]);
}

function getDutys($eventID) {
	global $wpdb;
	$dutyData = $wpdb->get_results("SELECT * FROM `wp_cwp_dutys` WHERE event_id = '$eventID'", ARRAY_N);

	return $dutyData;
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
	
	foreach($dutyData as $duty) {
		if ($duty[2] == $dutyName) {
			array_push($dutys, $duty);
		}
	}

	return $dutys;
}

?>