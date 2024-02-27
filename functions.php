<?php
// functions.php

function countOnlineUsers($db) {
    $timeout = 1; // 5 minutes (ajustez selon vos besoins)
    $timestamp = time() - $timeout;

    // Supprimer les sessions expirées

    // Compter les sessions actives
    $countQuery = $db->query("SELECT COUNT(*) as total FROM active_sessions");

    // Utiliser fetchArray et extraire la valeur
    $result = $countQuery->fetchArray(SQLITE3_ASSOC);

    if ($result) {
        return $result['total'];
    } else {
        return 0;
    }
}
?>
