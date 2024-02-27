<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deconnection</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            flex-direction: column; /* Permet d'aligner les éléments verticalement */
        }


        }

        h1 {
            text-align: center;
            color: #3498db;
            font-size: 36px;
            margin: 0;
            padding: 0;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        label, input {
            display: block;
            width: 100%;
            margin: 5px 0;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
            font-size: 20px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #2677ad;
        }

        a {
            color: #3498db;
            text-decoration: none;
            pointer-events: all;
        }

        a:hover {
            text-decoration: underline;
        }

        .new-conversation-link {
            background-color: #3498db;
            color: white;
            font-size: 20px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .conversation-table {
            width: 100%;
            border-collapse: collapse;
        }

        .conversation-link {
            display: block;
            padding: 10px;
            background-color: #ecf0f1;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
        }
        
    </style>
</head>
<body>

<p>
<?php
session_start();

// Supprimer l'utilisateur de la table active_sessions s'il y est
if (isset($_SESSION['session_id'])) {
    $session_id = $_SESSION['session_id'];
    
    // Connexion à la base de données SQLite
    $db = new SQLite3('users.db');

    // Supprimer l'entrée associée à la session de l'utilisateur
    $deleteQuery = $db->prepare("DELETE FROM active_sessions WHERE session_id = :session_id");
    $deleteQuery->bindParam(":session_id", $session_id);
    $deleteQuery->execute();

    // Fermeture de la connexion à la base de données
    $db->close();
}

// Terminer la session
session_unset();
session_destroy();

// Rediriger vers la page d'accueil (ou une autre page de votre choix)
header("Location: page_de_base.php"); // Remplacez "index.php" par la page souhaitée
exit;

$query = $db->query('SELECT COUNT(*) as total FROM users');
$result = $query->fetchArray(SQLITE3_ASSOC);
if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $db->lastErrorMsg());
}

// Affichage du nombre d'utilisateurs
echo "<br>" . $result['total'] . ' utilisateurs inscrit. '; ?>
</p>
</body>
</html>