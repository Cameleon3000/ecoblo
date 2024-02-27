<?php
// Charger l'autoloader de Composer
require 'vendor/autoload.php';

// Créer une instance de PHPMailer
$mail = new PHPMailer\PHPMailer\PHPMailer();

// Paramètres du serveur SMTP (exemple pour Gmail)
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'votre_adresse@gmail.com';
$mail->Password = 'votre_mot_de_passe';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Paramètres de l'e-mail
$mail->setFrom('votre_adresse@gmail.com', 'Votre Nom');
$mail->addAddress('destinataire@example.com', 'Nom du destinataire');
$mail->Subject = 'Sujet de l\'e-mail';
$mail->Body = 'Contenu de l\'e-mail';

// Envoyer l'e-mail
if ($mail->send()) {
    echo 'L\'e-mail a été envoyé avec succès.';
} else {
    echo 'Erreur lors de l\'envoi de l\'e-mail : ' . $mail->ErrorInfo;
}
