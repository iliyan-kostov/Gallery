<?php require_once 'sessionSettings.php'; ?>
<?php

function homepage() {
    echo '        ' . "\n";
    echo '        ' . '<div class="text">' . "\n";
    echo '        ' . '    <h1>' . "\n";
    echo '        ' . '        Welcome!' . "\n";
    echo '        ' . '    </h1>' . "\n";
    echo '        ' . '    <h2>' . "\n";
    echo '        ' . '        Here you can create galleries of images' . "\n";
    echo '        ' . '    </h2>' . "\n";
    echo '        ' . '    <h2>' . "\n";
    echo '        ' . '        or browse content by other users!' . "\n";
    echo '        ' . '    </h2>' . "\n";
    echo '        ' . '    <br>' . "\n";
    echo '        ' . '    <br>' . "\n";
    echo '        ' . '    <h3>' . "\n";
    echo '        ' . '        Most recently uploaded images:' . "\n";
    echo '        ' . '    </h3>' . "\n";
    echo '        ' . '</div>' . "\n";
    echo '        ' . "\n";

    $recent = $_SESSION['settings']['homepage']['recent_images'];
    if ($recent < 0) {
        $recent = 0;
    }
    if ($recent > 0) {
        $server_name = $_SESSION['settings']['database']['server_name'];
        $database_name = $_SESSION['settings']['database']['database_name'];
        $root_username = $_SESSION['settings']['database']['root_username'];
        $root_password = $_SESSION['settings']['database']['root_password'];

        $conn = new mysqli($server_name, $root_username, $root_password, $database_name);

        if ($conn->connect_error) {
            echo ("Connection failed: " . $conn->connect_error);
        } else {
            // search for images:
            {
                $stmt = $conn->prepare(
                        'SELECT `image_id`, `image_name`, `galleries`.`gallery_id` ' .
                        'FROM `images` ' .
                        'LEFT JOIN `galleries` ' .
                        'ON `images`.`gallery_id` = `galleries`.`gallery_id` ' .
                        'ORDER BY `image_id` DESC ' .
                        'LIMIT ?;'
                );
                $param = $recent;
                $stmt->bind_param("s", $param);
                if (!$stmt->execute()) {
                    // error
                } else {
                    $stmt->bind_result($image_id, $image_name, $gallery_id);
                    // check if ANY images have been uploaded:
                    // (do {} while style)
                    if (($stmt->fetch())) {
                        echo '        ' . "\n";
                        echo '        ' . '<table class="searchresults">' . "\n";
                        echo '        ' . '    <tr>' . "\n";
                        // add first image:
                        {
                            $path = 'galleries/' . $gallery_id . '/' . $image_name;
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            <a href="index.php?getImage=' . $image_id . '"><img src="' . $path . '" class="preview"></a>' . "\n";
                            echo '        ' . '        </td>' . "\n";
                        }
                        // cells for additional images (other than first):
                        while ($stmt->fetch()) {
                            $path = 'galleries/' . $gallery_id . '/' . $image_name;
                            echo '        ' . '        <td>' . "\n";
                            echo '        ' . '            <a href="index.php?getImage=' . $image_id . '"><img src="' . $path . '" class="preview"></a>' . "\n";
                            echo '        ' . '        </td>' . "\n";
                        }
                        echo '        ' . '    </tr>' . "\n";
                        echo '        ' . '</table>' . "\n";
                        echo '        ' . "\n";
                    }
                }
                $stmt->close();
            }
        }
    }
}

homepage();
?>