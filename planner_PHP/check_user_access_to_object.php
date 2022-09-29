<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL|E_STRICT);
    
    require_once 'db_helper.php';
        
    $user_id = filter_input(INPUT_POST, 'user_id');
    $object_id = filter_input(INPUT_POST, 'object_id');

//    echo $user_id . ', ' . $object_id . '<br>';
    
    $ressrc = oci_parse($conn, "BEGIN :access := GET_USER_ACCESS_TO_OBJECT(:user_id, :object_id); END;");
    oci_bind_by_name($ressrc, 'access', $access, 8, SQLT_CHR);
    oci_bind_by_name($ressrc, 'user_id', $user_id);
    oci_bind_by_name($ressrc, 'object_id', $object_id);
    
    $oci_res = oci_execute($ressrc);
    if (!$oci_res) {
        print_r(oci_error($ressrc));
    }
    
    echo $access;
?>