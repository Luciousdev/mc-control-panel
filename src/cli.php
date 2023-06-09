<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    if (headers_sent()) {
        echo '<script>window.location.href = "../index.php";</script>';
        exit();
    } else {
        header("Location: ../index.php");
        exit();
    }
}

require 'assets/script/sql.php';
require 'assets/script/db_connect.php';
$user = $_SESSION['username'];
$userid = $_SESSION['user_id'];
$serverData = getMcServer($userid);
$_SESSION['serverdata'] = $serverData;
$servername = $serverData[0]['servername'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Console - <?php echo $servername ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-1/6 bg-gray-800 text-white flex flex-col items-start justify-between py-8">
            <div>
                <h1 style="padding-left: 10px;" class="text-2xl font-bold mb-4"><a href="#" class="text-blue-500">Welcome <?php echo $user; ?>!</a></h1>
                <nav class="flex flex-col space-y-4">
                    <a style="padding-left: 10px;" href="homepage.php" class="nav-link">Home</a>
                    <a style="padding-left: 10px;" href="#" class="nav-link">Console</a>
                    <a style="padding-left: 10px;" href="#" class="nav-link">FTP connection</a>
                    <a style="padding-left: 10px;" href="#" class="nav-link">File browser</a>
                </nav>
            </div>
            <div>
                <a style="padding-left: 10px;" href="logout.php" class="nav-link">Logout</a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 bg-gray-700">
            <div class="px-8 py-4 flex flex-col h-screen">
                <div id="console" class="flex-1 bg-black p-4 overflow-y-auto"></div>
                <form id="command-form" class="mt-4 flex" action="assets/script/db_connect.php" method="POST">
                    <input id="command-input" type="text" name="command" class="flex-1 border border-gray-300 p-2 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </form>
            </div>
        </div>
    </div>

    <script>
        const consoleElement = document.getElementById('console');
        const commandForm = document.getElementById('command-form');
        const commandInput = document.getElementById('command-input');

        function getColorFromCode(code) {
            const colorMap = {
                0: '#000000',  // Black
                1: '#0000AA',  // Dark Blue
                2: '#00AA00',  // Dark Green
                3: '#00AAAA',  // Dark Aqua
                4: '#AA0000',  // Dark Red
                5: '#AA00AA',  // Dark Purple
                6: '#FFAA00',  // Gold
                7: '#AAAAAA',  // Gray
                8: '#555555',  // Dark Gray
                9: '#5555FF',  // Blue
                a: '#55FF55',  // Green
                b: '#55FFFF',  // Aqua
                c: '#FF5555',  // Red
                d: '#FF55FF',  // Light Purple
                e: '#FFFF55',  // Yellow
                f: '#FFFFFF'   // White
            };
            return colorMap[code] || '#FFFFFF';  // Default to White if code is not found
        }

        commandForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const command = commandInput.value;
            appendToConsole(`<span class="text-blue-500">$ ${command}</span>`);
            commandInput.value = '';

            // Send the command to the server-side script for execution
            fetch('assets/script/execute-command.php', {
                method: 'POST',
                body: new URLSearchParams({ command }),
            })
                .then(response => response.text())
                .then(output => appendToConsole(output))
                .catch(error => appendToConsole(`<span class="text-red-500">${error}</span>`));
        });

        function appendToConsole(content) {
            const lineBreak = document.createElement('br');
            const lines = content.split('\n');

            lines.forEach(line => {
                const span = document.createElement('span');
                const strippedContent = line.replace(/§[0-9a-f]/g, '');

                span.innerHTML = strippedContent;

                // Extract color codes from the line
                const colorCodes = line.match(/§[0-9a-f]/g) || [];

                // Apply color codes as inline styles
                colorCodes.forEach(colorCode => {
                    const color = colorCode.replace('§', '');
                    span.style.color = getColorFromCode(color);
                });

                consoleElement.appendChild(span);
                consoleElement.appendChild(lineBreak.cloneNode()); 
            });

            consoleElement.scrollTop = consoleElement.scrollHeight; 
        }
    </script>
</body>
</html>
