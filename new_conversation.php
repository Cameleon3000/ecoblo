<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!$_SESSION['connexionSecurisee']) {
    header('Location: verification.php');
    exit;
}

try {
    if (isset($_GET['choice'])) {
        $users_a_contacter = $_GET['choice'];

        $db = new SQLite3("users.db");

        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->bindParam(":username", $users_a_contacter);
        $result = $stmt->execute()->fetchArray();

        if ($result != false) {
            $receveur_id = $result["id"];
            $expediteur = $_SESSION['user_id'];

            $_SESSION['user_id'] = $expediteur;

            $db->close();

            // Ouvrir la base de données avec le mode journal en mémoire
            $db = new SQLite3("chat.db");
            $db->exec('PRAGMA journal_mode = MEMORY;');
            $db->exec('PRAGMA read_uncommitted = true;');

            // Les données que vous souhaitez insérer
            $conversation_name = "Nom de la conversation";

            // Construire la requête SQL d'insertion
            $query = "INSERT INTO conversation (expediteur_id, receveur_id, conversation_name) VALUES (:expediteur, :receveur_id, :conversation_name)";
            $insertStmt = $db->prepare($query);
            $insertStmt->bindParam(":expediteur", $expediteur);
            $insertStmt->bindParam(":receveur_id", $receveur_id);
            $insertStmt->bindParam(":conversation_name", $conversation_name);

            // Exécuter la requête SQL
            $result = $insertStmt->execute();

            // Récupérer l'ID de la dernière ligne insérée
            $conversation_id = $db->lastInsertRowID();

            // Stocker l'ID de la conversation dans la variable de session
            $_SESSION["conversation_id"] = $conversation_id;
            echo "<p>" . $_SESSION['conversation_id'] . "</p>";

            // Vérifier si l'insertion a réussi
            if ($result) {
                echo "Enregistrement ajouté avec succès.";
                header("Location: chat.php");
            } else {
                echo "Erreur lors de l'ajout de l'enregistrement : " . $db->lastErrorMsg();
            }

            // Fermer la connexion à la base de données
            $db->close();
        }
    }
} catch (Exception $e) {
    // Attrape toutes les exceptions et affiche le message d'erreur
    echo "Une erreur s'est produite : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ECOBLO - Connexion</title>
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
<body class="container">
<h1 class="main-title">Mes conversations :</h1>
<?php
echo '<a href="conversations.php" class="back-link">← Retour</a>';
?>
<?php
try {
    // Connexion à la base de données SQLite
    $bdd = new PDO('sqlite:users.db');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer les noms d'utilisateurs
    $requete = $bdd->prepare("SELECT username FROM users");
    $requete->execute();

    // Affichage du début du tableau HTML
    echo '<table>';

    // Boucle pour afficher les résultats dans une table HTML
    while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td class="conversation-link">Nouvelle conversation avec ...<a href="new_conversation.php?choice=' . $row["username"] . '">' . $row['username'] . '</a></td></tr>';
    }

    // Fin du tableau HTML
    echo '</table>';

} catch (PDOException $e) {
    // Gestion des erreurs PDO
    echo 'Erreur : ' . $e->getMessage();
} finally {
    // Fermeture de la connexion à la base de données
    if ($bdd) {
        $bdd = null;
    }
}
?>
</body>
</html>
