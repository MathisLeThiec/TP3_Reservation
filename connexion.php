<?php
require 'config.php';
// session_start(); // Assure-toi que la session est démarrée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT id, password, verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        if ($user['verified'] == 1) {
            $_SESSION['user_id'] = $user['id']; // Stocke l'ID de l'utilisateur en session
            header("Location: profil.php"); // Redirige vers la page profil
            exit(); // Arrête l'exécution du script après la redirection
        } else {
            echo "Veuillez vérifier votre email avant de vous connecter.";
        }
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ReservExpress</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 450px;
            margin: 8% auto;
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
                        <a class="nav-link btn btn-outline-primary me-2  active" href="connexion.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary" href="inscription.php">Inscription</a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
    
    <!-- Formulaire de connexion -->
    <div class="container">
        <div class="login-container">
            <h2 class="form-title">Connexion</h2>
            
            <div id="alertMessage" class="alert alert-danger d-none" role="alert">
                <!-- Messages d'erreur apparaîtront ici -->
            </div>
            
            <form action="connexion.php" method="POST" id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
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