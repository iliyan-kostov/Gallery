<?php require_once 'sessionSettings.php'; ?>
<?php

echo "\n";

if (isset($_GET['myGalleries'])) {
    require 'myGalleries.php';
} else if (isset($_GET['search'])) {
    require 'search.php';
} else if (isset($_GET['getGallery'])) {
    require 'getGallery.php';
} else if (isset($_GET['getImage'])) {
    require 'getImage.php';
} else if (isset($_GET['getUser'])) {
    require 'getUser.php';
} else {
    require 'homepage.php';
}
?>
