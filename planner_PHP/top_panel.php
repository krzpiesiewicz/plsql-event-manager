<?php

    require_once 'web_helper.php';

    function echo_nav_bar_link($to_page, $title, $pass_querry = false, $pos = 'left') {
        $page = curr_page();
        echo '<li class="' . $pos . '"><a class="elem';
        if ($page == $to_page) {echo ' active';}
        echo '" href="' . $to_page;
        if ($pass_querry && passing_from()) {
            echo '?from=' . curr_url_and_query_encoded();
        }
        echo '">' . $title . '</a></li>';
    }
    
    function passing_from() {
        return !in_array(curr_page(), array('login.php', 'register.php'));
    }

    function echo_header() {
        
        echo '<header id="HEADER"><ul class="topnav">';
        
        echo_nav_bar_link('index.php', 'Main');
        
        global $is_logged;
        global $logged;
        
        if ($is_logged) {
            echo_nav_bar_link('events.php', 'Wydarzenia');
            echo_nav_bar_link('calendars.php', 'Kalendarze');
            echo_nav_bar_link('users_and_groups.php', 'Użytkownicy i grupy');
        
            echo_nav_bar_link('logout.php', 'Wyloguj', true, 'right');
            echo '<li class="elem right"><i>Zalogowano jako: ' . $logged['email'] . '</i></li>';
        }
        else {
            echo_nav_bar_link('login.php', 'Logowanie', true, 'right');
            echo_nav_bar_link('register.php', 'Rejestracja', true, 'right');
        }
        
        echo '</ul>
        </header>
        ';
    }
?>