<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['file'])) {
    $file = 'live/' . basename($_GET['file']);

    if (file_exists($file)) {
        unlink($file);
        $message = "Transmisión eliminada exitosamente.";
    } else {
        $message = "Transmisión no encontrada.";
    }
} else {
    $message = "No se especificó ninguna transmisión para eliminar.";
}

header('Location: manage_lives.php?message=' . urlencode($message));
exit();
?>