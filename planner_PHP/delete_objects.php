<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL|E_STRICT);
    
    require_once 'web_helper.php';
    require_once 'db_helper.php';
        
    $ids = json_decode(filter_input(INPUT_POST, 'ids_to_delete'));
    
    print_r($ids);
    
    print_r($logged);
    
    if ($ids != null) {
        
        print_r($ids);
        
        foreach ($ids as $id) {

            $r = oci_parse($conn,
                "BEGIN user_delete_object(:id, :user); END;");
            oci_bind_by_name($r, 'id', $id);
            oci_bind_by_name($r, 'user', $logged['user_id']);

            $oci_res = oci_execute($r);
            if (!$oci_res) {
                print_r(oci_error($r));
            }
        }
        
    }
    
    print_r(from());
        
    come_back_or_to_index();
?>