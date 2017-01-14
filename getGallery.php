<?php require_once 'sessionSettings.php'; ?>
<?php

function getGallery() {
    if (isset($_GET['getGallery'])) {
        $getGallery = $_GET['getGallery'];
        $getGallery = strip_tags($getGallery);

        // perform search:
        {
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
                            'SELECT `username`, `gallery_name`, `gallery_id`, `gallery_description` ' .
                            'FROM `galleries` ' .
                            'WHERE `gallery_id` = ?;'
                    );
                    $param1 = $getGallery;
                    $stmt->bind_param("s", $param1);
                    if ($stmt->execute()) {
                        $stmt->bind_result($username, $gallery_name, $gallery_id, $gallery_description);
                        if (!$stmt->fetch()) {
                            // failed fetch
                            echo '            ' . "\n";
                            echo '            ' . '<div class="text">' . "\n";
                            echo '            ' . '    <h2>' . "\n";
                            echo '            ' . '        No such gallery found!' . $gallery_name . "\n";
                            echo '            ' . '    </h2>' . "\n";
                            echo '            ' . '</div>' . "\n";
                            $stmt->close();
                        } else {
                            // successful fetch
                            $stmt->close();

                            // table with general information about the gallery:
                            {
                                echo '            ' . "\n";
                                echo '            ' . '<div class="text">' . "\n";
                                echo '            ' . '    <h2>' . "\n";
                                echo '            ' . '        Displaying: ' . $gallery_name . "\n";
                                echo '            ' . '    </h2>' . "\n";
                                echo '            ' . '</div>' . "\n";
                                echo '            ' . '<table class="searchresults">' . "\n";
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">gallery id:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">username:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">gallery name:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th class="description">' . "\n";
                                echo '            ' . '            <div class="text">gallery description:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <div class="text">' . $gallery_id . '</div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <div class="text"><a href="index.php?getUser=' . $username . '" class="text">' . $username . '</a></div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <div class="text">' . $gallery_name . '</div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td class="description">' . "\n";
                                echo '            ' . '            <div class="text">' . $gallery_description . '</div>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                                echo '            ' . '</table>' . "\n";
                            }

                            // table with gallery contents:
                            {
                                echo '            ' . "\n";
                                echo '            ' . '<div class="text">' . "\n";
                                echo '            ' . '    <h3>' . "\n";
                                echo '            ' . '        Gallery contents:' . "\n";
                                echo '            ' . '    </h3>' . "\n";
                                echo '            ' . '</div>' . "\n";
                                echo '            ' . '<table class="searchresults">' . "\n";
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">image preview:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">image id:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">image name:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th class="description">' . "\n";
                                echo '            ' . '            <div class="text">image description:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                                // rows
                                {
                                    $stmt2 = $conn->prepare(
                                            'SELECT `image_name`, `image_id`, `image_description` ' .
                                            'FROM `images` ' .
                                            'WHERE `images`.`gallery_id` = ? ' .
                                            'ORDER BY `image_id` ASC;'
                                    );
                                    $param2 = $gallery_id;
                                    $stmt2->bind_param("s", $param2);
                                    if ($stmt2->execute()) {
                                        $stmt2->bind_result($image_name, $image_id, $image_description);
                                        while ($stmt2->fetch()) {
                                            $image_path = 'galleries/' . $gallery_id . '/' . $image_name;
                                            echo '            ' . '    <tr>' . "\n";
                                            echo '            ' . '        <td>' . "\n";
                                            echo '            ' . '            <a href="index.php?getImage=' . $image_id . '"><img src="' . $image_path . '" class="preview"></a>' . "\n";
                                            echo '            ' . '        </td>' . "\n";
                                            echo '            ' . '        <td>' . "\n";
                                            echo '            ' . '            <div class="text"><a href="index.php?getImage=' . $image_id . '">' . $image_id . '</a></div>' . "\n";
                                            echo '            ' . '        </td>' . "\n";
                                            echo '            ' . '        <td>' . "\n";
                                            echo '            ' . '            <div class="text"><a href="index.php?getImage=' . $image_id . '">' . $image_name . '</a></div>' . "\n";
                                            echo '            ' . '        </td>' . "\n";
                                            echo '            ' . '        <td class="description">' . "\n";
                                            echo '            ' . '            <div class="text">' . $image_description . '</div>' . "\n";
                                            echo '            ' . '        </td>' . "\n";
                                            echo '            ' . '    </tr>' . "\n";
                                        }
                                    }
                                    $stmt2->close();
                                }
                                echo '            ' . '</table>' . "\n";
                            }
                        }
                    }
                    //$stmt->close();
                }
            }
            $conn->close();
        }
    }
}

getGallery();
?>