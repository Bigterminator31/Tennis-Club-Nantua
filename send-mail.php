<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $email = htmlspecialchars($_POST["email"]);
    $sujet = htmlspecialchars($_POST["sujet"]);
    $message = htmlspecialchars($_POST["message"]);

    $to = "tennisclubnantua@gmail.com"; // ton adresse mail
    $subject = "Nouveau message depuis le site du Tennis Club de Nantua";
    $body = "Nom : $nom\nEmail : $email\nSujet : $sujet\n\nMessage :\n$message";

    $headers = "From: $email\r\nReply-To: $email\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "<h2>Merci $nom ! Votre message a été envoyé avec succès.</h2>";
    } else {
        echo "<h2>Une erreur est survenue lors de l’envoi du message. Veuillez réessayer.</h2>";
    }
} else {
    echo "<h2>Requête invalide.</h2>";
}
?>
