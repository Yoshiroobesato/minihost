<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - MiniHost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Panel de Control</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>. Gestione sus transmisiones en vivo aquí.</p>

        <a href="create_live.php" class="btn btn-primary">Crear Nueva Transmisión</a>
        <a href="manage_lives.php" class="btn btn-secondary">Administrar Transmisiones</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>