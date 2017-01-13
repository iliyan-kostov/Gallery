<?php require_once 'sessionSettings.php'; ?>
<?php

echo '            ' . "\n";

echo '            ' . '<table>' . "\n";
echo '            ' . '    <tr>' . "\n";

// navigation and search:
{
    echo '            ' . '        <td class="floatleft" id="homeButton">' . "\n";
    echo '            ' . '            <div class="text">' . "\n";
    echo '            ' . '                <a href="index.php">' . "\n";
    echo '            ' . '                    HOME' . "\n";
    echo '            ' . '                </a>' . "\n";
    echo '            ' . '            </div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchBox">' . "\n";
    echo '            ' . '            <div class="text">' . "\n";
    echo '            ' . '                <form action ="index.php" method="get">' . "\n";
    echo '            ' . '                    <input type="text" name="search">' . "\n";
    echo '            ' . '                    <select name="searchlimit">' . "\n";
    echo '            ' . '                         <option value="10">10</option>' . "\n";
    echo '            ' . '                         <option value="20">20</option>' . "\n";
    echo '            ' . '                         <option value="50">50</option>' . "\n";
    echo '            ' . '                         <option value="100">100</option>' . "\n";
    echo '            ' . '                    </select>' . "\n";
    echo '            ' . '                    <input type="submit" value="Search">' . "\n";
    echo '            ' . '                </form>' . "\n";
    echo '            ' . '            </div>' . "\n";
    echo '            ' . '        </td>' . "\n";
}

// user controls:
{
    if (isset($_SESSION['username'])) {
        // authenticated user:
        {
            echo '            ' . '        <td class="floatright" id="userControls">' . "\n";
            echo '            ' . '            <div class="text">' . "\n";
            echo '            ' . '                <form action="logout.php" method="get">' . "\n";
            echo '            ' . '                    Logged in as: ' . $_SESSION['username'] . ' <input type="submit" value="Logout">' . "\n";
            echo '            ' . '                </form>' . "\n";
            echo '            ' . '            </div>' . "\n";
            echo '            ' . '        </td>' . "\n";
            echo '            ' . '        <td class="floatright" id="myGalleries">' . "\n";
            echo '            ' . '            <div class="text">' . "\n";
            echo '            ' . '                MYGALLERIES' . "\n";
            echo '            ' . '            </div>' . "\n";
            echo '            ' . '        </td>' . "\n";
        }
    } else {
        // unauthenticated user:
        {
            echo '            ' . '        <td class="floatright" id="userControls">' . "\n";
            echo '            ' . '            <div class="text">' . "\n";
            echo '            ' . '                <form action="login.php" method="post">' . "\n";
            echo '            ' . '                    <input type="text" name="username">' . "\n";
            echo '            ' . '                    <input type="password" name="password">' . "\n";
            echo '            ' . '                    <input type="submit" value="Login">' . "\n";
            echo '            ' . '                </form>' . "\n";
            echo '            ' . '            </div>' . "\n";
            echo '            ' . '        </td>' . "\n";
        }
    }
}

echo '            ' . '    </tr>' . "\n";
echo '            ' . '</table>' . "\n";

echo '            ' . "\n";
?>