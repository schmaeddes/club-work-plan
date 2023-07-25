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
 * 
 * Create custom table at activation of plugin
 * 
 */

include 'Duty.php';

function create_the_custom_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
	
    $table_name = $wpdb->prefix . 'workplan';

    $sql = "CREATE TABLE " . $table_name . " (
	id int(11) NOT NULL AUTO_INCREMENT,
	event VARCHAR(100) NOT NULL,
	duty VARCHAR(100) NOT NULL,
	startTime time NULL,
	endTime time NULL,
    member VARCHAR(100) NULL,
    dateOfEntry time NULL,
	PRIMARY KEY (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_the_custom_table');

/**
 * 
 * Add template to dropdown
 * 
 */

function add_page_template_to_dropdown($templates) {
   $templates[plugin_dir_path( __FILE__ ) . 'template.php'] = __( 'Workplan', 'text-domain' );

   return $templates;
}

add_filter( 'theme_page_templates', 'add_page_template_to_dropdown' );

function change_page_template($template)
{
    if (is_page()) {
        $meta = get_post_meta(get_the_ID());

        if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
            $template = $meta['_wp_page_template'][0];
        }
    }

    return $template;
}

add_filter( 'template_include', 'change_page_template', 99 );

/**
 * 
 * Functions auf work-plan
 * 
 */

// --------------------------------------------------------------------
//	Arbeitsplan

function eingabeFeld( $id ) {
	echo'<div class="eingabeFeld">
			<form class="arbeitsPlanInput" action="http://mann.schmaeddes.de/submitmitglied" method="get">
				<p><input class="formMitglied" name="mitglied" type="text" size="25" maxlength="30"></p>
				<p><input type="hidden" name="id" value="'. $id .'"></p>
				<input type="image" class="checkmark" src="http://mann.schmaeddes.de/wp-content/themes/frohsinn/images/checkmark.png">
		 	</form>
		 </div>';
}

function arbeitsplan($event) {

	$dutyData = getEvent($event);
	$dutyNames = getUniqueListOfDutyNames($dutyData);

	foreach ($dutyNames as $dutyName) {

		$dutys = getDutysFromDutyName($dutyName, $dutyData);
		dienstListe($dutyName, $dutys);
	}
}

function dienstListe($dutyName, $dutys, $beschreibung = ""){
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
	$abfrageMitglied = $wpdb->get_results("SELECT `mitglied` FROM `wp_workplan` WHERE `id` = '$id'", ARRAY_N);
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
	$wpdb->update('wp_workplan', array( 'mitglied' => NULL ), array( 'id' => $id));
	echo '<div class="dankeUndZurueck">'. $mitglied .' wurde erfolgreich gelöscht!<br><a href="http://maennerei-meindorf.de/daemmerschoppen-arbeitsplan/">Hier gehts zurück!</a></div>';
}

function getEvent($event) {
	global $wpdb;
	$dutyData = $wpdb->get_results("SELECT * FROM `wp_workplan` WHERE event = 'wf2023'", ARRAY_N);

	return $dutyData;
}

function getUniqueListOfDutyNames($dutyData) {
		$arr = array();
		foreach ($dutyData as $duty) {
			$arr[] = $duty[2];
		}
		$unique_data = array_unique($arr);
		
		return $unique_data;
}

function getDutysFromDutyName($dutyName, $dutyData) {
	$dutys = array();
	
	foreach($dutyData as $duty) {
		if ($duty[2] == $dutyName) {
			array_push($dutys, $duty);
		}
	}

	return $dutys;
}
