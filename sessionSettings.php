<?php require_once 'sessionSettings.php'; ?>
<?php

// session initialization:
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = array();
}

// database and data settings:
{
    if (!isset($_SESSION['settings']['database'])) {
        $_SESSION['settings']['database'] = array();
    }
    
    $_SESSION['settings']['database']['server_name'] = 'INSERT_SERVER_NAME_HERE';
    $_SESSION['settings']['database']['database_name'] = 'INSERT_DATABASE_NAME_HERE';
    $_SESSION['settings']['database']['root_username'] = 'INSERT_ROOT_USERNAME_HERE';
    $_SESSION['settings']['database']['root_password'] = 'INSERT_ROOT_PASSWORD_HERE';

    $_SESSION['settings']['database']['max_image_name_length'] = 30; //30 characters
    $_SESSION['settings']['database']['max_gallery_name_length'] = 30; // 30 characters
    $_SESSION['settings']['database']['min_username_length'] = 5; // 5 characters
    $_SESSION['settings']['database']['max_username_length'] = 30; // 30 characters
    $_SESSION['settings']['database']['min_password_length'] = 5; // 5 characters
    $_SESSION['settings']['database']['max_password_length'] = 30; // 30 characters
    $_SESSION['settings']['database']['max_descriptions_length'] = 500; // 500 characters
    $_SESSION['settings']['database']['max_comments_length'] = 500; // 500 characters
}

// styles settings:
{
    if (!isset($_SESSION['settings']['styles'])) {
        $_SESSION['settings']['styles'] = array();
    }

    $_SESSION['settings']['styles']['fonts'] = 'https://fonts.googleapis.com/css?family=Open+Sans'; // fonts CSS file
    $_SESSION['settings']['styles']['layout'] = 'css/layout.css'; // layout CSS file
    if (!isset($_SESSION['settings']['styles']['color'])) {
        $_SESSION['settings']['styles']['color'] = 'css/color_green.css'; // colors CSS file
    }
}
?>