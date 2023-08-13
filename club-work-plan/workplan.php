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
include 'includes/Duty.php';
include 'includes/Event.php';
include 'admin/adminMenu.php';

add_action('admin_post_nopriv_add-member', 'submit_add_member');
function submit_add_member() {
    global $wpdb;

    $dutyID = $_POST['duty_id'];
    $slug = $_POST['page_id'];
    $newMemberName = $_POST['member'];
    $data = array('member' => $newMemberName);
    $wpdb->update($wpdb->prefix . 'cwp_dutys', $data, array('ID' => $dutyID));

	wp_safe_redirect(site_url('/' . $slug));
}

function get_workplan_for_event($eventID) {
	$eventDto = getEventData($eventID);
	$dutyData = getDutys($eventID);

	echo '<h1>' . $eventDto->name . '</h1>';
	echo '<h1>' . $eventDto->description . '</h1>';
	echo '<h1>' . $eventDto->date . '</h1>';

	$dutyNames = get_unique_list_of_dutyNames($dutyData);

	foreach ($dutyNames as $dutyName) {
		$dutys = get_dutys_from_dutyName($dutyName, $dutyData);
		create_duty_list_for_dutyName($dutyName, $dutys);
	}
}

function create_duty_list_for_dutyName($dutyName, $dutys) {
	global $post;
	$slug = $post->post_name;

	$current_user = wp_get_current_user();
	$hasNewStartTime = true;
	$startTimeOfLastDuty = "";
	
	echo '<br><div class="dienstBar">' . $dutyName . '</div><br>';

	foreach ($dutys as $duty) {
		$dutyDto = new Duty($duty);

		if ($hasNewStartTime == true) {
			echo '<div class="zeitBoxNachZeit">';
			$startTimeOfLastDuty = $dutyDto->startTime;
			$hasNewStartTime = false;
		} elseif ($dutyDto->startTime != $startTimeOfLastDuty) {
			echo '</div><div class="zeitBoxNachZeit">';
			$startTimeOfLastDuty = $dutyDto->startTime;
		}

		if ($dutyDto->member != "") {
			echo '<div class="zeitBoxVoll">';
		} else {
			echo '<div class="zeitBoxLeer">';
		}

		echo '<div class="artDesDienstes">' . $dutyName . '</div><div class="zeitDesDienstes">';
		if ($dutyDto->endTime != "") {
			printf("%.5s - %.5s", $dutyDto->startTime, $dutyDto->endTime);
		} else {
			printf("ab %.5s", $dutyDto->startTime);
		}
		echo '</div>';

		if ($dutyDto->member != "") {
			$splittedName = $dutyDto->member;

			if ($dutyDto->member != "Adler Meindorf") {
				$splittedName = substr($dutyDto->member, 0, stripos($dutyDto->member, " ")) . " " . substr($dutyDto->member, stripos($dutyDto->member, " "), 2) . ".";
			}

			echo '<div class="mitgliedsName">' . $splittedName . '</div>';

			if (user_can($current_user, 'administrator')) {
				//echo '<a href="http://mann.schmaeddes.de/deletemitglied?id='. $dutyDto->id .'&mitglied='. $dutyDto->member .'"><div class="deleteButtonAdmin">X</div></a>';
			}
		} else {
			?>
			<div class="eingabeFeld">
				<form action="<?php echo admin_url("admin-post.php"); ?>" method="post">
					<input type="hidden" name="action" value="add-member" />
					<input type="hidden" name="duty_id" value="<?php echo $dutyDto->id; ?>">
					<input type="hidden" name="page_id" value="<?php echo $slug; ?>">
					<input type="text" name="member" size="25" maxlength="30">
					<input type="submit" name="add-member" id="add-member" class="button button-primary" value="Add Member" />
				</form>
			</div>
			<?php 
		}
		echo '</div>';
	}
	echo '</div>';
}


function submitMitglied() {
	global $wpdb;
	$member = $_GET["member"];
	$dutyID = $_GET["dutyID"];
	$eventID = $_GET["eventID"];

	$getMember = $wpdb->get_row("SELECT `member` FROM `wp_workplan_dutys` WHERE `id` = '$dutyID'");
	
	if ($getMember == "" && $getMember != $member) {
		$wpdb->update($wpdb->prefix . 'cwp_dutys', array('member' => $member), array('ID' => $dutyID));
	}

	$redirectUrl = sprintf("/wp-admin/admin.php?page=%s&eventID=%s", "club-workplan", $eventID);

	wp_safe_redirect(site_url($redirectUrl));
}

function deleteMitglied() {
	global $wpdb;
	$mitglied = $_GET["mitglied"];
	$id = $_GET["id"];
	$wpdb->update('wp_workplan_dutys', array('mitglied' => NULL), array('id' => $id));

	
}

function getEventData($eventID) {
	global $wpdb;
	$eventData = $wpdb->get_row("SELECT * FROM `wp_cwp_events` WHERE id = '$eventID'");

	return new Event($eventData);
}

function getDutys($eventID) {
	global $wpdb;
	$dutyData = $wpdb->get_results("SELECT * FROM `wp_cwp_dutys` WHERE event_id = '$eventID'", ARRAY_N);

	return $dutyData;
}

function getDuty($dutyID) {
	global $wpdb;
	$dutyData = $wpdb->get_row("SELECT * FROM `wp_cwp_dutys` WHERE id = '$dutyID'");

	return new Duty($dutyData);
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
