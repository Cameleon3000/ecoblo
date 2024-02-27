<?php
session_start();
if (!$_SESSION['connexionSecurisee']) {
    header('Location: verification.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOBLO - Messagerie</title>
    <style>
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
    <form action="deconnection.php" method="post"> <!-- Fix : Utilisation de deconnection.php dans l'action -->
        <input type="submit" value="Déconnexion">
    </form>
    <div class="container">
        <h1>ECOBLO - Messagerie</h1>
        <br>

        <a href="verification.php?choice=new_conversation" class="new-conversation-link">Faire une nouvelle conversation</a>

        <br>
        <br>

        <table class="conversation-table">
            <?php
            // Remplacez ceci par l'ID réel de l'utilisateur
            $utilisateur_id = $_SESSION["user_id"];

            try {
                // Connexion à la base de données SQLite pour les conversations (chat.db)
                $bdd_chat = new PDO('sqlite:chat.db');
                $bdd_chat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Requête SQL pour récupérer les identifiants de conversation pour l'utilisateur
                $requete = $bdd_chat->prepare("SELECT conversation_id, receveur_id FROM conversation WHERE expediteur_id = :utilisateur_id 
                                            UNION 
                                            SELECT conversation_id, expediteur_id FROM conversation WHERE receveur_id = :utilisateur_id");
                $requete->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
                $requete->execute();

                // Préparation de la requête pour récupérer le nom du destinataire depuis la base de données des utilisateurs (users.db)
                $bdd_users = new SQLite3("users.db");
                $stmtReceiver = $bdd_users->prepare("SELECT username FROM users WHERE id = :id");

                // Récupération des résultats
                while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
                    // Récupération du nom du destinataire
                    $stmtReceiver->bindParam(":id", $row['receveur_id']);
                    $resultReceiver = $stmtReceiver->execute()->fetchArray();

                    if ($resultReceiver && isset($resultReceiver["username"])) {
                        $receiver_name = $resultReceiver["username"];
                        echo '<tr><td class="conversation-link">Conversation avec <a href="go_to_conversation.php?choice=' . $row['conversation_id'] . '">' . $receiver_name . '</a></td></tr>';
                    }
                }

            } catch (PDOException $e) {
                // Gestion des erreurs PDO
                echo 'Erreur : ' . $e->getMessage();
            } finally {
                // Fermeture de la connexion à la base de données
                if ($bdd_chat) {
                    $bdd_chat = null;
                }
                if ($bdd_users) {
                    $bdd_users->close();
                }
            }
            ?>
        </table>
    </div>
</body>

</html>
