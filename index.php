<?php
// Handle form submission
if ($_POST && isset($_POST['password'])) {
    require_once 'config.php';
    
    if ($_POST['password'] === $config['passwords']['member']) {
        $action = isset($_POST['view']) ? 'view' : (isset($_POST['download']) ? 'download' : 'view');
        
        if (isset($_POST['stay_logged_in'])) {
            // Persistent login - use session and cookie
            session_start();
            $_SESSION['authenticated'] = true;
            setcookie($config['cookie_name'], 'member', time() + $config['cookie_lifetime'], '/');
            header('Location: ?show=1&action=' . $action);
            exit;
        } else {
            // Direct PDF delivery - no session, no cookies
            if (file_exists($config['pdf_file'])) {
                header('Content-Type: application/pdf');
                if ($action === 'download') {
                    header('Content-Disposition: attachment; filename="' . basename($config['pdf_file']) . '"');
                } else {
                    header('Content-Disposition: inline; filename="' . basename($config['pdf_file']) . '"');
                }
                header('Content-Length: ' . filesize($config['pdf_file']));
                readfile($config['pdf_file']);
                exit;
            }
        }
    } else {
        session_start();
        $error = 'Falsches Passwort';
    }
} else {
    session_start();
}

require_once 'config.php';

// Check if user is already authenticated via cookie (only for persistent logins)
$is_authenticated = false;

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    $is_authenticated = true;
} elseif (isset($_COOKIE[$config['cookie_name']]) && $_COOKIE[$config['cookie_name']] === 'member') {
    $is_authenticated = true;
    $_SESSION['authenticated'] = true;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie($config['cookie_name'], '', time() - 3600, '/');
    header('Location: ./');
    exit;
}

// Show PDF directly if authenticated
if ($is_authenticated && (isset($_GET['show']) || isset($_GET['download']))) {
    if (file_exists($config['pdf_file'])) {
        header('Content-Type: application/pdf');
        $action = isset($_GET['action']) ? $_GET['action'] : 'view';
        if ($action === 'download') {
            header('Content-Disposition: attachment; filename="' . basename($config['pdf_file']) . '"');
        } else {
            header('Content-Disposition: inline; filename="' . basename($config['pdf_file']) . '"');
        }
        header('Content-Length: ' . filesize($config['pdf_file']));
        readfile($config['pdf_file']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Zugang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Aktuelles Nachrichtenblatt</h1>
        
        <?php if (!$is_authenticated): ?>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" id="password" name="password" required autocomplete="off" data-form-type="other">
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="stay_logged_in" name="stay_logged_in">
                    <label for="stay_logged_in">Eingeloggt bleiben - nur am eigenen Rechner anklicken</label>
                    <div class="cookie-info">
                        Bei dieser Option wird ein Cookie gespeichert.
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="view" class="btn-view">Anschauen</button>
                    <button type="submit" name="download" class="btn-download">Herunterladen</button>
                </div>
            </form>
        <?php else: ?>
            <div class="success">
                <p>Erfolgreich angemeldet!</p>
                
                <div class="button-group">
                    <button type="button" onclick="window.location.href='?show=1&action=view'" class="btn-view">Anschauen</button>
                    <button type="button" onclick="window.location.href='?show=1&action=download'" class="btn-download">Herunterladen</button>
                </div>
                
                <div class="logout">
                    <button type="button" onclick="window.location.href='?logout=1'" class="btn-logout">Abmelden</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>