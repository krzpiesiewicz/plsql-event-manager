<?php
    require_once 'vendor/autoload.php';
    use Eloquent\Enumeration\AbstractEnumeration;
    
    class OrderType extends AbstractEnumeration {
        const ASC = 'ASC';
        const DSC = 'DSC';
    }
    
    function access_type_name($access_type) {
        switch ($access_type) {
            case 'owner' : return 'właściciel';
            case 'write' : return 'każdy może edytować';
            case 'read' : return 'widoczne dla wszystkich';
            case 'denied' : return 'prywatne';
            default : return $access_type;
        }
    }
    
    function plsql($plsql_text) {
        return "BEGIN ".$plsql_text." END";
    }
    
    
    
    require_once(dirname(dirname(__DIR__))."/.login/login.php");
    
//    function log_into_db() {
//        require_once(dirname(dirname(__DIR__))."/.login/login.php");
//        return $conn;
//    }
//    $conn = log_into_db();
?>