<?php
session_start();

require 'assets/script/db_connect.php';
$user = $_SESSION['username'];
$servername = "Luciousdev"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Console - <?php echo $servername ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="overflow-hidden">
    <div class="flex h-screen">
        <div class="w-1/6 bg-gray-800 text-white py-4 px-8">
            <span>Welcome <?php echo $user; ?></span>
            <ul class="mt-4">
                <li class="mb-2"><a href="#" class="text-blue-500">Home</a></li>
                <li class="mb-2"><a href="#" class="text-blue-500">Console</a></li>
                <li class="mb-2"><a href="#" class="text-blue-500">FTP connection</a></li>
                <li class="mb-2"><a href="#" class="text-blue-500">File browser</a></li>
                <li><a href="#" class="text-blue-500">Link</a></li>
            </ul>
        </div>

        <div class="flex-1 bg-gray-100">
            <div class="px-8 py-4">
                <div id="console" class="h-5/6 bg-black p-4 overflow-auto"></div>
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
            const span = document.createElement('span');

            // Remove color codes from the content
            const strippedContent = content.replace(/§[0-9a-f]/g, '');

            span.innerHTML = strippedContent;

            // Extract color codes from the content
            const colorCodes = content.match(/§[0-9a-f]/g) || [];

            // Apply color codes as inline styles
            colorCodes.forEach(colorCode => {
                const color = colorCode.replace('§', '');
                span.style.color = getColorFromCode(color);
            });

            consoleElement.appendChild(span);
            consoleElement.appendChild(lineBreak);
            consoleElement.scrollTop = consoleElement.scrollHeight; // Auto scroll to bottom
        }




    </script>
</body>
</html>