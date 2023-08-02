<?php

/*
Template Name: Club Workplan
*/
    $cwpEventId = get_post_custom_values('cwp_event_id')[0];

    get_header();

    get_workplan_for_event($cwpEventId);

    get_footer();
    
?>