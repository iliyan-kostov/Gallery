<?php require_once 'sessionSettings.php'; ?>
<?php

echo "\n";

if (isset($_GET['login'])) {
    
} else if (isset($_GET['logout'])) {
    
} else if (isset($_GET['myGalleries'])) {
    require 'myGalleries.php';
} else if (isset($_GET['search'])) {
    require 'search.php';
} else if (isset($_GET['getGallery'])) {
    echo '<div class="text">GetGallery page</div>';
    require 'getGallery.php';
} else if (isset($_GET['getImage'])) {
    echo '<div class="text">GetImage page</div>';
    require 'getImage.php';
} else if (isset($_GET['getUser'])) {
    echo '<div class="text">GetUser page</div>';
    require 'getUser.php';
} else {
    echo '<div class="text">Default page</div>';
    require 'default.php';
}
?>
