<?php require_once 'sessionSettings.php'; ?>
<?php

function getUser() {
    if (isset($_GET['getUser'])) {
        $getUser = $_GET['getUser'];
        $getUser = strip_tags($getUser);

        $server_name = $_SESSION['settings']['database']['server_name'];
        $database_name = $_SESSION['settings']['database']['database_name'];
        $root_username = $_SESSION['settings']['database']['root_username'];
        $root_password = $_SESSION['settings']['database']['root_password'];

        $conn = new mysqli($server_name, $root_username, $root_password, $database_name);

        if ($conn->connect_error) {
            echo ("Connection failed: " . $conn->connect_error);
        } else {

            $stmt1 = $conn->prepare(
                    'SELECT `username` ' .
                    'FROM `users` ' .
                    'WHERE `username` = ?;'
            );
            $param1 = $getUser;
            $stmt1->bind_param("s", $param1);
            if (!$stmt1->execute()) {
                // error
            } else {
                if (!$stmt1->fetch()) {
                    // user doesn't exist
                    $stmt1->close();
                    echo '            ' . "\n";
                    echo '            ' . '<div class="text">' . "\n";
                    echo '            ' . '    <h2>' . "\n";
                    echo '            ' . '        User: ' . $getUser . ' not found! ' . "\n";
                    echo '            ' . '    </h2>' . "\n";
                    echo '            ' . '</div>' . "\n";
                    echo '            ' . "\n";
                } else {
                    // user exists
                    $stmt1->close();
                    echo '            ' . "\n";
                    echo '            ' . '<div class="text">' . "\n";
                    echo '            ' . '    <h2>' . "\n";
                    echo '            ' . '        User: ' . $getUser . "\n";
                    echo '            ' . '    </h2>' . "\n";
                    echo '            ' . '</div>' . "\n";
                    echo '            ' . "\n";
                    //display data about the user:
                    {
                        $stmt = $conn->prepare(
                                'SELECT `gallery_id`, `gallery_name`, `gallery_description` ' .
                                'FROM `galleries` ' .
                                'WHERE `username` = ? ' .
                                'ORDER BY `gallery_id` ASC;'
                        );
                        $param = $getUser;
                        $stmt->bind_param("s", $param);
                        if (!$stmt->execute()) {
                            // error in execution
                        } else {
                            $stmt->bind_result($gallery_id, $gallery_name, $gallery_description);
                            echo '            ' . "\n";
                            echo '            ' . '<div class="text">' . "\n";
                            echo '            ' . '    <h3>' . "\n";
                            echo '            ' . '        User galleries:' . "\n";
                            echo '            ' . '    </h3>' . "\n";
                            echo '            ' . '</div>' . "\n";
                            echo '            ' . '<table class="searchresults">' . "\n";
                            echo '            ' . '    <tr>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">gallery id:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">gallery name:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '        <th class="description">' . "\n";
                            echo '            ' . '            <div class="text">gallery description:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '    </tr>' . "\n";
                            // rows:
                            while ($stmt->fetch()) {
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <div class="text"><a href="index.php?getGallery=' . $gallery_id . '" class="text">' . $gallery_id . '</a></div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <div class="text"><a href="index.php?getGallery=' . $gallery_id . '" class="text">' . $gallery_name . '</a></div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td class="description">' . "\n";
                                echo '            ' . '            <div class="text">' . $gallery_description . '</div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                            }
                            echo '            ' . '</table>' . "\n";
                            echo '            ' . "\n";
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}

getUser();
?>