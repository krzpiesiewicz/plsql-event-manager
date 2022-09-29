<?php
    require_once 'web_helper.php';
    require_once 'db_helper.php';
    require_once 'vendor/autoload.php';
    use Eloquent\Enumeration\AbstractEnumeration;
  
    class EventOrderValue extends AbstractEnumeration {
        const ID = 'event.ID';
        const NAME = 'object.NAME';
        const BEGIN_DATE = 'event.BEGIN_DATE';
        const END_DATE = 'event.END_DATE';
    }
    
    function query_timestamp_to_char($var_name) {
        return "TO_CHAR(" . $var_name . ", 'DD.MM.YYYY, HH24:MI:SS')";
    }
    
    function query_text_to_timestamp($var_name) {
        return "TO_TIMESTAMP(" . $var_name . ", 'DD.MM.YYYY, HH24:MI:SS')";
    }
    
    function compute_collumn_query($collumn_name) {
        if (in_array($collumn_name, array('BEGIN_DATE', 'END_DATE'))) {
            $collumn_query = query_timestamp_to_char($collumn_name);
        }
        else {
            $collumn_query = $collumn_name;
        }
        return $collumn_query . " AS " . $collumn_name;
    }
    
    function search_events($collumns_query, $collumns_names, $filter) {
        
        if (!empty($collumns_names)) {
            // projekcja
            $q = "SELECT";
            for ($i = 0; $i < count($collumns_query); $i++) {
                if ($i > 0) {
                    $q .= ",";
                }
                $q .= " " . compute_collumn_query($collumns_names[$i]);
            }
            $q .= " FROM ";
            
            // selekcja i sortowanie
            $q .= "(SELECT";
            for ($i = 0; $i < count($collumns_query); $i++) {
                if ($i > 0) {
                    $q .= ",";
                }
                $q .= " " . $collumns_query[$i];
            }
            
            $object = "SELECT /*+ index(object, type_and_upper_name__idx) */ ID, NAME, DEFAULT_ACCESS FROM object WHERE type = 'event'";
            
            if (!empty($filter['name'])) {
                $name = "%" . $filter['name'] . "%";
                $object .= " AND UPPER(NAME) LIKE UPPER(:name)";
//                print_r($filter['name']);
//                echo "<br><br>";
            }
            
            if (!empty($filter['name'])) {
                $name = "%" . $filter['name'] . "%";
                $object .= " AND UPPER(NAME) LIKE UPPER(:name)";
//                print_r($filter['name']);
//                echo "<br><br>";
            }
            
            $event = "SELECT /*+ index(event, event_by_begin_date__idx) */ ID, BEGIN_DATE, END_DATE FROM event";
            
            if (!empty($filter['begin_date']) || !empty($filter['end_date'])) {
                $event .= " WHERE";
            }
            
            if (!empty($filter['begin_date'])) {
                $event .= " begin_date >= TO_TIMESTAMP(:begin_date, 'DD.MM.YYYY, HH24:MI:SS')";
                if (!empty($filter['end_date'])) {
                    $event .= " AND";
                }
            }
            
            if (!empty($filter['end_date'])) {
                $event .= " end_date <= TO_TIMESTAMP(:end_date, 'DD.MM.YYYY, HH24:MI:SS')";
            }
            
            $q .= " FROM (". $event . ") event INNER JOIN (" . $object . ") object ON event.ID = object.ID WHERE";
            
            $inclusive_access = "EXISTS (SELECT 1 FROM entity_access WHERE object_id = object.id AND entity_id = :user_id)";
            
            $bind_user = false;
            
            if (!empty($filter['default_access'])) {
                
                $q .= " DEFAULT_ACCESS = :default_access";
                
                if ($filter['default_access'] == 'denied') {
                    $q .= " AND " . $inclusive_access;
                    $bind_user = true;
                }
            }
            else {
                $q .= " DEFAULT_ACCESS != 'denied' OR " . $inclusive_access;
                $bind_user = true;
            }
            
            $q .= " ORDER BY " . $filter['ord_val'] . " " . $filter['ord_type'];
            
            $q .= " OFFSET :offset ROWS FETCH NEXT :rows_cnt ROWS ONLY";
            
            $q .= ")";
            
            global $logged;
            global $conn;
            
//            print_r($filter);
//            echo "<br><br>";
            
            print_r($q);
            echo "<br><br>";
            
            $ressrc = oci_parse($conn, $q);
            if (!empty($filter['name'])) {
                oci_bind_by_name($ressrc, 'name', $name);
            }
            
            if (!empty($filter['begin_date'])) {
                oci_bind_by_name($ressrc, 'begin_date', $filter['begin_date']);
            }
            if (!empty($filter['end_date'])) {
                oci_bind_by_name($ressrc, 'end_date', $filter['end_date']);
            }
            
            if (!empty($filter['default_access'])) {
                oci_bind_by_name($ressrc, 'default_access', $filter['default_access']);
            }
            
            if ($bind_user) {
                oci_bind_by_name($ressrc, 'user_id', $logged['user_id']);
            }
            
            oci_bind_by_name($ressrc, 'offset', $filter['offset']);
            oci_bind_by_name($ressrc, 'rows_cnt', $filter['rows_cnt']);
            
            $oci_res = oci_execute($ressrc);
            if (!$oci_res) {
                print_r(oci_error($ressrc));
            }
            
            oci_fetch_all($ressrc, $events, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC + OCI_RETURN_NULLS);
            return $events;
        }
    }
    
    $project_event_record_field = function ($field_val, $collumn_name) {
        if (in_array($collumn_name, array('DEFAULT_ACCESS'))) {
            return access_type_name($field_val);
        }
        return $field_val;
    }
?>