<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL|E_STRICT);
    
    require_once 'web_helper.php';
    require_once 'db_helper.php';
    require_once 'event_helper.php';
            
    if (filter_input(INPUT_POST, 'new_event_send') != null) {
        
        $name = filter_input(INPUT_POST, 'new_event/name');
        $begin_date_text = filter_input(INPUT_POST, 'new_event/begin_date');
        $end_date_text = filter_input(INPUT_POST, 'new_event/end_date');
        $default_access = filter_input(INPUT_POST, 'new_event/default_access');
        $owner = filter_input(INPUT_POST, 'new_event/owner');
        $id = null;
        
        $r = oci_parse($conn,
            "declare
            begin_date TIMESTAMP;
            end_date TIMESTAMP;
            begin
            begin_date := " . query_text_to_timestamp(':begin_date_text') . ";
            end_date := " . query_text_to_timestamp(':end_date_text') . ";
            :id := add_event(:name, begin_date, end_date, :access, :owner);
            end;");
        oci_bind_by_name($r, 'id', $id, 38, SQLT_INT);
        oci_bind_by_name($r, 'name', $name);
        oci_bind_by_name($r, 'begin_date_text', $begin_date_text);
        oci_bind_by_name($r, 'end_date_text', $end_date_text);
        oci_bind_by_name($r, 'access', $default_access);
        oci_bind_by_name($r, 'owner', $owner);

        $oci_res = oci_execute($r);
        if (!$oci_res) {
            print_r(oci_error($r));
        }
        
        come_back_or_to_index();
    }
?>