<?php
    require_once 'web_helper.php';
    require_once 'db_helper.php';

    function edit_event($post_name, $is_new) {
    
        global $conn;
        global $logged;
        
        $php_handler = $is_new ? 'add_event.php' : 'update_event.php';
        $send_button_title = $is_new ? 'Stwórz wydarzenie' : 'Aktualizuj wydarzenie';
        
        $validate = 'return validate_event(\'' . $post_name .'_begin_date\', \'' . $post_name .'_end_date\')';
        
        
        
        echo '
        <form id="form_' . $post_name . '" action="' . $php_handler . '?from=' . curr_url_and_query_encoded() . '" method="post" '
        . ' onsubmit="' . $validate . '" >
        <table>';
        
        $disabled = "";
        $id = "";
        $name ="";
        $begin_date = "";
        $end_date = "";
        $default_access= "";
        
        if (!$is_new) {
            $id = filter_input(INPUT_POST, $post_name . '/id');
            if (is_null($id)) {
                $disabled = 'disabled="disabled"';
            }
            else {
                $name = filter_input(INPUT_POST, $post_name . '/');
                $begin_date = filter_input(INPUT_POST, $post_name . '/begin_date');
                $end_date = filter_input(INPUT_POST, $post_name . '/end_date');
                $default_access = filter_input(INPUT_POST, $post_name . '/access');
            }
            
            echo '<tr>
            <td>id wydarzenia:</td><td><input class="form-control" type="text" value="' . $id . '" name="' . $post_name . '/id"
            id="' . $post_name . '_id" required="required" ' . $disabled . ' readonly="readonly"/></td>
            </tr>';
        }
        
        echo '
        <tr>
        <td>nazwa wydarzenia:</td><td><input class="form-control" type="text" value="' . $name . '" name="' . $post_name . '/name" id="'
        . $post_name . '_name"
        required="required" ' . $disabled . '/></td>
        </tr>
        <tr>
        <td>data początku:</td><td><div class="input-group date">
        <input class="form-control" type="text" value="' . $begin_date . '" name="' . $post_name . '/begin_date" id="' . $post_name
        . '_begin_date" required="required"
        placeholder="DD.MM.RRRR, HH:MM:SS" ' . $disabled . '/>
        </div></td>
        </tr>
        <tr>
        <td>data końca:</td><td><div class="input-group date">
        <input class="form-control" type="text" value="' . $end_date . '" name="' . $post_name . '/end_date" id="' . $post_name
        . '_end_date" required="required" placeholder="DD.MM.RRRR, HH:MM:SS" ' . $disabled . '/>
        </div></td>                    
        </tr>
        <tr>
        <td>
        domyślny dostęp: 
        </td>
        <td>
        <select class="form-control" name="' . $post_name . '/default_access" id="' . $post_name . '_default_access" '
        .' required="required" ' . $disabled . '.>';

        $ressrc = oci_parse($conn, "SELECT * FROM access_type");
        $oci_res = oci_execute($ressrc);
        if (!$oci_res) {
            print_r(oci_error($ressrc));
        }
        $access_count = oci_fetch_all($ressrc, $access_types, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC + OCI_RETURN_NULLS);

        $default_new_access = 'read';
        for ($i = 0; $i < $access_count; $i++) {
            $access_type = $access_types[$i]['NAME'];
            if ($access_type != 'owner') {
                echo '<option value="' . $access_type . '"';
                if ($access_type == $default_access || (empty($default_access) && $access_type == $default_new_access)) {
                    echo ' selected="selected"';
                }
                echo '>' . access_type_name($access_type) . '</option>';
            }
        }
        echo '
        </select>
        </td>
        </tr>
        <tr>
        <td>';
        if ($is_new) {
            echo '<input type="hidden" name="' . $post_name . '/owner" value="' . $logged['user_id'] . '">';
        }
        echo '<input type="Submit" onclick="' . $validate . '" id="' . $post_name . '_send"'
                . ' name="' . $post_name . '_send" value="' . $send_button_title . '" ' . $disabled . '></td>
        </tr>
        </table>
        </form>';
    }
?>