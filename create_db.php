<?php
// create_db.php

// Connexion à la base de données SQLite
$db = new SQLite3('chat.db');

// Vérification de la connexion à la base de données
if (!$db) {
    die("La connexion à la base de données a échoué.");
}

// Requête SQL pour créer la table "messages" si elle n'existe pas déjà
$query = "CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY,
    message_text TEXT,
    username TEXT NOT NULL,
    timestamp INTEGER
);";

// Exécution de la requête SQL
if ($db->exec($query)) {
    echo "Base de données et table créées avec succès.";
} else {
    echo "La création de la base de données a échoué : " . $db->lastErrorMsg();
}

// Fermeture de la connexion à la base de données
$db->close();
?>
