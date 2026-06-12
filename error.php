<?php
$code = isset($_GET['code']) ? (int)$_GET['code'] : 404;

switch ($code) {
    case 400:
        $errTitle = "400";
        $errSubtitle = "Bad Request";
        $errDesc = "The request could not be understood by the server due to malformed syntax.";
        break;
    case 403:
        $errTitle = "403";
        $errSubtitle = "Restricted Access";
        $errDesc = "You do not have permission to access this directory or page using the credentials you supplied.";
        break;
    case 500:
        $errTitle = "500";
        $errSubtitle = "Under Construction";
        $errDesc = "The server encountered an internal error or misconfiguration and was unable to complete your request. Our engineers are on it.";
        break;
    case 404:
    default:
        $errTitle = "404";
        $errSubtitle = "Page Not Found";
        $errDesc = "The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.";
        $code = 404;
        break;
}

http_response_code($code);

$pathPrefix = "./";
$pageTitle = "$errTitle $errSubtitle | Erix Construction";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="stylesheet" href="./assets/css/style.css"/>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>
  body {
    margin: 0;
    padding: 0;
    background: #0a0a0a;
    color: #f5f5f0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
</head>
<body>

<main style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: var(--bg); color: var(--text-muted); position: relative; overflow: hidden; padding: 40px 20px;">
  
  <!-- Subtle Background Grid/Glow for Aesthetic -->
  <div style="position: absolute; top: -20%; left: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(212, 160, 23, 0.05) 0%, transparent 70%); z-index: 0; pointer-events: none;"></div>
  <div style="position: absolute; bottom: -20%; right: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(212, 160, 23, 0.03) 0%, transparent 70%); z-index: 0; pointer-events: none;"></div>

  <div style="max-width: 600px; text-align: center; position: relative; z-index: 1;">
    <h1 style="font-family: 'Bebas Neue', sans-serif; font-size: clamp(6rem, 15vw, 12rem); line-height: 1; color: var(--gold); margin-bottom: 10px; text-shadow: 0 10px 30px rgba(212, 160, 23, 0.2);">
      <?php echo $errTitle; ?>
    </h1>
    <h2 style="font-family: 'Outfit', sans-serif; font-size: clamp(1.5rem, 4vw, 2.5rem); font-weight: 500; color: var(--cream); margin-bottom: 20px; letter-spacing: -0.02em;">
      <?php echo htmlspecialchars($errSubtitle); ?>
    </h2>
    <p style="font-family: 'DM Sans', sans-serif; font-size: 1.1rem; line-height: 1.6; color: rgba(245,245,240,0.7); margin-bottom: 40px; max-width: 80%; margin-left: auto; margin-right: auto;">
      <?php echo htmlspecialchars($errDesc); ?>
    </p>
    
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
      <a href="./index.php" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center;">
        Back to Home
      </a>
    </div>
  </div>
</main>

</body>
</html>
