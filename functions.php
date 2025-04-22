<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function emailExists($email, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() ? true : false;
}

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lethiecmathis@gmail.com';
        $mail->Password = 'acmi squx jxza miyw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('no-reply@reservation.com', 'Réservation');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Vérification de votre compte';
        $mail->Body = "Cliquez sur le lien suivant pour vérifier votre compte : <a href='http://localhost/TP3/verify.php?token=$token'>Vérifier mon compte</a>";
        $mail->send();
    } catch (Exception $e) {
        echo "Erreur de l'envoi de l'email: {$mail->ErrorInfo}";
    }
}
?>