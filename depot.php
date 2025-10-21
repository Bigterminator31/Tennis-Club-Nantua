<?php
session_start();

$upload_dir = __DIR__ . "/uploads";
$users_file = __DIR__ . "/users.json";

// Créer uploads si inexistant
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// --- LOGIN ---
if (!isset($_SESSION['user'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $users = json_decode(file_get_contents($users_file), true);
        $found = false;
        foreach ($users as $u) {
            if ($u['username'] === $_POST['username'] && password_verify($_POST['password'], $u['password'])) {
                $_SESSION['user'] = $u['username'];
                $found = true;
                break;
            }
        }
        if (!$found) $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }

    if (!isset($_SESSION['user'])) {
        echo '<!DOCTYPE html><html lang="fr"><head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Connexion - Dépôt</title>
                <link rel="stylesheet" href="css/style.css">
              </head><body>
              <main class="container">
              <h2>Connexion requise</h2>';
        if (isset($error)) echo "<p style='color:red'>$error</p>";
        echo '<form method="POST">
                Nom d\'utilisateur: <input type="text" name="username" required><br><br>
                Mot de passe: <input type="password" name="password" required><br><br>
                <input type="submit" value="Connexion">
              </form>
              </main></body></html>';
        exit;
    }
}

// --- UPLOAD ---
if (isset($_POST['submit']) && isset($_FILES['file'])) {
    $filename = basename($_FILES['file']['name']);
    $target_file = $upload_dir . "/" . $filename;

    // Limiter les types de fichiers autorisés
    $allowed_types = ['pdf','doc','docx','jpg','png'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if(!in_array($ext, $allowed_types)){
        $message = "Type de fichier non autorisé !";
    } elseif(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        file_put_contents($upload_dir . "/log.txt", date('Y-m-d H:i') . " - " . $_SESSION['user'] . " - $filename\n", FILE_APPEND);
        $message = "Fichier uploadé avec succès !";
    } else {
        $message = "Erreur lors de l'upload.";
    }
}

// --- TELECHARGEMENT ---
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $filepath = $upload_dir . "/" . $file;
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        echo "Fichier introuvable.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dépôt de fichiers</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
  <section class="card">
    <h2>Dépôt de fichiers</h2>
    <?php if(isset($message)) echo "<p style='color:green'>$message</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
      <label>Choisir un fichier:</label><br>
      <input type="file" name="file" required><br><br>
      <input type="submit" name="submit" value="Téléverser">
    </form>
  </section>

  <section class="card">
    <h2>Fichiers disponibles</h2>
    <ul>
      <?php
      $files = array_diff(scandir($upload_dir), array('.', '..', 'log.txt'));
      $logs = file_exists($upload_dir . "/log.txt") ? file($upload_dir . "/log.txt") : [];
      foreach ($files as $f) {
          $owner = "";
          foreach ($logs as $line) {
              if (strpos($line, $f) !== false) {
                  $owner = explode(" - ", trim($line))[1];
              }
          }
          echo "<li><a href='?download=$f'>$f</a> (mis par: $owner)</li>";
      }
      ?>
    </ul>
  </section>

  <form method="POST" action="">
    <input type="submit" name="logout" value="Déconnexion">
  </form>
</main>
</body>
</html>

<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: depot.php");
    exit;
}
?>
