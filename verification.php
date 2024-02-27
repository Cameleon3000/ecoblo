<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de la Connexion</title>
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
            flex-direction: column;


        }

        .main-title {
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

        .secure-message {
            display: none; /* Caché initialement */
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            padding: 10px;
            color: #2ecc71; /* Couleur du texte */
            border-radius: 5px; /* Coins arrondis */
        }
        .secure-message-none {
            display: none; /* Caché initialement */
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            padding: 10px;
            color: red; /* Couleur du texte */
            border-radius: 5px; /* Coins arrondis */
        }
    </style>
</head>
<body>
    <h1 class="main-title">Vérification de la Connexion</h1>

    <?php
    session_start();

    if (isset($_GET['choice'])) {
        if ($_GET['choice'] == 'connexion') {
            $_SESSION['destination_site'] = 'connection.php';
        } elseif ($_GET['choice'] == 'inscription') {
            $_SESSION['destination_site'] = 'inscription.php';
        } elseif ($_GET['choice'] == 'new_conversation') {
            $_SESSION['destination_site'] = 'new_conversation.php';
        }
    }

    // Fonction pour vérifier si l'activité réseau est suspecte
    function isSuspiciousNetworkActivity() {
        $headers = apache_request_headers();

        if (!isset($headers['User-Agent']) || !isset($headers['Referer']) || $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
            return true;
        }

        $ptrRecord = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $countryCode = substr($ptrRecord, strrpos($ptrRecord, '.') + 1);
        $blockedCountries = array("CN", "RU", "IN", "JP");

        return in_array($countryCode, $blockedCountries);
    }

    // Fonction pour vérifier si la connexion est sécurisée (HTTPS)
    function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    // Fonction pour vérifier si l'adresse IP est autorisée
    function isAllowedIP($ip) {
        // Ajoutez ici vos règles pour les adresses IP autorisées
        $allowedIPs = array($_SESSION['ip']);
        return in_array($ip, $allowedIPs);
    }

    // Obtient l'adresse IP du client
    $clientIP = $_SERVER['REMOTE_ADDR'];

    // Vérifie si la connexion est sécurisée
    $secureMessage = isSecure() ? '<p class="secure">La connexion est sécurisée (HTTPS).</p>' : '<p class="not-secure">La connexion n\'est pas sécurisée (HTTP).</p>';

    // Vérifie si l'adresse IP est autorisée
    $ipMessage = isAllowedIP($clientIP) ? '<p class="allowed">L\'adresse IP du client est autorisée.</p>' : '<p class="not-allowed">L\'adresse IP du client n\'est pas autorisée.</p>';

    // Vérifie si l'activité réseau est suspecte
    $networkActivityMessage = isSuspiciousNetworkActivity() ? '<p class="not-secure">La connexion réseau n\'est pas sécurisée.</p>' : '<p class="secure">Le réseau est sécurisé.</p>';

    // Affiche les résultats
    //

    //echo '<div class="result">' . $secureMessage . $ipMessage . $networkActivityMessage . '</div>';

    // Vérifie si toutes les conditions sont vraies
    if ($ipMessage === '<p class="allowed">L\'adresse IP du client est autorisée.</p>' && $networkActivityMessage === '<p class="secure">Le réseau est sécurisé.</p>') {
        $page_a_ouvrir = $_SESSION['destination_site'];
        $_SESSION['connexionSecurisee'] = true;
        echo '<p id="secureMessage" class="secure-message">Sécurisation en cours...</p>';
        echo '<script>';
        echo 'document.getElementById("secureMessage").style.display = "block";';
        echo 'setTimeout(function() {';
        echo 'window.location.href = "' . $page_a_ouvrir . '";';
        echo '}, 500);';
        echo '</script>';
    } else {
        echo '<p id="secureMessage" class="secure-message-none">La connexion n\'est pas entièrement sécurisée. Redirection en cours...</p>';
        echo '<script>';
        echo 'document.getElementById("secureMessage").style.display = "block";';
        echo 'setTimeout(function() {';
        echo 'window.location.href = "page_de_base.php";';
        echo '}, 500);';
        echo '</script>';
    }
?>
</body>
</html>
