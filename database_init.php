<?php require_once 'sessionSettings.php'; ?>
<?php

function database_init() {
    // connect to database:
    $server_name = $_SESSION['settings']['database']['server_name'];
    $database_name = $_SESSION['settings']['database']['database_name'];
    $root_username = $_SESSION['settings']['database']['root_username'];
    $root_password = $_SESSION['settings']['database']['root_password'];

    // connect to the database:
    $conn = new mysqli($server_name, $root_username, $root_password);
    if ($conn->connect_error) {
        // connection not successful
        echo "Connection to database failed!" . "<br>\n";
    } else {
        // connection successful
        echo "Connection to database successfull!" . "<br>\n";

        // database initialization:
        {
            $statement = (
                    "CREATE DATABASE IF NOT EXISTS `" . $database_name . "` " .
                    "CHARACTER SET utf8mb4 " .
                    "COLLATE utf8mb4_unicode_ci;"
                    );
            if ($conn->query($statement) === TRUE) {
                echo "Database `" . $database_name . "` created successfully" . "<br>\n";
            } else {
                echo "Error creating database `" . $database_name . "`: " . $conn->error . "<br>\n";
            }
        }

        // create "users" table:
        {
            $statement = (
                    "CREATE TABLE IF NOT EXISTS `" . $database_name . "`.`users` " .
                    "(" .
                    "`username` VARCHAR(" . $_SESSION['settings']['database']['max_username_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "`password` VARCHAR(" . $_SESSION['settings']['database']['max_password_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "PRIMARY KEY (`username`) USING BTREE " .
                    ") ENGINE = InnoDB;"
                    );
            if ($conn->query($statement) === TRUE) {
                echo "Table `users` created successfully" . "<br>\n";
            } else {
                echo "Error creating table `users`: " . $conn->error . "<br>\n";
            }
        }

        // create "galleries" table:
        {
            $statement = (
                    "CREATE TABLE IF NOT EXISTS `" . $database_name . "`.`galleries` " .
                    "( " .
                    "`username` VARCHAR(" . $_SESSION['settings']['database']['max_username_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "`gallery_id` INT NOT NULL AUTO_INCREMENT, " .
                    "`gallery_name` VARCHAR(" . $_SESSION['settings']['database']['max_gallery_name_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "`gallery_description` VARCHAR(" . $_SESSION['settings']['database']['max_descriptions_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, " .
                    "PRIMARY KEY (`gallery_id`) USING HASH, " .
                    "FOREIGN KEY (`username`) REFERENCES `" . $database_name . "`.`users`(`username`) ON DELETE RESTRICT ON UPDATE RESTRICT, " .
                    "INDEX (`username`) USING BTREE " .
                    ") ENGINE = InnoDB AUTO_INCREMENT = 1;"
                    );
            if ($conn->query($statement) === TRUE) {
                echo "Table `galleries` created successfully" . "<br>\n";
            } else {
                echo "Error creating table `galleries`: " . $conn->error . "<br>\n";
            }
        }

        // create "images" table:
        {
            $statement = (
                    "CREATE TABLE IF NOT EXISTS `" . $database_name . "`.`images` " .
                    "( " .
                    "`gallery_id` INT NOT NULL, " .
                    "`image_id` INT NOT NULL AUTO_INCREMENT, " .
                    "`image_name` VARCHAR(" . $_SESSION['settings']['database']['max_image_name_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "`image_description` VARCHAR(" . $_SESSION['settings']['database']['max_descriptions_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, " .
                    "PRIMARY KEY (`image_id`) USING HASH, " .
                    "FOREIGN KEY (`gallery_id`) REFERENCES `" . $database_name . "`.`galleries`(`gallery_id`) ON DELETE RESTRICT ON UPDATE RESTRICT, " .
                    "UNIQUE KEY `image_file_path` (`gallery_id`, `image_name`), " .
                    "INDEX (`gallery_id`) USING HASH " .
                    ") ENGINE = InnoDB AUTO_INCREMENT = 1;"
                    );
            if ($conn->query($statement) === TRUE) {
                echo "Table `images` created successfully" . "<br>\n";
            } else {
                echo "Error creating table `images`: " . $conn->error . "<br>\n";
            }
        }

        // create "comments" table:
        {
            $statement = (
                    "CREATE TABLE IF NOT EXISTS `" . $database_name . "`.`comments` " .
                    "( " .
                    "`image_id` INT NOT NULL, " .
                    "`username` VARCHAR(" . $_SESSION['settings']['database']['max_username_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "`comment_id` INT NOT NULL AUTO_INCREMENT, " .
                    "`comment_text` VARCHAR(" . $_SESSION['settings']['database']['max_comments_length'] . ") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, " .
                    "PRIMARY KEY (`comment_id`) USING HASH, " .
                    "FOREIGN KEY (`image_id`) REFERENCES `" . $database_name . "`.`images`(`image_id`) ON DELETE RESTRICT ON UPDATE RESTRICT, " .
                    "FOREIGN KEY (`username`) REFERENCES `" . $database_name . "`.`users`(`username`) ON DELETE RESTRICT ON UPDATE RESTRICT, " .
                    "INDEX (`image_id`) USING HASH, " .
                    "INDEX (`username`) USING BTREE " .
                    ") ENGINE = InnoDB AUTO_INCREMENT = 1;"
                    );
            if ($conn->query($statement) === TRUE) {
                echo "Table `comments` created successfully" . "<br>\n";
            } else {
                echo "Error creating table `comments`: " . $conn->error . "<br>\n";
            }
        }

        // close the connection to database:
        $conn->close();
        echo "Connection to database closed!" . "<br>\n";
    }
}

database_init();
?>