<?php
session_start();
$_SESSION['destination_site'] = "inscription.php";
if (!$_SESSION['connexionSecurisee']) {
    header('Location: verification.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ECOBLO - Inscription</title>
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
</head>
<body>
    <?php
    // ... Votre code PHP ...

    // Affiche le lien de retour
    echo '<a href="page_de_base.php" class="back-link">← Retour</a>';
    ?>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["nom_utilisateur"];
        $password = $_POST["mot_de_passe"];

        // Valider les entrées (ajoutez des validations supplémentaires selon vos besoins)
        if (empty($username) || empty($password)) {
            echo "<p>Les champs ne peuvent pas être vides.</p>";
        } else {
            // Hacher le mot de passe
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Connexion à la base de données SQLite
            $db = new SQLite3("users.db");

            // Vérifier si le nom d'utilisateur existe déjà
            $checkUserStmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE username = :username");
            $checkUserStmt->bindParam(":username", $username);
            $userResult = $checkUserStmt->execute()->fetchArray(SQLITE3_ASSOC);

            if ($userResult['count'] > 0) {
                echo "<h2>Le nom d'utilisateur existe déjà. Choisissez un autre nom d'utilisateur.</h2>";
            } else {
                // Préparer et exécuter la requête d'insertion
                $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password_hash);
                $stmt->execute();

                echo "<p>Compte créé avec succès. <a href='connection.php'>Se connecter</a></p>";
            }

            // Fermer la connexion à la base de données
            $db->close();
        }
    }
    ?>

    <h1>Inscription</h1>
    <form action="" method="post">
        <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required><br>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>
        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>
