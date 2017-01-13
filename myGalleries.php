<?php require_once 'sessionSettings.php'; ?>
<?php

function myGalleries() {
    if (!isset($_SESSION['username'])) {
        // user not logged in
        echo '        ' . "\n";
        echo '        ' . '<div class="text">' . "\n";
        echo '        ' . '    <h2>' . "\n";
        echo '        ' . '        You have to log in to see your galleries!' . "\n";
        echo '        ' . '    </h2>' . "\n";
        echo '        ' . '</div>' . "\n";
        echo '        ' . "\n";
    } else {
        // display user's galleries:
        {
            $username = $_SESSION['username'];

            $server_name = $_SESSION['settings']['database']['server_name'];
            $database_name = $_SESSION['settings']['database']['database_name'];
            $root_username = $_SESSION['settings']['database']['root_username'];
            $root_password = $_SESSION['settings']['database']['root_password'];

            $conn = new mysqli($server_name, $root_username, $root_password, $database_name);

            if ($conn->connect_error) {
                echo ("Connection failed: " . $conn->connect_error);
            } else {
                // search for galleries:
                {
                    $stmt = $conn->prepare(
                            "SELECT `gallery_id`, `gallery_name`, `gallery_description` " .
                            "FROM `galleries` " .
                            "WHERE `username` = ? " .
                            "ORDER BY `gallery_id` ASC;"
                    );
                    $param = '%' . $username . '%';
                    $stmt->bind_param("s", $param);
                    if ($stmt->execute()) {
                        $stmt->bind_result($gallery_id, $gallery_name, $gallery_description);
                        echo '        ' . "\n";
                        echo '        ' . '<div class="text">' . "\n";
                        echo '        ' . '    <h2>' . "\n";
                        echo '        ' . '        List of your galleries:' . "\n";
                        echo '        ' . '    </h2>' . "\n";
                        echo '        ' . '</div>' . "\n";
                        echo '        ' . '<table class="searchresults">' . "\n";
                        echo '        ' . '    <tr>' . "\n";
                        echo '        ' . '        <th>' . "\n";
                        echo '        ' . '            <div class="text">gallery id:</div>' . "\n";
                        echo '        ' . '        </th>' . "\n";
                        echo '        ' . '        <th>' . "\n";
                        echo '        ' . '            <div class="text">gallery name:</div>' . "\n";
                        echo '        ' . '        </th>' . "\n";
                        echo '        ' . '        <th>' . "\n";
                        echo '        ' . '            <div class="text">gallery description:</div>' . "\n";
                        echo '        ' . '        </th>' . "\n";
                        echo '        ' . '    </tr>' . "\n";
                        while ($stmt->fetch()) {
                            echo '        ' . '    <tr>' . "\n";
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            <a class="text" href="index.php?getGallery=' . $gallery_id . '">' . $gallery_id . '</a>' . "\n";
                            echo '        ' . '        </td>' . "\n";
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            <a class="text" href="index.php?getGallery=' . $gallery_id . '">' . $gallery_name . '</a>' . "\n";
                            echo '        ' . '        </td>' . "\n";
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            ' . $gallery_description . "\n";
                            echo '        ' . '        </td>' . "\n";
                            echo '        ' . '    </tr>' . "\n";
                        }
                        echo '        ' . '</table>' . "\n";
                        echo '        ' . "\n";
                    }
                    $stmt->close();
                }
            }
            $conn->close();
        }
    }
}

myGalleries();
?>