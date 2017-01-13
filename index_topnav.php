<?php

echo '            ' . "\n";

echo '            ' . '<table>' . "\n";
echo '            ' . '    <tr>' . "\n";

// navigation and search:
{
    echo '            ' . '        <td class="floatleft" id="homeButton">' . "\n";
    echo '            ' . '            <div class="text">' . "\n";
    echo '            ' . '                HOME' . "\n";
    echo '            ' . '            </div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchBox">' . "\n";
    echo '            ' . '            <div class="text">' . "\n";
    echo '            ' . '                [search]' . "\n";
    echo '            ' . '            </div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchLimit">' . "\n";
    echo '            ' . '            <div class="text">' . "\n";
    echo '            ' . '                [searchlimit]' . "\n";
    echo '            ' . '            </div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchButton">' . "\n";
    echo '            ' . '            <div class="text">' . "\n";
    echo '            ' . '                SEARCH' . "\n";
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
            echo '            ' . '                MYGALLERIES Logged in as: username LOGOUT' . "\n";
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