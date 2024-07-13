<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = $_POST['fileName'];
    $filePath = "live/" . $fileName;

    if (file_exists($filePath)) {
        $streamUrl = $_POST['streamUrl'];
        $thumbnailUrl = $_POST['thumbnailUrl'] ?? '';
        $title = $_POST['title'];
        $description = $_POST['description'];
        $downloadUrl = $_POST['downloadUrl'] ?? '';
        $libraries = $_POST['libraries'];
        $password = $_POST['password'] ?? '';

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

        echo "Transmisión editada con éxito. <a href='$filePath' target='_blank'>Ver transmisión</a>";
    } else {
        echo "Archivo no encontrado.";
    }
} else {
    if (isset($_GET['file'])) {
        $fileName = htmlspecialchars($_GET['file']);
        $filePath = "live/" . $fileName;

        if (file_exists($filePath)) {
            // Extraer los datos actuales del archivo HTML
            $fileContent = file_get_contents($filePath);
            preg_match('/<source src=\'([^\']+)\' type=\'video\/mp4\'>/', $fileContent, $streamUrlMatches);
            preg_match('/<img src=\'([^\']+)\' alt=\'Miniatura\' class=\'img-fluid mb-3\'>/', $fileContent, $thumbnailUrlMatches);
            preg_match('/<h1>([^<]+)<\/h1>/', $fileContent, $titleMatches);
            preg_match('/<p>([^<]+)<\/p>/', $fileContent, $descriptionMatches);
            preg_match('/<a href=\'([^\']+)\' class=\'btn btn-success\'>Descargar Video<\/a>/', $fileContent, $downloadUrlMatches);
            preg_match_all('/<script src=\'([^\']+)\'><\/script>/', $fileContent, $libraryMatches);
            preg_match('/const correctPassword = \'([^\']+)\';/', $fileContent, $passwordMatches);

            $streamUrl = $streamUrlMatches[1] ?? '';
            $thumbnailUrl = $thumbnailUrlMatches[1] ?? '';
            $title = $titleMatches[1] ?? '';
            $description = $descriptionMatches[1] ?? '';
            $downloadUrl = $downloadUrlMatches[1] ?? '';
            $libraries = $libraryMatches[1] ?? [];
            $password = $passwordMatches[1] ?? '';

            // Mostrar el formulario de edición con los datos actuales
            echo "
            <!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <title>Editar Transmisión en Vivo</title>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css'>
                <link rel='stylesheet' href='css/styles.css'>
            </head>
            <body>
                <div class='container mt-5'>
                    <h2>Editar Transmisión en Vivo</h2>
                    <form action='edit.php' method='post'>
                        <input type='hidden' name='fileName' value='$fileName'>
                        <div class='mb-3'>
                            <label for='streamUrl' class='form-label'>URL del Stream</label>
                            <input type='url' class='form-control' id='streamUrl' name='streamUrl' value='$streamUrl' required>
                        </div>
                        <div class='mb-3'>
                            <label for='thumbnailUrl' class='form-label'>URL de la Miniatura (opcional)</label>
                            <input type='url' class='form-control' id='thumbnailUrl' name='thumbnailUrl' value='$thumbnailUrl'>
                        </div>
                        <div class='mb-3'>
                            <label for='title' class='form-label'>Título</label>
                            <input type='text' class='form-control' id='title' name='title' value='$title' required>
                        </div>
                        <div class='mb-3'>
                            <label for='description' class='form-label'>Descripción</label>
                            <textarea class='form-control' id='description' name='description' rows='3' required>$description</textarea>
                        </div>
                        <div class='mb-3'>
                            <label for='downloadUrl' class='form-label'>URL de Descarga (opcional)</label>
                            <input type='url' class='form-control' id='downloadUrl' name='downloadUrl' value='$downloadUrl'>
                        </div>
                        <div class='mb-3'>
                            <label for='libraries' class='form-label'>Bibliotecas de Video</label>
                            <select multiple class='form-control' id='libraries' name='libraries[]'>
                                <option value='https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video.min.js'" . (in_array('https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video.min.js', $libraries) ? ' selected' : ''). ">ajax</option>

<option value='https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video.min.js'" . (in_array('https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video.min.js', $libraries) ? ' selected' : '') . ">Video.js</option>
                                <!-- Añade más opciones aquí si es necesario -->
                            </select>
                        </div>
                        <div class='mb-3'>
                            <label for='password' class='form-label'>Contraseña (opcional)</label>
                            <input type='password' class='form-control' id='password' name='password' value='$password'>
                        </div>
                        <button type='submit' class='btn btn-primary'>Guardar Cambios</button>
                    </form>
                </div>
                <script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js'></script>
            </body>
            </html>";
        } else {
            echo "Archivo no encontrado.";
        }
    } else {
        echo "Archivo no especificado.";
    }
}
?>