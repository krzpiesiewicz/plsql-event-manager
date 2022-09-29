<?php
    require_once 'web_helper.php';
    
    setcookie('logged/user_id', '', time()-3600, "/");
    setcookie('logged/email', '', time()-3600, "/");
    setcookie('logged/name', '', time()-3600, "/");
    setcookie('logged/surname', '', time()-3600, "/");
    setcookie('logged/passhash', '', time()-3600, "/");
    header('Location: index.php');
//    come_back_or_to_index();
?>