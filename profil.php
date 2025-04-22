<?php
require 'config.php';
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les réservations de l'utilisateur
$stmt = $pdo->prepare("SELECT id, date_reservation, heure, motif FROM reservations WHERE user_id = ?");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mise à jour des informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $email, $user_id]);
    header("Location: profil.php");
    exit();
}

// Mise à jour du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$new_password, $user_id]);
    header("Location: profil.php");
    exit();
}

// Déconnexion
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: connexion.php");
    exit();
}

// Suppression du compte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    session_destroy();
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - ReservExpress</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            padding: 1rem 0;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
        }
        
        .profile-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .profile-header {
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-warning {
            background-color: #f39c12;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-success {
            background-color: #27ae60;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            background-color: #2ecc71;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            border-left: 4px solid var(--secondary-color);
            padding-left: 1rem;
        }
        
        .reservation-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        
        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .table thead {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .action-buttons {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                        <a class="nav-link active" href="profil.php">
                            <i class="fas fa-user me-1"></i> Mon compte
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" class="d-inline">
                            <button type="submit" name="logout" class="nav-link btn">
                                <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="profile-container">
                    <div class="profile-header">
                        <h3 class="section-title"><i class="fas fa-user me-2"></i>Informations personnelles</h3>
                    </div>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-user-tag me-2 text-muted"></i>Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($user['nom']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-signature me-2 text-muted"></i>Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($user['prenom']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-envelope me-2 text-muted"></i>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <button type="submit" name="update_info" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Mettre à jour mes informations
                        </button>
                    </form>
                </div>
                
                <div class="profile-container mt-4">
                    <div class="profile-header">
                        <h3 class="section-title"><i class="fas fa-lock me-2"></i>Sécurité</h3>
                    </div>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-key me-2 text-muted"></i>Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-warning w-100">
                            <i class="fas fa-key me-2"></i>Modifier le mot de passe
                        </button>
                    </form>
                    
                    <div class="action-buttons">
                        <form method="POST">
                            <button type="submit" name="delete_account" class="btn btn-danger w-100" onclick="return confirm('Voulez-vous vraiment supprimer votre compte ? Cette action est irréversible.');">
                                <i class="fas fa-user-slash me-2"></i>Supprimer mon compte
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="profile-container">
                    <div class="profile-header d-flex justify-content-between align-items-center">
                        <h3 class="section-title mb-0"><i class="fas fa-calendar-check me-2"></i>Mes réservations</h3>
                        <a href="reservation.php" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Nouvelle réservation
                        </a>
                    </div>
                    
                    <?php if (count($reservations) > 0) : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar-day me-2"></i>Date</th>
                                        <th><i class="fas fa-clock me-2"></i>Heure</th>
                                        <th><i class="fas fa-sticky-note me-2"></i>Motif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $reservation) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reservation['date_reservation']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['heure']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['motif']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>Vous n'avez aucune réservation active.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>