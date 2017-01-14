<?php require_once 'sessionSettings.php'; ?>
<?php

function logout() {
    if (isset($_SESSION['username'])) {
        unset($_SESSION['username']);
    }
}

logout();
?>