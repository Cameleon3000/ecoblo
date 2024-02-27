<?php
session_start();

// Vérifier si la session de connexion sécurisée est établie
if (!isset($_SESSION['connexionSecurisee']) || !$_SESSION['connexionSecurisee']) {
    $_SESSION['destination_site'] = "chat.php";
    header('Location: verification.php');
    exit;
}

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
    $_SESSION['destination_site'] = "page_de_base.php"; // Fix : Ajout de la page de redirection correcte
    header("Location: verification.php");
    exit;
}

error_log("Erreur dans contenu_chat.php : " . json_encode(error_get_last()));

// contenu_chat.php
error_log("Erreur dans contenu_chat.php : " . json_encode(error_get_last()));
// En haut de vos fichiers PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCC</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            color: #333;
        }

        #liste_messages {
            border: 1px solid #ccc;
            padding: 10px;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        p {
            margin: 5px 0;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            padding: 5px;
            width: 200px;
        }

        button {
            padding: 5px 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
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

    <script>
        async function ajouterElement() {
            var nouvelElement = document.getElementById('nouvel_element').value;

            if (nouvelElement.trim() !== '') {
                try {
                    const response = await fetch('ajouter_element.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'nouvel_element=' + encodeURIComponent(nouvelElement),
                    });

                    if (response.ok) {
                        const data = await response.text();
                        const listeMessages = document.getElementById('liste_messages');

                        // Ajouter les nouveaux messages à la fin de la liste
                        listeMessages.innerHTML += data;

                        // Faire défiler vers le bas pour afficher les nouveaux messages
                        listeMessages.scrollTop = listeMessages.scrollHeight;

                        document.getElementById('nouvel_element').value = '';
                    } else {
                        console.error('Erreur lors de la requête : ', response.statusText);
                    }
                } catch (error) {
                    console.error('Erreur lors de la requête : ', error.message);
                }
            }
        }


        // Mettre à jour la liste des messages toutes les X secondes
        setInterval(async function() {
            try {
                const response = await fetch('contenu_chat.php');

                if (response.ok) {
                    const data = await response.text();
                    document.getElementById('liste_messages').innerHTML = data;
                } else {
                    console.error('Erreur lors de la requête : ', response.statusText);
                }
            } catch (error) {
                console.error('Erreur lors de la requête : ', error.message);
            }
        }, 1000);


        setInterval(async function() {
            try {
                const response = await fetch('count_online_users.php');

                if (response.ok) {
                    const data = await response.json();
                    const onlineUsersCount = data.total;
                    // Utilisez onlineUsersCount comme une chaîne de texte simple
                    document.getElementById('users_online').textContent = 'Nombre d\'utilisateurs en ligne : ' + onlineUsersCount;
                } else {
                    console.error('Erreur lors de la requête : ', response.statusText);
                }
            } catch (error) {
                console.error('Erreur lors de la requête : ', error.message);
            }
        }, 1000);

    </script>
</head>
<body>

    <?php
    // ... Votre code PHP ...

    // Affiche le lien de retour
    echo '<a href="javascript:history.back()" class="back-link">← Retour</a>';
    ?>
    <h2>SCC</h2>
    <h3>Il y a <?php
// count_users.php

// Connexion à la base de données SQLite
$db = new SQLite3('users.db');

// Exécution de la requête pour compter le nombre d'enregistrements
$query = $db->query('SELECT COUNT(
$result = $query->fetchArray(SQLITE3_ASSOC);
if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $db->lastErrorMsg());
}

// Affichage du nombre d'utilisateurs
echo '' . $result['total'] . ' utilisateurs inscrit. ';


//echo 'Il y a actuellement ' . countOnlineUsers($db) . ' utilisateur(s) en ligne.';

// Fermeture de la connexion à la base de données
$db->close();
?>
    <div id="users_online">
        <?php include 'count_online_users.php'; ?>
    </div>
    </h3>

    <br>

    <div id="liste_messages">
        <?php include 'contenu_chat.php'; ?>
    </div>

    <label for="nouvel_element">Nouveau message :</label>
    <input type="text" id="nouvel_element" required>
    <button onclick="ajouterElement()">Envoyer</button>
</body>
</html>
