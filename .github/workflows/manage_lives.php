<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Lógica para listar transmisiones existentes
$lives = glob('live/*.html');
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Transmisiones - MiniHost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Administrar Transmisiones</h1>
        <p>Aquí puede editar o eliminar sus transmisiones en vivo.</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (count($lives) > 0): ?>
            <ul class="list-group">
                <?php foreach ($lives as $live): ?>
                    <li class="list-group-item">
                        <?php
                        $title = basename($live, '.html');
                        echo "<a href='$live' target='_blank'>$title</a>";
                        ?>
                        <a href="edit.php?file=<?php echo urlencode(basename($live)); ?>" class="btn btn-sm btn-warning ms-2">Editar</a>
                        <a href="delete_live.php?file=<?php echo urlencode(basename($live)); ?>" class="btn btn-sm btn-danger ms-2">Eliminar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay transmisiones en vivo disponibles.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>