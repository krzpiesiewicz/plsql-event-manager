<?php

    if(filter_input(INPUT_SERVER, 'HTTPS') != "on")
    {
        header("Location: https://".filter_input(INPUT_SERVER, 'HTTP_HOST').filter_input(INPUT_SERVER, 'REQUEST_URI'));
        exit();
    }
    
//    if(session_id() == '') {
//        session_start();
//    }
    
    function name_regex() {
        return '[A-ZĆŁŃŚŹŻ][a-ząćęłńóśźż]*';
    }
    
    function surname_regex() {
        return '[A-ZĆŁŃŚŹŻ][a-ząćęłńóśźż]*';
    }
    
    function null_in_array($arr) {
        foreach ($arr as $v) {
            if ($v == null) {
                return true;
            }
        }
        return false;
    }
    
    $logged = array (
        'user_id' => filter_input(INPUT_COOKIE, 'logged/user_id'),
        'email' => filter_input(INPUT_COOKIE, 'logged/email'),
        'passhash' => filter_input(INPUT_COOKIE, 'logged/passhash')
//        ,
//        'name' => filter_input(INPUT_COOKIE, 'logged/name'),
//        'surname' => filter_input(INPUT_COOKIE, 'logged/surname')
    );
    
    $is_logged = !null_in_array($logged);
    
    function root() {
        return '/~kp385996/planner/';
    }
    
    function without_root($url) {
        return str_replace(root(), "", $url);
    }
    
    function curr_page() {
        $res = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_URL);
        return without_root($res);
    }
    
    function curr_query() {
        return filter_input(INPUT_SERVER, "QUERY_STRING");
    }
    
    function curr_url_and_query() {
        $res = curr_page();
        $query_string = curr_query();
        if ($query_string != null) {
            $res .= '?' . $query_string;
        }
        return $res;
    }
    
    function curr_url_and_query_encoded() {
        return rawurlencode(curr_url_and_query());
    }
    
    function from() {
        $url = filter_input(INPUT_GET, 'from', FILTER_SANITIZE_URL);
        $res = rawurldecode($url);
//        echo 'url: ';print_r($url);echo '<br>res: ';print_r($res);echo '<br>';
        return $res;
    }
    
    function from_not_set() {
        return from() == false;
    }
    
    function come_back_or_to_index() {
        $page = from();
        
        if (page == null) {
            $page = 'index.php';
        }
        header('Location:' . $page);
        exit();
    }
?>