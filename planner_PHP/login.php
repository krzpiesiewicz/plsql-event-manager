<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Planner. Logowanie</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.css" rel="stylesheet"/>
        
        <link rel="stylesheet" type="text/css" href="main.css">
    </head>
    <body>
        <?php
//            ini_set('display_errors', 1);
//            error_reporting(E_ALL|E_STRICT);
        
            require_once 'web_helper.php';
            require_once 'db_helper.php';
            require_once 'page_template.php';
            
            begin_template();
            
            $wrong_email = false;
            $wrong_password = false;
            
            if (filter_input(INPUT_POST, 'login_send') != null) {
                
                $email = filter_input(INPUT_POST, 'login/email', FILTER_VALIDATE_EMAIL);
                $pass = filter_input(INPUT_POST, 'login/password');

                $passhash = sha1($pass);
                $id = null;
                
                $r = oci_parse($conn, 'begin :id := log_user(:email, :passhash); end;');
                oci_bind_by_name($r, 'id', $id, 38, SQLT_INT);
                oci_bind_by_name($r, 'email', $email);
                oci_bind_by_name($r, 'passhash', $passhash);
                
                $oci_res = oci_execute($r);
                if (!$oci_res) {
                    echo 'ŹLE<br>';
                    $err = oci_error($r);
                    if (substr_count($err['message'], 'ORA-20000')) {
                        $wrong_email = true;
                    }
                    if (substr_count($err['message'], 'ORA-20001')) {
                        $wrong_password = true;
                    }
                }
                else {
                    setcookie('logged/user_id', $id, time() + 60 * 60 * 13, '/');
                    setcookie('logged/passhash', $passhash, time() + 60 * 60 * 13, '/');
                    setcookie('logged/email', $email, time() + 60 * 60 * 13, '/');
                    come_back_or_to_index();
                }
            }
            
            echo '<div class="row equal"><span><h1>Logowanie:</h1>';
            echo '<form action="'.curr_url_and_query().'" method="post">
                    <table>
                    <tr>
                    <td>e-mail:</td><td><input type="email" name="login/email" required="required" /></td>
                    </tr>
                    <tr>
                    <td>hasło:</td><td><input type="password" name="login/password" required="required" /></td>
                    </tr>
                    <tr>
                    <td><input type="Submit" name="login_send" value="Zaloguj"></td>
                    </tr>
                    </table>
                </form>';
            if ($wrong_email != false) {
                echo '<div class="err">Nieznany adres e-mail.</div>';
            }
            if ($wrong_password != false) {
                echo '<div class="err">Niepoprawny e-mail lub hasło.</div>';
            }
            echo '</span></div>';
            
            end_template();
        ?>
    </body>
</html>