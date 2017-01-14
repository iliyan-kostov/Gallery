<?php require_once 'sessionSettings.php'; ?>
<?php

function myGalleries() {
    if (!isset($_SESSION['username'])) {
        // user not logged in
        echo '        ' . "\n";
        echo '        ' . '<div class="text">' . "\n";
        echo '        ' . '    <h3>' . "\n";
        echo '        ' . '        You have to log in to see your galleries!' . "\n";
        echo '        ' . '    </h3>' . "\n";
        echo '        ' . '</div>' . "\n";
        echo '        ' . "\n";
    } else {
        if (isset($_GET['createGallery']) && isset($_GET['gallery_name']) && isset($_GET['gallery_description'])) {
            // handle gallery creation requests:
            $gallery_name = $_GET['gallery_name'];
            $gallery_description = $_GET['gallery_description'];
            $gallery_name = substr($gallery_name, 0, $_SESSION['settings']['database']['max_gallery_name_length']);
            $gallery_description = substr($gallery_description, 0, $_SESSION['settings']['database']['max_descriptions_length']);
            $gallery_name = strip_tags($gallery_name);
            $gallery_description = strip_tags($gallery_description);

            if (strlen($gallery_name) < 0) {
                // name too short!
                echo '        ' . "\n";
                echo '        ' . '<div class="text">' . "\n";
                echo '        ' . '    <h3>' . "\n";
                echo '        ' . '        Gallery name too short!' . "\n";
                echo '        ' . '    </h3>' . "\n";
                echo '        ' . '</div>' . "\n";
                echo '        ' . "\n";
            } else {
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
                                'INSERT INTO `galleries` (`username`, `gallery_name`, `gallery_description`) ' .
                                'VALUES (?, ?, ?);'
                        );
                        $param1 = $username;
                        $param2 = $gallery_name;
                        $param3 = $gallery_description;
                        $stmt->bind_param("sss", $param1, $param2, $param3);
                        if ($stmt->execute()) {
                            // gallery created successfully:
                            echo '        ' . "\n";
                            echo '        ' . '<div class="text">' . "\n";
                            echo '        ' . '    <h3>' . "\n";
                            echo '        ' . '        Gallery created!' . "\n";
                            echo '        ' . '    </h3>' . "\n";
                            echo '        ' . '</div>' . "\n";
                            echo '        ' . "\n";
                        } else {
                            echo '        ' . "\n";
                            echo '        ' . '<div class="text">' . "\n";
                            echo '        ' . '    <h3>' . "\n";
                            echo '        ' . '        Failed to create gallery!' . "\n";
                            echo '        ' . '    </h3>' . "\n";
                            echo '        ' . '</div>' . "\n";
                            echo '        ' . "\n";
                        }
                        $stmt->close();
                    }
                }
                $conn->close();
            }
        } else if (isset($_GET['deleteGallery'])) {
            // handle gallery deletion requests:
            $gallery_id = $_GET['deleteGallery'];
            $gallery_id = strip_tags($gallery_id);
            if (!is_numeric($gallery_id) || strlen($gallery_id) < 1) {
                // wrong input
            } else {
                $username = $_SESSION['username'];

                $server_name = $_SESSION['settings']['database']['server_name'];
                $database_name = $_SESSION['settings']['database']['database_name'];
                $root_username = $_SESSION['settings']['database']['root_username'];
                $root_password = $_SESSION['settings']['database']['root_password'];

                $conn = new mysqli($server_name, $root_username, $root_password, $database_name);

                if ($conn->connect_error) {
                    echo ("Connection failed: " . $conn->connect_error);
                } else {
                    // check if gallery belongs to the user:
                    {
                        $stmt = $conn->prepare(
                                "SELECT `username` " .
                                "FROM `galleries` " .
                                "WHERE `galleries`.`gallery_id` = ? AND `username` = ?;"
                        );
                        $param1 = $gallery_id;
                        $param2 = $username;
                        $stmt->bind_param("ss", $param1, $param2);
                        if ($stmt->execute()) {
                            // check results:
                            $stmt->bind_result($name);
                            if (!$stmt->fetch()) {
                                $stmt->close();
                                // Error fetching:
                                echo '        ' . "\n";
                                echo '        ' . '<div class="text">' . "\n";
                                echo '        ' . '    <h3>' . "\n";
                                echo '        ' . '        Failed to delete gallery!' . "\n";
                                echo '        ' . '    </h3>' . "\n";
                                echo '        ' . '</div>' . "\n";
                                echo '        ' . "\n";
                            } else {
                                $stmt->close();
                                if ($username != $name) {
                                    // the gallery doesn't belong to the user that requested to delete it:
                                    echo '        ' . "\n";
                                    echo '        ' . '<div class="text">' . "\n";
                                    echo '        ' . '    <h3>' . "\n";
                                    echo '        ' . '        The gallery you want to delete is not yours!' . "\n";
                                    echo '        ' . '    </h3>' . "\n";
                                    echo '        ' . '</div>' . "\n";
                                    echo '        ' . "\n";
                                } else {
                                    // delete the gallery folder with all files in it if it exists:
                                    {
                                        $path = 'galleries/' . $gallery_id;
                                        $files = glob($path . '/*'); // get all file names
                                        foreach ($files as $file) { // iterate files
                                            if (is_file($file))
                                                unlink($file); // delete file
                                        }
                                        if (is_dir($path)) {
                                            rmdir($path);
                                        }
                                    }

                                    //remove entries in the database:
                                    {
                                        // remove comments related to affected images:
                                        {
                                            $stmt2 = $conn->prepare(
                                                    'DELETE FROM `comments` ' .
                                                    'WHERE `image_id` IN ' .
                                                    '(SELECT `image_id` ' .
                                                    'FROM `images` ' .
                                                    'WHERE `images`.`gallery_id` = ?);'
                                            );
                                            echo "ERROR: (" . $conn->errno . ") " . $conn->error . "\n";
                                            $param2 = $gallery_id;
                                            $stmt2->bind_param("s", $param2);
                                            $stmt2->execute();
                                            $stmt2->close();
                                        }

                                        // remove affected images:
                                        {
                                            $stmt3 = $conn->prepare(
                                                    'DELETE FROM `images` ' .
                                                    'WHERE `gallery_id` = ?;'
                                            );
                                            $param3 = $gallery_id;
                                            $stmt3->bind_param("s", $param3);
                                            $stmt3->execute();
                                            $stmt3->close();
                                        }
                                        // remove affected gallery from gallery list:
                                        {
                                            $stmt4 = $conn->prepare(
                                                    'DELETE FROM `galleries` ' .
                                                    'WHERE `gallery_id` = ?;'
                                            );
                                            $param4 = $gallery_id;
                                            $stmt4->bind_param("s", $param4);
                                            $stmt4->execute();
                                            $stmt4->close();
                                        }
                                    }

                                    // output result message:
                                    {
                                        echo '        ' . "\n";
                                        echo '        ' . '<div class="text">' . "\n";
                                        echo '        ' . '    <h3>' . "\n";
                                        echo '        ' . '        Gallery deleted!' . "\n";
                                        echo '        ' . '    </h3>' . "\n";
                                        echo '        ' . '</div>' . "\n";
                                        echo '        ' . "\n";
                                    }
                                }
                            }
                        }
                        // $stmt->close();
                    }
                }
                $conn->close();
            }
        }

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
                    $param = $username;
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
                        echo '        ' . '        <th class="description">' . "\n";
                        echo '        ' . '            <div class="text">gallery description:</div>' . "\n";
                        echo '        ' . '        </th>' . "\n";
                        echo '        ' . '        <th>' . "\n";
                        echo '        ' . '            <div class="text">DELETE</div>' . "\n";
                        echo '        ' . '        </th>' . "\n";
                        echo '        ' . '    </tr>' . "\n";
                        while ($stmt->fetch()) {
                            echo '        ' . '    <tr>' . "\n";
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            ' . $gallery_id . "\n";
                            echo '        ' . '        </td>' . "\n";
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            <a class="text" href="index.php?getGallery=' . $gallery_id . '">' . $gallery_name . '</a>' . "\n";
                            echo '        ' . '        </td>' . "\n";
                            echo '        ' . '        <td class="description">' . "\n";
                            echo '        ' . '            ' . $gallery_description . "\n";
                            echo '        ' . '        </td>' . "\n";
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            <a class="text" href="index.php?myGalleries&deleteGallery=' . $gallery_id . '">' . 'DELETE' . '</a>' . "\n";
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

        // form for gallery creation:
        {

            echo '        ' . '<form action = "index.php" method="get">' . "\n";
            echo '        ' . '    <div class="text">' . "\n";
            echo '        ' . '        <h3>' . "\n";
            echo '        ' . '            Create new gallery:' . "\n";
            echo '        ' . '        </h3>' . "\n";
            echo '        ' . '    </div>' . "\n";
            echo '        ' . '    <table class="searchresults">' . "\n";
            echo '        ' . '        <tr>' . "\n";
            echo '        ' . '            <th>' . "\n";
            echo '        ' . '                <div class="text">gallery name:</div>' . "\n";
            echo '        ' . '            </th>' . "\n";
            echo '        ' . '            <th>' . "\n";
            echo '        ' . '                <div class="text">gallery description:</div>' . "\n";
            echo '        ' . '            </th>' . "\n";
            echo '        ' . '            <th>' . "\n";
            echo '        ' . '                <div class="text">create...</div>' . "\n";
            echo '        ' . '            </th>' . "\n";
            echo '        ' . '        </tr>' . "\n";
            echo '        ' . '        <tr>' . "\n";
            echo '        ' . '            <td>' . "\n";
            echo '        ' . '                <input type = "text" name = "gallery_name">' . "\n";
            echo '        ' . '            </td>' . "\n";
            echo '        ' . '            <td>' . "\n";
            echo '        ' . '                <input type = "text" name = "gallery_description">' . "\n";
            echo '        ' . '            </td>' . "\n";
            echo '        ' . '            <td>' . "\n";
            echo '        ' . '                <input type = "submit" value="Greate gallery">' . "\n";
            echo '        ' . '            </td>' . "\n";
            echo '        ' . '        </tr>' . "\n";
            echo '        ' . '    </table>' . "\n";
            echo '        ' . '    <input type = "hidden" name = "myGalleries" value="">' . "\n";
            echo '        ' . '    <input type = "hidden" name = "createGallery" value="">' . "\n";
            echo '        ' . '</form>' . "\n";
            echo '        ' . "\n";
        }
    }
}

myGalleries();
?>