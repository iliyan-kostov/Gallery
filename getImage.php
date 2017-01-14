<?php require_once 'sessionSettings.php'; ?>
<?php

function getImage() {
    if (isset($_GET['getImage'])) {
        $getImage = $_GET['getImage'];
        $getImage = strip_tags($getImage);

        $server_name = $_SESSION['settings']['database']['server_name'];
        $database_name = $_SESSION['settings']['database']['database_name'];
        $root_username = $_SESSION['settings']['database']['root_username'];
        $root_password = $_SESSION['settings']['database']['root_password'];

        $conn = new mysqli($server_name, $root_username, $root_password, $database_name);

        if ($conn->connect_error) {
            echo ("Connection failed: " . $conn->connect_error);
        } else {

            $stmt1 = $conn->prepare(
                    'SELECT `username`, `image_name`, `images`.`gallery_id`, `image_description`, `gallery_name` ' .
                    'FROM `images` ' .
                    'LEFT JOIN `galleries` ' .
                    'ON `galleries`.`gallery_id` = `images`.`gallery_id` ' .
                    'WHERE `images`.`image_id` = ?;'
            );
            $param1 = $getImage;
            $stmt1->bind_param("s", $param1);
            $stmt1->bind_result($username, $image_name, $gallery_id, $image_description, $gallery_name);
            if (!$stmt1->execute()) {
                // error
            } else {
                if (!$stmt1->fetch()) {
                    // image doesn't exist
                    $stmt1->close();
                    echo '            ' . "\n";
                    echo '            ' . '<div class="text">' . "\n";
                    echo '            ' . '    <h2>' . "\n";
                    echo '            ' . '        Image id: ' . $getImage . ' not found! ' . "\n";
                    echo '            ' . '    </h2>' . "\n";
                    echo '            ' . '</div>' . "\n";
                    echo '            ' . "\n";
                } else {
                    // image exists
                    $stmt1->close();

                    // handle comment upload:
                    {
                        if (isset($_POST['upload_comment_text']) && isset($_POST['upload_comment_image_id'])) {
                            $upload_comment_text = $_POST['upload_comment_text'];
                            $upload_comment_image_id = $_POST['upload_comment_image_id'];
                            $upload_comment_text = substr($upload_comment_text, 0, $_SESSION['settings']['database']['max_comments_length']);
                            $upload_comment_text = strip_tags($upload_comment_text);
                            $upload_comment_image_id = strip_tags($upload_comment_image_id);

                            // only allow commenting for registered users:
                            if (isset($_SESSION['username']) && $upload_comment_image_id == $getImage) {
                                // insert comment into database:
                                $stmt3 = $conn->prepare(
                                        'INSERT INTO `comments`(`image_id`, `username`, `comment_text`) ' .
                                        'VALUES (? , ? , ?);'
                                );
                                $param3_1 = $upload_comment_image_id;
                                $param3_2 = $_SESSION['username'];
                                $param3_3 = $upload_comment_text;
                                $stmt3->bind_param("sss", $param3_1, $param3_2, $param3_3);
                                if (!$stmt3->execute()) {
                                    // error
                                }
                            }
                        }
                    }



                    $image_path = 'galleries/' . $gallery_id . '/' . $image_name;
                    echo '            ' . "\n";
                    echo '            ' . '<div class="text">' . "\n";
                    echo '            ' . '    <h2>' . "\n";
                    echo '            ' . '        Image id: ' . $getImage . "\n";
                    echo '            ' . '    </h2>' . "\n";
                    echo '            ' . '</div>' . "\n";
                    echo '            ' . "\n";
                    //display data about the image:
                    {
                        echo '            ' . "\n";
                        echo '            ' . '<form action = "index.php?getImage=' . $getImage . '" method="post">' . "\n";
                        echo '            ' . '<table class="searchresults">' . "\n";
                        echo '            ' . '    <tr>' . "\n";
                        echo '            ' . '        <th>' . "\n";
                        echo '            ' . '            <div class="text">user:</div>' . "\n";
                        echo '            ' . '        </th>' . "\n";
                        echo '            ' . '        <th>' . "\n";
                        echo '            ' . '            <div class="text"><a href="index.php?getUser=' . $username . '" class="text">' . $username . '</a></div>' . "\n";
                        echo '            ' . '        </th>' . "\n";
                        echo '            ' . '    </tr>' . "\n";
                        echo '            ' . '    <tr>' . "\n";
                        echo '            ' . '        <th>' . "\n";
                        echo '            ' . '            <div class="text">gallery name:</div>' . "\n";
                        echo '            ' . '        </th>' . "\n";
                        echo '            ' . '        <th>' . "\n";
                        echo '            ' . '            <div class="text"><a href="index.php?getGallery=' . $gallery_id . '" class="text">' . $gallery_name . '</a></div>' . "\n";
                        echo '            ' . '        </th>' . "\n";
                        echo '            ' . '    </tr>' . "\n";
                        echo '            ' . '    <tr>' . "\n";
                        echo '            ' . '        <td>' . "\n";
                        echo '            ' . '            <div class="text">image description:</div>' . "\n";
                        echo '            ' . '        </td>' . "\n";
                        echo '            ' . '        <td class="description">' . "\n";
                        echo '            ' . '            <div class="text">' . $image_description . '</div>' . "\n";
                        echo '            ' . '        </td>' . "\n";
                        echo '            ' . '    </tr>' . "\n";
                        echo '            ' . '    <tr>' . "\n";
                        echo '            ' . '        <th>' . "\n";
                        echo '            ' . '            <div class="text">image name:</div>' . "\n";
                        echo '            ' . '        </th>' . "\n";
                        echo '            ' . '        <th>' . "\n";
                        echo '            ' . '            <div class="text">' . $image_name . '</div>' . "\n";
                        echo '            ' . '        </th>' . "\n";
                        echo '            ' . '    <tr>' . "\n";
                        echo '            ' . '        <td>' . "\n";
                        echo '            ' . '            <div class="text">image:</div>' . "\n";
                        echo '            ' . '        </td>' . "\n";
                        echo '            ' . '        <td class>' . "\n";
                        echo '            ' . '            <img src="' . $image_path . '" class="full_view">' . "\n";
                        echo '            ' . '        </td>' . "\n";
                        echo '            ' . '    </tr>' . "\n";
                        // comments:
                        {
                            echo '            ' . '    <tr>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">user:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '        <th>' . "\n";
                            echo '            ' . '            <div class="text">comment:</div>' . "\n";
                            echo '            ' . '        </th>' . "\n";
                            echo '            ' . '    </tr>' . "\n";
                            // rows:
                            {
                                $stmt = $conn->prepare(
                                        'SELECT `username`, `comment_id`, `comment_text` ' .
                                        'FROM `comments` ' .
                                        'WHERE `image_id` = ? ' .
                                        'ORDER BY `comment_id` ASC;'
                                );
                                $param = $getImage;
                                $stmt->bind_param("s", $param);
                                if (!$stmt->execute()) {
                                    // error in execution
                                } else {
                                    $stmt->bind_result($username_commenter, $comment_id, $comment_text);
                                    while ($stmt->fetch()) {
                                        echo '            ' . '    <tr>' . "\n";
                                        echo '            ' . '        <td>' . "\n";
                                        echo '            ' . '            <div class="text"><a href="index.php?getUser=' . $username_commenter . '" class="text">' . $username_commenter . '</a></div>' . "\n";
                                        echo '            ' . '        </td>' . "\n";
                                        echo '            ' . '        <td class="description">' . "\n";
                                        echo '            ' . '            <div class="text">' . $comment_text . '</div>' . "\n";
                                        echo '            ' . '        </td>' . "\n";
                                        echo '            ' . '    </tr>' . "\n";
                                    }
                                }
                                $stmt->close();
                            }
                        }
                        // write comments:
                        {
                            if (!isset($_SESSION['username'])) {
                                // for not logged-in users:
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text"></div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">Log in to write a comment</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                            } else {
                                // for logged in users:
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text"></div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '        <th>' . "\n";
                                echo '            ' . '            <div class="text">Write a comment:</div>' . "\n";
                                echo '            ' . '        </th>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                                echo '            ' . '    <tr>' . "\n";
                                echo '            ' . '        <td>' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '        <td class="description">' . "\n";
                                echo '            ' . '            <textarea name = "upload_comment_text" value="" id="comment_textarea"></textarea>' . "\n";
                                echo '            ' . '            <input type="submit" value="Comment" id="comment_submit">' . "\n";
                                echo '            ' . '            <input type="hidden" name="upload_comment_image_id" value="' . $getImage . '">' . "\n";
                                echo '            ' . '        </td>' . "\n";
                                echo '            ' . '    </tr>' . "\n";
                            }
                        }
                        echo '            ' . '</table>' . "\n";
                        echo '            ' . '</form>' . "\n";
                        echo '            ' . "\n";
                    }
                }
            }
        }
    }
}

getImage();
?>