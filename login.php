<?php require_once 'sessionSettings.php'; ?>
<?php

function login() {
    if (!isset($_SESSION['username']) && isset($_POST['username']) && isset($_POST['password'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $username = str_replace(" ", "", $username);
        $password = str_replace(" ", "", $password);
        $username = substr($username, 0, $_SESSION['settings']['database']['max_username_length']);
        $password = substr($password, 0, $_SESSION['settings']['database']['max_password_length']);
        $username = strip_tags($username);
        $password = strip_tags($password);

        if (strlen($username) < $_SESSION['settings']['database']['min_username_length']) {
            echo "Username too short!";
        } else if (strlen($password) < $_SESSION['settings']['database']['min_password_length']) {
            echo "Password too short!";
        } else {

            $server_name = $_SESSION['settings']['database']['server_name'];
            $database_name = $_SESSION['settings']['database']['database_name'];
            $root_username = $_SESSION['settings']['database']['root_username'];
            $root_password = $_SESSION['settings']['database']['root_password'];

            $conn = new mysqli($server_name, $root_username, $root_password, $database_name);

            if ($conn->connect_error) {
                echo ("Connection failed: " . $conn->connect_error);
            } else {
                $stmt = $conn->prepare(
                        "SELECT `username`, `password` " .
                        "FROM `users` " .
                        "WHERE `username` = ?;"
                );
                $stmt->bind_param("s", $username);
                if ($stmt->execute()) {
                    $stmt->bind_result($name, $pass);
                    $success = $stmt->fetch();
                    if ($success === NULL) {
                        // username not registered - register new user:
                        $stmt2 = $conn->prepare(
                                "INSERT INTO `users` (`username`, `password`) " .
                                "VALUES (? , ?);"
                        );
                        $stmt2->bind_param("ss", $username, $password);
                        if ($stmt2->execute()) {
                            $_SESSION['username'] = $username;
                        };
                        $stmt2->close();
                    } else if ($success === TRUE) {
                        // username registered - check passwords:
                        if ($pass == $password) {
                            $_SESSION['username'] = $username;
                        } else {
                            // wrong password
                            echo "Wrong password!";
                        }
                    } else {
                        // error occured
                    }
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
}

login();
?>