<?php
// create_db.php

// Connexion à la base de données SQLite
$db = new SQLite3('users.db');

// Vérification de la connexion à la base de données
if (!$db) {
    die("La connexion à la base de données a échoué.");
}

// Requête SQL pour créer la table "users" si elle n'existe pas déjà
$queryUsers = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY,
    username TEXT,
    password TEXT,
    timestamp INTEGER
);";

// Requête SQL pour créer la table "active_sessions" si elle n'existe pas déjà
$querySessions = "CREATE TABLE IF NOT EXISTS active_sessions (
    session_id TEXT PRIMARY KEY,
    ip_address TEXT,
    timestamp INTEGER
);";

// Exécution de la requête SQL pour la table "users"
if ($db->exec($queryUsers)) {
    echo "Table 'users' créée avec succès.";
} else {
    echo "La création de la table 'users' a échoué : " . $db->lastErrorMsg();
}

// Exécution de la requête SQL pour la table "active_sessions"
if ($db->exec($querySessions)) {
    echo "Table 'active_sessions' créée avec succès.";
} else {
    echo "La création de la table 'active_sessions' a échoué : " . $db->lastErrorMsg();
}

// Fermeture de la connexion à la base de données
$db->close();
?>
