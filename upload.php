<?php
// Handle form submission
if ($_POST && isset($_POST['password']) && isset($_FILES["pdf"])) {
    require_once 'config.php';
    
    if ($_POST['password'] === $config['passwords']['admin']) {
        if ($_FILES["pdf"]["error"] == 0) {
            $targetFile = __DIR__ . "/" . $config['pdf_file'];
            if ($_FILES["pdf"]["type"] == "application/pdf") {
                if (move_uploaded_file($_FILES["pdf"]["tmp_name"], $targetFile)) {
                    $success = "Upload erfolgreich! Datei wurde als " . $config['pdf_file'] . " gespeichert.";
                } else {
                    $error = "Fehler beim Speichern der Datei.";
                }
            } else {
                $error = "Bitte nur PDF-Dateien hochladen.";
            }
        } else {
            $error = "Fehler beim Upload.";
        }
    } else {
        $error = "Falsches Passwort";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Upload</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>PDF Upload</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="password">Admin-Passwort:</label>
                <input type="password" id="password" name="password" required autocomplete="off" data-form-type="other">
            </div>
            
            <div class="form-group">
                <label for="pdf">PDF-Datei auswählen:</label>
                <input type="file" id="pdf" name="pdf" accept="application/pdf" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-upload">PDF hochladen</button>
            </div>
        </form>
    </div>
</body>
</html>