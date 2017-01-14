<?php require_once 'sessionSettings.php'; ?>
<?php

function search() {
    if (isset($_GET['search']) && isset($_GET['searchlimit'])) {

        $search = $_GET['search'];
        $searchlimit = $_GET['searchlimit'];
        $search = strip_tags($search);
        $searchlimit = strip_tags($searchlimit);

        if (!is_numeric($searchlimit) || $searchlimit < 1) {
            echo '            ' . '<div class="text">' . "\n";
            echo '            ' . '    <h3>' . "\n";
            echo '            ' . '        Search results: none (wrong input!!!)' . "\n";
            echo '            ' . '    </h3>' . "\n";
            echo '            ' . '</div>' . "\n";
        } else {
            echo '            ' . '<div class="text">' . "\n";
            echo '            ' . '    <h3>' . "\n";
            echo '            ' . '        Search results for: "' . $search . '" (showing ' . $searchlimit . ' of each)' . "\n";
            echo '            ' . '    </h3>' . "\n";
            echo '            ' . '</div>' . "\n";

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
                    // search for users:
                    {
                        $stmt = $conn->prepare(
                                'SELECT `username` ' .
                                'FROM `users` ' .
                                'WHERE `username` LIKE ? ' .
                                'ORDER BY `username` ASC ' .
                                'LIMIT ?;'
                        );
                        $param1 = '%' . $search . '%';
                        $param2 = $searchlimit;
                        $stmt->bind_param("ss", $param1, $param2);
                        if ($stmt->execute()) {
                            $stmt->bind_result($name);
                            echo '            ' . "\n";
                            echo '            ' . '<div class="text">' . "\n";
                            echo '            ' . '    <h3>' . "\n";
                            echo '            ' . '        Users:' . "\n";
                            echo '            ' . '    </h3>' . "\n";
                            echo '            ' . '</div>' . "\n";
                            echo '            ' . '<table class="searchresults">' . "\n";
                            echo '            ' . '    <tr>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">username:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '    </tr>' . "\n";
                            while ($stmt->fetch()) {
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <a class="text" href="index.php?getUser=' . $name . '">' . $name . '</a>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                            }
                            echo '            ' . '</table>' . "\n";
                            echo '            ' . "\n";
                        }
                        $stmt->close();
                    }

                    // search for galleries:
                    {
                        $stmt = $conn->prepare(
                                'SELECT `gallery_name`, `gallery_id` ' .
                                'FROM `galleries` ' .
                                'WHERE `gallery_name` LIKE ? ' .
                                'ORDER BY `gallery_id` DESC ' .
                                'LIMIT ?;'
                        );
                        $param1 = '%' . $search . '%';
                        $param2 = $searchlimit;
                        $stmt->bind_param("ss", $param1, $param2);
                        if ($stmt->execute()) {
                            $stmt->bind_result($name, $id);
                            echo '            ' . "\n";
                            echo '            ' . '<div class="text">' . "\n";
                            echo '            ' . '    <h3>' . "\n";
                            echo '            ' . '        Galleries:' . "\n";
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
                            echo '            ' . '    </tr>' . "\n";
                            while ($stmt->fetch()) {
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <a class="text" href="index.php?getGallery=' . $id . '">' . $id . '</a>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <a class="text" href="index.php?getGallery=' . $id . '">' . $name . '</a>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                            }
                            echo '            ' . '</table>' . "\n";
                            echo '            ' . "\n";
                        }
                        $stmt->close();
                    }

                    // search for images:
                    {
                        $stmt = $conn->prepare(
                                'SELECT `image_name`, `image_id`, `gallery_id` ' .
                                'FROM `images` ' .
                                'WHERE `image_name` LIKE ? ' .
                                'ORDER BY `image_id` DESC ' .
                                'LIMIT ?;'
                        );
                        $param1 = '%' . $search . '%';
                        $param2 = $searchlimit;
                        $stmt->bind_param("ss", $param1, $param2);
                        if ($stmt->execute()) {
                            $stmt->bind_result($name, $id, $gallery_id);
                            echo '            ' . "\n";
                            echo '            ' . '<div class="text">' . "\n";
                            echo '            ' . '    <h3>' . "\n";
                            echo '            ' . '        Images:' . "\n";
                            echo '            ' . '    </h3>' . "\n";
                            echo '            ' . '</div>' . "\n";
                            echo '            ' . '<table class="searchresults">' . "\n";
                            echo '            ' . '    <tr>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">image:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">image name:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '    </tr>' . "\n";
                            while ($stmt->fetch()) {
                                $path = 'galleries/' . $gallery_id . '/' . $name;
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <a href="index.php?getImage=' . $id . '">' . '<img src="' . $path . '" class="preview">' . '</a>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '            <a class="text" href="index.php?getImage=' . $id . '">' . $name . '</a>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                            }
                            echo '            ' . '</table>' . "\n";
                            echo '            ' . "\n";
                        }
                        $stmt->close();
                    }
                }
                $conn->close();
            }
        }
    }
}

search();
?>