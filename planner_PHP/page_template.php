<?php
    require_once 'top_panel.php';
    
    function begin_template() {
        echo_header();
        echo '<div id="CONTENT-BOX" class="container well">';
//        . '<article id="CONTENT"';
        echo '<div class="container"';
        if ($centered) {
            echo ' class="centered"';
        }
        echo '>';
    }
    
    function end_template() {
        echo '</div>';
//        echo '</article>';
        echo '</div>';
    }
?>