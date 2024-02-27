<?php
// En haut de vos fichiers PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// contenu_chat.php

// Connexion à la base de données SQLite
$db = new SQLite3('chat.db');

// Assurez-vous que la variable de session 'conversation_id' est définie
if (!isset($_SESSION['conversation_id'])) {
    echo "<p>Erreur : 'conversation_id' non défini dans la session.</p>";
    exit;
}

$conversation_id = $_SESSION['conversation_id'];

// Récupération des messages depuis la base de données, triés par ordre chronologique
$query = $db->prepare("SELECT message_text, timestamp, username FROM messages WHERE conversation_id = :conversation_id ORDER BY timestamp ASC");
$query->bindParam(":conversation_id", $conversation_id);

$result = $query->execute();

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // Vérifier si la clé 'username' existe dans le tableau $row
    if (array_key_exists('username', $row)) {
        $content = htmlspecialchars($row['message_text'], ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        echo "<p><strong>{$username}:</strong> {$content}</p>";
    } else {
        echo "<p>Erreur : La clé 'username' n'existe pas dans le tableau.</p>";
    }
}

// Fermeture de la base de données
$db->close();
?>
