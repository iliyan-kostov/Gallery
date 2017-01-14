<?php require_once 'sessionSettings.php'; ?>
<?php

echo "\n";
echo '        <link rel="stylesheet" href="' . $_SESSION['settings']['styles']['fonts'] . '">' . "\n"; // fonts
echo '        <link rel="stylesheet" href="' . $_SESSION['settings']['styles']['layout'] . '">' . "\n"; // layout
echo '        <link rel="stylesheet" href="' . $_SESSION['settings']['styles']['color'] . '">' . "\n"; // colors
echo '        <link rel="stylesheet" href="css/styles_chooser.css">' . "\n"; // styles chooser
echo "\n";
?>