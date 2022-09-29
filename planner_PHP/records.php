<?php
    require_once 'event_helper.php';
    
    function selectable_record_table($collumn_names, $collumn_titles, $records, $checkbox_name, $on_change_checkbox,
            $collumns_for_post, $post_names, $button, $proj_fun) {
        
        $c_cnt = sizeof($collumn_names);
        
        echo '<table class="table table-striped"><thead><tr><th></th>';
        for ($c = 0; $c < $c_cnt; $c++) {
            echo '<th>' . $collumn_titles[$collumn_names[$c]] . '</th>';
        }
        echo '</tr></thead>';
        
        for ($r = 0; $r < sizeof($records); $r++) {
            
            $record_query = array();
            for ($i = 0; $i < sizeof($collumns_for_post); $i++) {
                $record_query[$post_names[$collumns_for_post[$i]]] = $records[$r][$collumns_for_post[$i]];
            }
            
            echo '<tr>';
            
            if (!empty($checkbox_name)) {
                echo '<th><label for="hello">
                 <input type="checkbox" name="' . $checkbox_name . '" ' .
                 'value=\'' . json_encode($record_query) . '\' onclick="' . $on_change_checkbox . '(this)"/>
                 </label></th>';
            }
            
            for ($c = 0; $c < $c_cnt; $c++) {
                echo '<th>' . $proj_fun($records[$r][$collumn_names[$c]], $collumn_names[$c]) . '</th>';
            }
            
            if (!empty($button)) {
                echo '<th>' . $button . '</th>';
            }
            echo '</tr>';
        }
        echo '</tr></table>';
    }
?>