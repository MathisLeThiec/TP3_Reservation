<?php
require 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Traitement du formulaire de réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $date = $_POST['date_reservation'];
    $heure = $_POST['heure'];
    $motif = $_POST['motif'];

    // Vérifier si la date et l'heure sont déjà réservées
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
    $stmt->execute([$date, $heure]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $error_message = "Ce créneau est déjà réservé. Veuillez choisir un autre.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, date_reservation, heure, motif) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $date, $heure, $motif]);
        header("Location: profil.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Réservation - ReservExpress</title>
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
        
        .reservation-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .reservation-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .reservation-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        
        .reservation-subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: var(--secondary-color);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .btn-success {
            background-color: #27ae60;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-success:hover {
            background-color: #2ecc71;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .card-summary {
            border-left: 4px solid var(--secondary-color);
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
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
                        <a class="nav-link active" href="reservation.php">
                            <i class="fas fa-calendar-plus me-1"></i> Réserver
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">
                            <i class="fas fa-user me-1"></i> Mon compte
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="profil.php" class="d-inline">
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="reservation-container">
                    <div class="reservation-header">
                        <h2 class="reservation-title">
                            <i class="fas fa-calendar-plus me-2 text-success"></i>Nouvelle réservation
                        </h2>
                        <p class="reservation-subtitle">Complétez le formulaire ci-dessous pour confirmer votre réservation</p>
                    </div>
                    
                    <?php if (isset($error_message)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">
                                <span class="step-number">1</span>Sélectionnez une date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="date" name="date_reservation" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <small class="text-muted">Vous ne pouvez réserver qu'à partir d'aujourd'hui</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <span class="step-number">2</span>Choisissez une heure
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <input type="time" name="heure" class="form-control" required>
                            </div>
                            <small class="text-muted">Nos horaires d'ouverture: 08:00 - 18:00</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <span class="step-number">3</span>Précisez le motif
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                                <input type="text" name="motif" class="form-control" placeholder="Décrivez brièvement l'objet de votre réservation..." required>
                            </div>
                        </div>
                                                
                        <div class="action-buttons">
                            <button type="submit" name="reserve" class="btn btn-success flex-grow-1">
                                <i class="fas fa-check-circle me-2"></i>Confirmer la réservation
                            </button>
                            <a href="profil.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Définir les heures d'ouverture
        document.addEventListener('DOMContentLoaded', function() {
            const timeInput = document.querySelector('input[type="time"]');
            timeInput.setAttribute('min', '08:00');
            timeInput.setAttribute('max', '18:00');
        });
    </script>
</body>
</html>