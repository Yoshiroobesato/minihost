<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $streamUrl = $_POST['streamUrl'];
    $thumbnailUrl = $_POST['thumbnailUrl'] ?? '';
    $title = $_POST['title'];
    $description = $_POST['description'];
    $downloadUrl = $_POST['downloadUrl'] ?? '';
    $libraries = $_POST['libraries'];
    $password = $_POST['password'] ?? '';

    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($title)) . ".html";
    $filePath = "live/" . $fileName;

    $isMp4 = strpos($streamUrl, '.mp4') !== false;

    $htmlContent = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>$title</title>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video-js.min.css'>
        <link rel='stylesheet' href='../css/styles.css'>
    </head>
    <body>";

    if (!empty($password)) {
        $htmlContent .= "
        <div class='container mt-5'>
            <h1>Acceso Restringido</h1>
            <form id='passwordForm'>
                <div class='mb-3'>
                    <label for='password' class='form-label'>Contraseña</label>
                    <input type='password' class='form-control' id='password' name='password' required>
                </div>
                <button type='submit' class='btn btn-primary'>Acceder</button>
            </form>
            <p id='error' class='text-danger' style='display:none;'>Contraseña incorrecta. Inténtalo de nuevo.</p>
        </div>
        <script>
            const correctPassword = '$password';
            document.getElementById('passwordForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const enteredPassword = document.getElementById('password').value;
                if (enteredPassword === correctPassword) {
                    document.getElementById('passwordForm').style.display = 'none';
                    document.getElementById('protectedContent').style.display = 'block';
                } else {
                    document.getElementById('error').style.display = 'block';
                }
            });
        </script>";
    }

    $htmlContent .= "
        <div id='protectedContent' style='display:" . (empty($password) ? "block" : "none") . ";'>
            <div class='container mt-5'>
                <h1>$title</h1>
                <p>$description</p>";

    if (!empty($thumbnailUrl)) {
        $htmlContent .= "<img src='$thumbnailUrl' alt='Miniatura' class='img-fluid mb-3'>";
    }

    if ($isMp4) {
        $htmlContent .= "
            <div class='video-container'>
                <video id='live-player' class='video-js vjs-default-skin' controls preload='auto' width='640' height='360'>
                    <source src='$streamUrl' type='video/mp4'>
                    Tu navegador no soporta el formato de video.
                </video>
            </div>";
    } else {
        $htmlContent .= "
            <div class='video-container'>
                <iframe src='$streamUrl' width='640' height='360' frameborder='0' allowfullscreen></iframe>
            </div>";
    }

    if (!empty($downloadUrl)) {
        $htmlContent .= "<div class='mt-3'>
                            <a href='$downloadUrl' class='btn btn-success'>Descargar Video</a>
                        </div>";
    }

    $htmlContent .= "</div>
        </div>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video.min.js'></script>";

    foreach ($libraries as $library) {
        $htmlContent .= "<script src='$library'></script>";
    }

    $htmlContent .= "
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const player = videojs('live-player', {
                    controls: true,
                    autoplay: false,
                    preload: 'auto',
                    fluid: true
                });

                // Deshabilitar el menú contextual del clic derecho para evitar la descarga
                document.getElementById('live-player').addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                });
            });
        </script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>";

    file_put_contents($filePath, $htmlContent);

    echo "Transmisión creada con éxito. <a href='$filePath' target='_blank'>Ver transmisión</a>";
}
?>