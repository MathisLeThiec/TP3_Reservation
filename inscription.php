<?php
require 'config.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = hashPassword($password);
    $token = bin2hex(random_bytes(50));
    
    if (!emailExists($email, $pdo)) {
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, date_naissance, adresse, telephone, email, password, verified, token) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)");
        $stmt->execute([$nom, $prenom, $date_naissance, $adresse, $telephone, $email, $hashedPassword, $token]);
        sendVerificationEmail($email, $token);
        echo "Inscription réussie. Veuillez vérifier votre email.";
    } else {
        echo "Cet email est déjà utilisé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ReservExpress</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .register-container {
            max-width: 650px;
            margin: 4% auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background-color: white;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
        }
        
        .btn-return {
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="fas fa-calendar-check me-2 text-primary"></i>ReservExpress
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href="connexion.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary active" href="inscription.php">Inscription</a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
    <!-- Formulaire d'inscription -->
    <div class="container">
        <div class="register-container">
            <h2 class="form-title">Créer un compte</h2>
            
            <div id="alertMessage" class="alert alert-danger d-none" role="alert">
                <!-- Messages d'erreur apparaîtront ici -->
            </div>
            
            <form action="inscription.php" method="POST" id="registerForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="date_naissance" class="form-label">Date de naissance</label>
                    <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                </div>
                
                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" required>
                </div>
                
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="row">
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
            
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous</a></p>
            </div>
            
            <div class="text-center btn-return">
                <a href="index.html" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>