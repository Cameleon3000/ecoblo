<!DOCTYPE html>
<html lang="fr">
<?php
session_start();
if (!$_SESSION['connexionSecurisee']) {
    header('Location: verification.php');
    exit;
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deuxième Site</title>
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
    <h1>Deuxième Site</h1>


    <button onclick="redirectToFirstSite()">Accéder au Premier Site</button>

    <script>
        function redirectToFirstSite() {
            // Redirige vers le premier site (le script PHP)
            window.location.href = "verification.php";
        }
    </script>
</body>
</html>
