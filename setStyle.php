<!DOCTYPE html>
<?php require_once 'sessionSettings.php'; ?>
<?php

function setStyle() {
    if (isset($_GET['setStyle'])) {
        $setStyle = $_GET['setStyle'];
        $setStyle = strip_tags($setStyle);
        $styleExists = FALSE;
        switch ($setStyle) {
            case 'color_green':
                $styleExists = TRUE;
                break;
            case 'color_blue':
                $styleExists = TRUE;
                break;
        }
        if ($styleExists) {
            $_SESSION['settings']['styles']['color'] = 'css/' . $setStyle . '.css'; // colors CSS file
        }
    }
}

setStyle();
?>
