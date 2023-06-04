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
$serverPort = $serverData[0]['mcserver_pass'];
$serverIP = $serverData[0]['adress'];




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - <?php echo $servername ?></title>
    <!-- <script src="https://cdn.tailwindcss.com/2.2.19/tailwind.min.css"></script> -->
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="overflow-hidden">
    <div class="flex min-h-screen">
        <div class="w-1/6 bg-gray-800 text-white py-4 px-8">
            <span>Welcome <?php echo $user; ?></span>
            <ul class="mt-4">
                <li class="mb-2"><a href="#" class="text-blue-500">Home</a></li>
                <li class="mb-2"><a href="cli.php" class="text-blue-500">Console</a></li>
                <li class="mb-2"><a href="#" class="text-blue-500">FTP connection</a></li>
                <li class="mb-2"><a href="#" class="text-blue-500">File browser</a></li>
                <li><a href="#" class="text-blue-500">Link</a></li>
                <li><a href="logout.php" class="text-blue-500">Logout</a></li>
            </ul>
        </div>

        <div class="flex-1 bg-gray-100 p-8">
            <div class="bg-white rounded-lg p-4 flex flex-col space-y-4 max-w-md mx-auto">
                <h2 class="text-lg font-semibold">Server status for: <?php echo $serverIP; ?></h2>
                <div class="flex items-center space-x-4">
                    <div>
                        <p class="text-gray-600">Current Players:</p>
                        <p id="currentPlayers" class="text-2xl font-semibold"></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Max Players:</p>
                        <p id="maxPlayers" class="text-2xl font-semibold"></p>
                    </div>
                </div>
                <div class="flex justify-center space-x-2">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Start</button>
                    <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Stop</button>
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Restart</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
  const serverIP = '<?php echo $serverIP; ?>';
  const serverPort = <?php echo $serverPort; ?>;

  function updatePlayerCounts() {
    const url = `https://api.mcsrvstat.us/2/${serverIP}:${serverPort}`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        if (data.online) {
          document.getElementById('currentPlayers').innerText = data.players.online;
          document.getElementById('maxPlayers').innerText = data.players.max;
        } else {
          document.getElementById('currentPlayers').innerText = 'N/A';
          document.getElementById('maxPlayers').innerText = 'N/A';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('currentPlayers').innerText = 'N/A';
        document.getElementById('maxPlayers').innerText = 'N/A';
      });
  }

  document.addEventListener('DOMContentLoaded', updatePlayerCounts);
</script>
</html>
