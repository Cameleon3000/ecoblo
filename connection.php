<?php
session_start();
$_SESSION['destination_site'] = "connection.php";
if (!$_SESSION['connexionSecurisee']) {
    header('Location: verification.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bonjour</title>
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

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #3498db;
            font-size: 36px;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: red;
            font-size: 20px;
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

        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 18px;
            text-decoration: none;
            color: #3498db;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        // Fonction pour masquer le message d'erreur après un certain délai
        function masquerMessageErreur() {
            var messageErreur = document.getElementById('message_erreur');
            if (messageErreur) {
                setTimeout(function() {
                    messageErreur.style.display = 'none';
                }, 500); // Masquer après 1 seconde (1000 millisecondes)
            }
        }
    </script>
</head>
<body>
<?php
    // ... Votre code PHP ...

    // Affiche le lien de retour
    echo '<a href="page_de_base.php" class="back-link">← Retour</a>';
?>
    <?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Connexion à la base de données SQLite
        $db = new SQLite3("users.db");

        // Valider les entrées utilisateur
        $username = filter_input(INPUT_POST, "nom_utilisateur", FILTER_SANITIZE_STRING);
        $password = $_POST["mot_de_passe"];
        echo "<h2 id='message_erreur'><h2>";
        sleep(1);

        if ($username && $password) {
            // Requête préparée pour récupérer le mot de passe haché
            $stmt = $db->prepare("SELECT password FROM users WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $result = $stmt->execute()->fetchArray();

            if ($result && password_verify($password, $result["password"])) {
                // Authentification réussie, définissez la variable de session
                $_SESSION["authenticated"] = true;
                $_SESSION['username'] = $username;

                // Ajouter une entrée à la table active_sessions
                $sessionId = session_id();
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $timestamp = time();
                $_SESSION["session_id"] = $sessionId;

                $insertQuery = $db->prepare("INSERT INTO active_sessions (session_id, ip_address, timestamp) VALUES (:session_id, :ip_address, :timestamp)");
                $insertQuery->bindParam(":session_id", $sessionId);
                $insertQuery->bindParam(":ip_address", $ipAddress);
                $insertQuery->bindParam(":timestamp", $timestamp);
                $insertQuery->execute();

                $db->close();

                $db = new SQLite3("users.db");

                $query = $db->prepare("SELECT id FROM users WHERE username = :username");
                $query->bindParam(":username", $_SESSION['username']);
                $result_2 = $query->execute()->fetchArray();

                $conversation_name = "Nom de la conversation";

                if (isset($result)) {
                    $expediteur = $result_2["id"];
                }

                $_SESSION['user_id'] = $expediteur;
                

                // Rediriger l'utilisateur
                $_SESSION['destination_site'] = "conversations.php";
                header("Location: verification.php");
                exit();
            } else {
                echo "<h2 id='message_erreur'>Nom d'utilisateur ou mot de passe incorrect.</h2>";
                echo '<script>masquerMessageErreur();</script>'; // Appel de la fonction JavaScript pour masquer le message
            }
        }

        // Fermeture de la base de données
        $db->close();
    }
    ?>

    <h1>Connexion</h1>
    <form action="" method="post">
        <label for="nom_utilisateur">Nom d'utilisateur :</label>
        <input type="text" id="nom_utilisateur" name="nom_utilisateur" placeholder="Nom d'utilisateur" required>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="submit" value="Se connecter">
    </form>
</body>
</html>
