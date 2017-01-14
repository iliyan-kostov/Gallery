<?php require_once 'sessionSettings.php'; ?>
<?php

function getGallery() {
    if (isset($_GET['getGallery'])) {
        $getGallery = $_GET['getGallery'];
        $getGallery = strip_tags($getGallery);

        // gallery display:
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

                            // handle image uploads:
                            if (isset($_FILES['upload_image']) && isset($_POST['upload_image_description']) && isset($_POST['upload_gallery_id'])) {
                                if ($username != $_SESSION['username']) {
                                    // cannot upload to other user's galleries!
                                } else {
                                    $upload_image_description = $_POST['upload_image_description'];
                                    $upload_gallery_id = $_POST['upload_gallery_id'];
                                    if ($gallery_id != $upload_gallery_id) {
                                        // wrong gallery
                                    } else {
                                        $upload_image_description = substr($upload_image_description, 0, $_SESSION['settings']['database']['max_descriptions_length']);
                                        $upload_image_description = strip_tags($upload_image_description);

                                        $target_dir = 'galleries/' . $gallery_id . '/';
                                        $target_name = basename($_FILES["upload_image"]["name"]);
                                        $target_name = strip_tags($target_name);
                                        $image_extension = pathinfo($target_name, PATHINFO_EXTENSION);

                                        $image_extension_length = strlen($image_extension);
                                        $image_remaining_name_length = min(strlen($target_name) - 1 - $image_extension_length, $_SESSION['settings']['database']['max_image_name_length'] - 1 - $image_extension_length); // 1 for dot
                                        $target_name = substr($target_name, 0, $image_remaining_name_length) . '.' . $image_extension;

                                        $target_path = $target_dir . $target_name;

                                        // create folder "galleries" under root if it doesn't exist
                                        if (!file_exists('galleries/')) {
                                            mkdir('galleries/');
                                        }

                                        // check if the file is an image:
                                        $check = getimagesize($_FILES["upload_image"]["tmp_name"]);
                                        if ($check == false) {
                                            // do not allow upload - the file is not an image
                                            echo '            ' . "\n";
                                            echo '            ' . '<div class="text">' . "\n";
                                            echo '            ' . '    <h3>' . "\n";
                                            echo '            ' . '        Error - the file is not an image!' . "\n";
                                            echo '            ' . '    </h3>' . "\n";
                                            echo '            ' . '</div>' . "\n";
                                            echo '            ' . "\n";
                                        } else {
                                            // check if file already exists:
                                            if (file_exists($target_path)) {
                                                // do not allow upload if file already exists:
                                                echo '            ' . "\n";
                                                echo '            ' . '<div class="text">' . "\n";
                                                echo '            ' . '    <h3>' . "\n";
                                                echo '            ' . '        Error - a file already exists with the same name in the same gallery!' . "\n";
                                                echo '            ' . '    </h3>' . "\n";
                                                echo '            ' . '</div>' . "\n";
                                                echo '            ' . "\n";
                                            } else {
                                                // check file size:
                                                if ($_FILES["upload_image"]["size"] > $_SESSION['settings']['uploads']['max_file_size']) {
                                                    // do not allow upload - very large file:
                                                    echo '            ' . "\n";
                                                    echo '            ' . '<div class="text">' . "\n";
                                                    echo '            ' . '    <h3>' . "\n";
                                                    echo '            ' . '        Cannot upload - file too large!' . "\n";
                                                    echo '            ' . '    </h3>' . "\n";
                                                    echo '            ' . '    <h3>' . "\n";
                                                    echo '            ' . '        Maximum file size: ' . $_SESSION['settings']['uploads']['max_file_size'] . ' bytes' . "\n";
                                                    echo '            ' . '    </h3>' . "\n";
                                                    echo '            ' . '</div>' . "\n";
                                                    echo '            ' . "\n";
                                                } else {
                                                    if (!is_dir($target_dir) && !mkdir($target_dir)) {
                                                        // directory creation failed
                                                    } else {
                                                        // register in database:
                                                        $stmt = $conn->prepare(
                                                                'INSERT INTO `images` (`gallery_id`, `image_name`, `image_description`) ' .
                                                                'VALUES (? , ? ,?);'
                                                        );
                                                        $param1 = $gallery_id;
                                                        $param2 = $target_name;
                                                        $param3 = $upload_image_description;
                                                        $stmt->bind_param("sss", $param1, $param2, $param3);
                                                        if ($stmt->execute()) {
                                                            // do upload
                                                            move_uploaded_file($_FILES["upload_image"]["tmp_name"], $target_path);
                                                        }
                                                        $stmt->close();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

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
                                echo '            ' . "\n";
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
                                            echo '            ' . '            <div class="text"><a href="index.php?getImage=' . $image_id . '" class="text">' . $image_id . '</a></div>' . "\n";
                                            echo '            ' . '        </td>' . "\n";
                                            echo '            ' . '        <td>' . "\n";
                                            echo '            ' . '            <div class="text"><a href="index.php?getImage=' . $image_id . '" class="text">' . $image_name . '</a></div>' . "\n";
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
                                echo '            ' . "\n";
                            }

                            // upload form:
                            if (isset($_SESSION['username']) && $_SESSION['username'] == $username) {
                                echo '            ' . "\n";
                                echo '            ' . '<form action = "index.php?getGallery=' . $gallery_id . '" method="post" enctype="multipart/form-data">' . "\n";
                                echo '            ' . '    <div class="text">' . "\n";
                                echo '            ' . '        <h3>' . "\n";
                                echo '            ' . '            Upload image:' . "\n";
                                echo '            ' . '        </h3>' . "\n";
                                echo '            ' . '    </div>' . "\n";
                                echo '            ' . '    <table class="searchresults">' . "\n";
                                echo '            ' . '        <tr>' . "\n";
                                echo '            ' . '            <th>' . "\n";
                                echo '            ' . '                <div class="text">image:</div>' . "\n";
                                echo '            ' . '            </th>' . "\n";
                                echo '            ' . '            <th class="description">' . "\n";
                                echo '            ' . '                <div class="text">description:</div>' . "\n";
                                echo '            ' . '            </th>' . "\n";
                                echo '            ' . '            <th>' . "\n";
                                echo '            ' . '                <div class="text">upload:</div>' . "\n";
                                echo '            ' . '            </th>' . "\n";
                                echo '            ' . '        </tr>' . "\n";
                                echo '            ' . '        <tr>' . "\n";
                                echo '            ' . '            <td>' . "\n";
                                echo '            ' . '                <input type = "file" name = "upload_image" accept = "image/*">' . "\n";
                                echo '            ' . '            </td>' . "\n";
                                echo '            ' . '            <td class="description">' . "\n";
                                echo '            ' . '                <input type = "text" name = "upload_image_description" value="">' . "\n";
                                echo '            ' . '            </td>' . "\n";
                                echo '            ' . '            <td>' . "\n";
                                echo '            ' . '                <input type = "submit" value="Upload">' . "\n";
                                echo '            ' . '                <input type = "hidden" name = "upload_gallery_id" value="' . $gallery_id . '">' . "\n";
                                echo '            ' . '            </td>' . "\n";
                                echo '            ' . '        </tr>' . "\n";
                                echo '            ' . '    </table>' . "\n";
                                echo '            ' . '</form>' . "\n";
                                echo '            ' . "\n";
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