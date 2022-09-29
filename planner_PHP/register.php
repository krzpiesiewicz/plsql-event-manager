<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Planner. Rejestracja</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
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
            
            if (filter_input(INPUT_POST, 'register_send') != null) {
                
                $name = filter_input(INPUT_POST, 'register/name');
                $surname = filter_input(INPUT_POST, 'register/surname');
                $email = filter_input(INPUT_POST, 'register/email', FILTER_VALIDATE_EMAIL);
                $pass = filter_input(INPUT_POST, 'register/password');

                $passhash = sha1($pass);
                $id = null;
                
                $r = oci_parse($conn, 'begin :id := add_user(:name, :surname, :email, :passhash); end;');
                oci_bind_by_name($r, 'id', $id, 38, SQLT_INT);
                oci_bind_by_name($r, 'name', $name);
                oci_bind_by_name($r, 'surname', $surname);
                oci_bind_by_name($r, 'email', $email);
                oci_bind_by_name($r, 'passhash', $passhash);
                
                
                $oci_res = oci_execute($r);
                if (!$oci_res) {
                    print_r(oci_error($r));
                }
                else {
                    setcookie('logged/user_id', $id, time() + 60 * 60 * 13, '/');
                    setcookie('logged/passhash', $passhash, time() + 60 * 60 * 13, '/');
                    setcookie('logged/email', $email, time() + 60 * 60 * 13, '/');
                    come_back_or_to_index();
                }
            }
            
            echo '<div class="row equal"><span><h1>Rejestracja:</h1>';
            echo '
                <form action="'.curr_url_and_query().'" method="post">
                    <table>
                    <tr>
                    <td>imię:</td><td><input type="text" name="register/name" id="name" required="required"
                    pattern="'.name_regex().'" title="Imię musi składać się z samych liter, zaczynając się z wielką literą."/></td>
                    </tr>
                    <tr>
                    <td>nazwisko:</td><td><input type="text" name="register/surname" id="surname" required="required"
                    pattern="'.surname_regex().'" title="Nazwisko musi składać się z samych liter, zaczynając się z wielką literą."/></td>
                    </tr>
                    <tr>
                    <td>e-mail:</td><td><input type="email" name="register/email" required="required" /></td>
                    </tr>
                    <tr>
                    <td>hasło:</td><td><input type="password" name="register/password" id="psw" required="required"
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Musi zawierać co najmniej jedną: cyfrę, małą i wielką literę oraz musi składać się z co najmniej 8 znaków." /></td>
                    </tr>
                    <tr>
                    <td><input type="Submit" name="register_send" value="Zarejestruj"></td>
                    </tr>
                    </table>
                </form>';
            echo '</span></div>';
            end_template();
        ?>
    </body>
</html>