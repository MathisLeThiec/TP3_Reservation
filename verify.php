<?php
// Inclure la configuration de la base de données
// Configuration de la base de données
$host = 'localhost';
$dbname = 'reservation_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Rechercher l'utilisateur avec le token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE token = ? AND verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Si l'utilisateur existe et n'est pas vérifié, on active le compte
        $stmt = $pdo->prepare("UPDATE users SET verified = 1, token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        echo "Compte vérifié avec succès. Vous pouvez maintenant vous connecter.";
    } else {
        // Si le token est invalide ou le compte déjà vérifié
        echo "Token invalide ou compte déjà vérifié.";
    }
} else {
    echo "Aucun token trouvé.";
}
?>
