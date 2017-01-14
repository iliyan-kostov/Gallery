<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<?php require_once 'sessionSettings.php'; ?>
<?php
if (isset($_POST['login'])) {
    require_once 'login.php';
} else if (isset($_POST['logout'])) {
    require_once 'logout.php';
} else if (isset($_GET['setStyle'])) {
    require_once 'setStyle.php';
}
?>
<html>
    <head>
        <meta charset="UTF-8">
<?php require 'sessionSettings_getStyleTags.php'; ?>
        <title></title>
    </head>
    <body>
        <div class="topnav">
<?php require 'index_topnav.php'; ?>
        </div>
        <div class="content">
<?php require 'index_content.php'; ?>
        </div>
    </body>
</html>
