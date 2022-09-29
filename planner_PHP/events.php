<!DOCTYPE html>
<html lang="pl" xml:lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Planner. Wydarzenia</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/locale/pl.js"></script>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.css" rel="stylesheet"/>
        
        <link rel="stylesheet" type="text/css" href="main.css">
        
        <script>                     
            $datetime_options = {
                locale: 'pl',
                format: 'DD.MM.YYYY, HH:mm:ss',
//                sideBySide: true,
                showTodayButton: true,
                showClose: true,
                useCurrent: false,
                keyBinds: function(){}
            };
            
            $(function () {
                $('#new_event_begin_date').datetimepicker($datetime_options);
            });
            $(function () {
                $('#new_event_end_date').datetimepicker($datetime_options);
            });
            
            $(function () {
                $('#edited_event_begin_date').datetimepicker($datetime_options);
            });
            $(function () {
                $('#edited_event_end_date').datetimepicker($datetime_options);
            });
            
            $datetime_options_for_filter = $datetime_options;
            $datetime_options_for_filter.showClear = true;

            
            $(function () {
                $('#event_filter_begin_date').datetimepicker($datetime_options_for_filter);
            });
            $(function () {
                $('#event_filter_end_date').datetimepicker($datetime_options_for_filter);
            });
            
            function check_dates(begin_date_id, end_date_id) {
                
                var begin_date = document.getElementById(begin_date_id);
                var end_date = document.getElementById(end_date_id);
                
                if(begin_date.value !== "" && end_date.value !== "") {
                    var begin_moment = moment(begin_date.value, 'DD.MM.YYYY, HH:mm:ss');
                    var end_moment = moment(end_date.value, 'DD.MM.YYYY, HH:mm:ss');

                    if (begin_moment > end_moment) {
                        end_date.setCustomValidity("Data zakończenia musi być niewcześniejsza niż data rozpoczęcia.");
                        return false;
                    } else {
                        end_date.setCustomValidity("");
                        return true;
                    }
                }
            }
            
            function validate_event(begin_date_id, end_date_id) {
                var res = check_dates(begin_date_id, end_date_id);
                return res;
            }
            
            function validate_event_filter() {
                var res = check_dates('event_filter_begin_date', 'event_filter_end_date');
                return res;
            }
            
            function is_editable_access(access) {
                return ['owner', 'write'].includes(access);
            }
            
            function validate_del_events() {
                wrong = false;
                for (var i = 0; i < selected_records.length; i++) {
                    if (!is_editable_access(selected_records[i]['access'])) {
                        alert('Nie masz uprawnień do usunięcia wydarzenia o id: ' + selected_records[i]['id']);
                        wrong = true;
                        break;
                    }
                }
                return !wrong;
            }
        </script>
        
    </head>
    <body>
        <?php
            ini_set('display_errors', 1);
            error_reporting(E_ALL|E_STRICT);
            
            require_once 'web_helper.php';
            require_once 'db_helper.php';
            require_once 'event_helper.php';
            
            require_once 'page_template.php';
            require_once 'event_editing.php';
            require_once 'records.php';
            
            begin_template();
            
            echo '<div class="row equal">';
            
            echo '<span><h1>Utwórz wydarzenie</h1>';
            edit_event('new_event', true);
            
            echo '</span><span><h1>Szczegóły wydarzenia</h1>';
            edit_event('edited_event', false);
            
            echo "</span>";
            echo "</div>";
            
            echo '<h1>Wydarzenia</h1>';
            
            $ord_val = filter_input(INPUT_GET, 'event_filter/ord_val');
            if (is_null($ord_val)) {
                $ord_val = EventOrderValue::BEGIN_DATE();
            }
            
            $ord_type = filter_input(INPUT_GET, 'event_filter/ord_type');
            if (is_null($ord_type)) {
                $ord_type = OrderType::ASC();
            }
            
            $offset = filter_input(INPUT_GET, 'event_filter/offset');
            if (is_null($offset)) {
                $offset = 0;
            }
            
            $rows_cnt = filter_input(INPUT_GET, 'event_filter/rows_cnt');
            if (is_null($rows_cnt)) {
                $rows_cnt = 10;
            }
            
            $name_filter = filter_input(INPUT_GET, 'event_filter/name');
            $begin_date_filter = filter_input(INPUT_GET, 'event_filter/begin_date');
            $end_date_filter = filter_input(INPUT_GET, 'event_filter/end_date');
            $default_access_filter = filter_input(INPUT_GET, 'event_filter/default_access');
            
            $validate = 'return validate_event_filter()';
            
            echo '<h4>Wyszukaj po:</h4>
                <form action="events.php" method="get" onsubmit="' . $validate . '">
                
                <div class="row equal">

                <span1><table><tr><td>od: </td><td>
                <div class="input-group date">
                <input class="form-control" type="text" value="' . $begin_date_filter . '" name="event_filter/begin_date"'
                . ' id="event_filter_begin_date" placeholder="DD.MM.RRRR, HH:MM:SS"/>
                </div>
                </td><td>,<td></tr></table></span1>
                
                <span1><table><tr><tD>do: </td><td>
                <div class="input-group date">
                <input class="form-control" type="text" value="' . $end_date_filter . '" name="event_filter/end_date"'
                . ' id="event_filter_end_date" placeholder="DD.MM.RRRR, HH:MM:SS"/>
                </div>
                </td><td>,<td></tr></table></span1>
                
                <span1><table><tr><td>nazwa: </td>
                <td><input class="form-control" type="text" value="' . $name_filter . '" name="event_filter/name"'
                . ' id="event_filter/name"/>
                </td><td>,<td></tr></table></span1>
                
                <span1><table><tr><td>domyślny dostęp:</td>
                <td><select class="form-control" name="event_filter/default_access">
                <option value="" ';
             if (is_null($default_access_filter)) {
                 echo ' selected="selected"';
             }
             echo '>';

            $ressrc = oci_parse($conn, "SELECT * FROM access_type");
            $oci_res = oci_execute($ressrc);
            if (!$oci_res) {
                print_r(oci_error($ressrc));
            }
            $access_count = oci_fetch_all($ressrc, $access_types, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC + OCI_RETURN_NULLS);

            $default_new_access = '';
            for ($i = 0; $i < $access_count; $i++) {
                $access_type = $access_types[$i]['NAME'];
                if ($access_type != 'owner') {
                    echo '<option value="' . $access_type . '"';
                    if ($access_type == $default_access_filter) {
                        echo ' selected="selected"';
                    }
                    echo '>' . access_type_name($access_type) . '</option>';
                }
            }
            echo '</select>
                </td><td>,<td></tr></table></span1>

                <span1><table><tr><td>liczba pozycji:</td>
                <td><input class="form-control" type="number" step="1" min="5" max="200" value="' . $rows_cnt . '" name="event_filter/rows_cnt">
                </td><td>,<td></tr></table></span1>

                <span1><table><tr><td>
                <input type="Submit" onclick="' . $validate . '" name="event_filter_send" value="Wyszukaj">
                </td></tr></table></span1>

                </div>

                </form>';
            
            $filter = array(
                'ord_val' => $ord_val,
                'ord_type' => $ord_type,
                'offset' => $offset,
                'rows_cnt' => $rows_cnt,
                'name' => $name_filter,
                'begin_date' => $begin_date_filter,
                'end_date' => $end_date_filter,
                'default_access' => $default_access_filter
            );
            
            $collumns_names = array (
                0 => 'ID',
                1 => 'NAME',
                2 => 'BEGIN_DATE',
                3 => 'END_DATE',
                4 => 'DEFAULT_ACCESS'
            );
            
            $collumns_query = array (
                0 => 'event.ID',
                1 => 'object.NAME',
                2 => 'event.BEGIN_DATE',
                3 => 'event.END_DATE',
                4 => 'object.DEFAULT_ACCESS'
            );
            
            $collumns_titles = array (
                'ID' => 'id',
                'NAME' => 'nazwa',
                'BEGIN_DATE' => 'data rozpoczęcia',
                'END_DATE' => 'data zakończenia',
                'DEFAULT_ACCESS' => 'domyślny dostęp'
            );
            
            $post_names = array (
                'ID' => 'id',
                'NAME' => 'name',
                'BEGIN_DATE' => 'begin_date',
                'END_DATE' => 'end_date',
                'DEFAULT_ACCESS' => 'default_access'
            );
            
            $collumns_for_post = array (
                0 => 'ID',
                1 => 'NAME',
                2 => 'BEGIN_DATE',
                3 => 'END_DATE',
                4 => 'DEFAULT_ACCESS'
            );
            
            $events = search_events($collumns_query, $collumns_names, $filter);
            
            
            echo '<h4>Zaznaczone:</h4>
                <div class="row equal">
                <form action="delete_objects.php?from=' . curr_url_and_query_encoded() . '" method="post" onsubmit="return validate_del_events()">
                <input type="hidden" name="ids_to_delete" value="" id="ids_to_send_to_delete">
                <span1><input type="Submit" id="delete_selected" value="Usuń zaznaczone" disabled="disabled"'
                .'></span1>
                </form></div>';             
            
            selectable_record_table($collumns_names, $collumns_titles, $events,
                    'selected_events', 'records_checkbox_change', $collumns_for_post, $post_names, NULL, $project_event_record_field);
            
            
            
            end_template();
        ?>
        <script>
            
            var selected_records_cnt = 0;
            var selected_records_ids = [];
            var selected_records = [];
            
            function records_checkbox_change(checkbox) {
                
                var record = JSON.parse(checkbox.value);
                var access = 'denied';
                
                params = {
                    user_id: $.cookie('logged/user_id'),
                    object_id: record['id']
                };
                
                var access = $.ajax({
                    type: 'POST',
                    url: "check_user_access_to_object.php",
                    data: params,
                    cache: false,
                    async: false
                }).responseText;
                
                record['access'] = access;
                
                
                if (checkbox.checked) {
                    selected_records_cnt++;
                    selected_records_ids.push(record['id']);
                    selected_records.push(record);
                }
                else {
                    selected_records_cnt--;
                    selected_records_ids = selected_records_ids.filter(function(item) {
                        return item !== record['id'];
                    });
                    selected_records = selected_records.filter(function(item) {
                        return item['id'] !== record['id'];
                    });
                    record = selected_records[selected_records.length - 1];
                }
                
                var send_to_edit = document.getElementById('send_event_to_edit');
                
                function set_field(what, clear) {
                    var item = document.getElementById('edited_event_' + what);
                    if (clear) {
                        item.value = "";
                    }
                    else {
                        item.value = record[what];
                    }
                }
                
                function set_field_editable(what, editable) {
                    var item = document.getElementById('edited_event_' + what);
                    if (editable) {
                        item.disabled = "";
                    }
                    else {
                        item.disabled = "disabled";
                    }
                }
                
                function set_field_for_owner(what, editable) {
                    var item = document.getElementById('edited_event_' + what);
                    if (editable) {
                        item.disabled = "";
                    }
                    else {
                        item.disabled = "disabled";
                    }
                }
                
                var fields = ['id', 'name', 'begin_date', 'end_date', 'default_access'];
                var editable_fields = ['id', 'name', 'begin_date', 'end_date'];
                var fields_for_owner = ['default_access'];
                
                function set_fields(clear) {
                    fields.forEach(function(what) {
                        set_field(what, clear);
                    });
                }
                
                function set_fields_editable(editable) {
                    
                    editable_fields.forEach(function(what) {
                        set_field_editable(what, editable);
                    });
                    
                    fields_for_owner.forEach(function(what) {
                        set_field_editable(what, editable && record['access'] == 'owner');
                    });
//                    set_field_editable('default_access', editable && record['access'] == 'owner');
                    
                    set_field_editable('send', editable);
                    var url = '<?php require_once 'web_helper.php'; echo curr_url_and_query_encoded(); ?>';
                    console.log(url);
                    console.log('update_event.php?from=' + url);
                    document.getElementById('form_edited_event').action = 'update_event.php?from=' + url;
                }
                
                if (selected_records_cnt === 0) {
                    set_fields(true);
                    set_fields_editable(false);
                }
                else {
                    set_fields(false);
                    set_fields_editable(is_editable_access(record['access']));
                }
                
                var delete_selected = document.getElementById('delete_selected');
                var ids_to_send_to_delete =  document.getElementById('ids_to_send_to_delete');
                             
                
                if (selected_records_cnt > 0) {
                    delete_selected.disabled="";
                    ids_to_send_to_delete.value = JSON.stringify(selected_records_ids);
                }
                else {
                    delete_selected.disabled="disabled";
                    ids_to_send_to_delete.value="";
                }
            }
            
            var checkbox_list = document.getElementsByName("selected_events");

            for (var i = 0; i < checkbox_list.length; i++) {
                if (checkbox_list[i].checked) {
                    records_checkbox_change(checkbox_list[i]);
                }
            }
        </script>
    </body>
</html>