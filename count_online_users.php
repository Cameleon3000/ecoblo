<?php
// count_online_users.php
include 'functions.php';
$db = new SQLite3('users.db');


$onlineUsers = countOnlineUsers($db);

// Retourne le résultat au format JSON
echo json_encode(['total' => $onlineUsers]);
?>
