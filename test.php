<?php
$db = new SQLite3('users.db');

$query = $db->query('SELECT COUNT(*) as total FROM active_sessions');
$result = $query->fetchArray(SQLITE3_ASSOC);
if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $db->lastErrorMsg());
}

// Affichage du nombre d'utilisateurs
echo '<p>' . $result['total'] . ' utilisateurs inscrit. </p>';
$db->close();
?>