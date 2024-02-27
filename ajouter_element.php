<?php
// ajouter_element.php
// ajouter_element.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nouvel_element'])) {
    $nouvelElement = htmlspecialchars($_POST["nouvel_element"], ENT_QUOTES, 'UTF-8');

    if (!empty($nouvelElement)) {
        // Assurez-vous que vous avez le nom d'utilisateur depuis la session
        $username = $_SESSION['username']; // Suppose que 'username' est le nom de la clé dans la session
        $conversation_id = $_SESSION['conversation_id'];

        // Connexion à la base de données SQLite
        $db = new SQLite3('chat.db');

        // Ajout du nouveau message à la base de données avec le nom d'utilisateur
        $stmt = $db->prepare("INSERT INTO messages (message_text, timestamp, username, conversation_id) VALUES (:content, :timestamp, :username, :conversation_id)");
        $stmt->bindParam(':content', $nouvelElement, SQLITE3_TEXT);
        $stmt->bindParam(':timestamp', time(), SQLITE3_INTEGER);
        $stmt->bindParam(':username', $username, SQLITE3_TEXT);
        $stmt->bindParam('conversation_id', $conversation_id, SQLITE3_INTEGER);
        $stmt->execute();

        // Fermeture de la base de données
        $db->close();
    }
}


?>
