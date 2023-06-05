<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  if (headers_sent()) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
  } else {
    header("Location: index.php");
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #111827;
            color: #F9FAFB;
        }
    </style>
</head>
<body>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-1/6 bg-gray-800 text-white flex flex-col items-start justify-between py-8">
        <div>
            <h1 style="padding-left: 10px;" class="text-2xl font-bold mb-4"><a href="#" class="text-blue-500">Welcome <?php echo $user; ?>!</a></h1>
            <nav class="flex flex-col space-y-4">
                <a style="padding-left: 10px;" href="#" class="nav-link">Home</a>
                <a style="padding-left: 10px;" href="cli.php" class="nav-link">Console</a>
                <a style="padding-left: 10px;" href="#" class="nav-link">FTP connection</a>
                <a style="padding-left: 10px;" href="#" class="nav-link">File browser</a>
            </nav>
        </div>
        <div>
            <a style="padding-left: 10px;" href="logout.php" class="nav-link">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-900 py-8 px-16">
      <div class="max-w-lg mx-auto">
        <h2 class="text-3xl font-semibold mb-8">Server status for: <?php echo $serverIP.":".$serverPort; ?></h2>
        <div class="bg-gray-800 p-6 rounded-lg flex justify-between">
          <div class="text-white">
            <p class="text-gray-400">Server IP:</p>
            <p id="" class="text-xl font-semibold"><?php echo $serverIP.":".$serverPort; ?></p>
          </div>
          <div class="text-white">
            <p class="text-gray-400">Players:</p>
            <p id="currentPlayers" class="text-2xl font-semibold"></p>
          </div>
        </div>
        <div class="flex justify-center space-x-4 mt-8">
          <button id="start" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Start</button>
          <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="stopServer()">Stop</button>
        </div>
      </div>
    </main>
  </div>
</body>
<script>
    function stopServer() {
    var xhttp = new XMLHttpRequest();

    var url = "assets/script/stop-command.php";

    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
      }
    };

    // Send the request
    xhttp.send();
  }



  const serverIP = '<?php echo $serverIP; ?>';
  const serverPort = <?php echo $serverPort; ?>;

  function updatePlayerCounts() {
    const url = `https://api.mcsrvstat.us/2/${serverIP}:${serverPort}`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        if (data.online) {
          document.getElementById('currentPlayers').innerText = data.players.online+"/"+data.players.max;
        } else {
          document.getElementById('currentPlayers').innerText = 'N/A';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('currentPlayers').innerText = 'N/A';
      });
  }

  document.addEventListener('DOMContentLoaded', updatePlayerCounts);


  document.getElementById("start").addEventListener("click", function() {
    // Send a request to the server with the variable value
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "http://192.168.2.176:3000/run-script/" + serverIP, true);
    xhr.send();
  });
</script>
</html>
