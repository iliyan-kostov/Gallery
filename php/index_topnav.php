<?php

echo '            ' . "\n";

echo '            ' . '<table>' . "\n";
echo '            ' . '    <tr>' . "\n";

// navigation and search:
{
    echo '            ' . '        <td class="floatleft" id="homeButton">' . "\n";
    echo '            ' . '            <div class="text">HOME</div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchBox">' . "\n";
    echo '            ' . '            <div class="text">[search]</div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchLimit">' . "\n";
    echo '            ' . '            <div class="text">[searchlimit]</div>' . "\n";
    echo '            ' . '        </td>' . "\n";
    echo '            ' . '        <td class="floatleft" id="searchButton">' . "\n";
    echo '            ' . '            <div class="text">SEARCH</div>' . "\n";
    echo '            ' . '        </td>' . "\n";
}

// user controls:
{
    if (isset($_SESSION['username'])) {
        // authenticated user:
        {
            echo '            ' . '        <td class="floatright" id="userLoggedIn">' . "\n";
            echo '            ' . '            <div class="text">MYGALLERIES Logged in as: username LOGOUT</div>' . "\n";
            echo '            ' . '        </td>' . "\n";
        }
    } else {
        // unauthenticated user:
        {
            echo '            ' . '        <td class="floatright" id="userNotLoggedIn">' . "\n";
            echo '            ' . '            <div class="text">[username][password] LOGIN</div>' . "\n";
            echo '            ' . '        </td>' . "\n";
        }
    }
}

echo '            ' . '    </tr>' . "\n";
echo '            ' . '</table>' . "\n";

echo '            ' . "\n";
?>