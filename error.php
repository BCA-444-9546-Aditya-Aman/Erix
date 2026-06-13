<?php
$code = isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] != 200 ? (int)$_SERVER['REDIRECT_STATUS'] : (isset($_GET['code']) ? (int)$_GET['code'] : 404);

switch ($code) {
    case 400:
        $errTitle    = "400";
        $errSubtitle = "Bad Request";
        $errDesc     = "The request could not be understood by the server due to malformed syntax.";
        $errEmoji    = "Hmm, that didn't compute.";
        break;
    case 403:
        $errTitle    = "403";
        $errSubtitle = "Restricted Access";
        $errDesc     = "You do not have permission to access this page using the credentials you supplied.";
        $errEmoji    = "Hard hat zone. No entry.";
        break;
    case 500:
        $errTitle    = "500";
        $errSubtitle = "Under Construction";
        $errDesc     = "The server encountered an internal error. Our engineers are already on it — coffee in hand.";
        $errEmoji    = "Even our servers need a nap sometimes.";
        break;
    case 404:
    default:
        $errTitle    = "404";
        $errSubtitle = "Page Not Found";
        $errDesc     = "The page you're looking for might have been removed, renamed, or is temporarily unavailable.";
        $errEmoji    = "Even we couldn't dig this page up.";
        $code        = 404;
        break;
}

http_response_code($code);
$pageTitle = "$errTitle — $errSubtitle | Erix Construction";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Barlow+Condensed:wght@400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./assets/css/style.css"/>
</head>
<body class="error-page">

<div class="bg-blob bg-blob-1"></div>
<div class="bg-blob bg-blob-2"></div>

<div class="page-wrap">

  <!-- ── ANIMATED EXCAVATOR ── -->
  <div class="excavator-wrap" id="excavator" title="Click to wake the excavator!">
    <svg viewBox="0 0 480 300" xmlns="http://www.w3.org/2000/svg" width="100%">

      <!-- Ground -->
      <g id="excGround">
        <ellipse cx="240" cy="272" rx="200" ry="12" fill="rgba(212,160,23,0.08)"/>
        <!-- ground cracks from snoring -->
        <line x1="100" y1="274" x2="130" y2="271" stroke="rgba(212,160,23,0.15)" stroke-width="1.5"/>
        <line x1="130" y1="271" x2="120" y2="268" stroke="rgba(212,160,23,0.1)" stroke-width="1"/>
        <line x1="320" y1="275" x2="360" y2="272" stroke="rgba(212,160,23,0.12)" stroke-width="1.5"/>
        <line x1="360" y1="272" x2="370" y2="268" stroke="rgba(212,160,23,0.1)" stroke-width="1"/>
      </g>

      <!-- ── MAIN BODY GROUP ── -->
      <g id="excBody">

        <!-- Tracks / chassis -->
        <rect x="110" y="240" width="220" height="30" rx="14" fill="#2a2825"/>
        <rect x="120" y="243" width="200" height="24" rx="10" fill="#1e1c1a"/>
        <!-- track teeth -->
        <rect x="125" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="145" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="165" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="185" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="205" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="225" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="245" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="265" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <rect x="285" y="259" width="14" height="7" rx="2" fill="#2a2825"/>
        <!-- track wheels -->
        <circle cx="132" cy="251" r="9" fill="#3a3835"/>
        <circle cx="132" cy="251" r="4" fill="#1e1c1a"/>
        <circle cx="308" cy="251" r="9" fill="#3a3835"/>
        <circle cx="308" cy="251" r="4" fill="#1e1c1a"/>

        <!-- Main cab body -->
        <rect x="140" y="190" width="170" height="54" rx="6" fill="#2a2825"/>
        <rect x="148" y="196" width="154" height="42" rx="4" fill="#222020"/>

        <!-- Cab top rounded -->
        <rect x="155" y="155" width="140" height="40" rx="8" fill="#2a2825"/>

        <!-- Windshield / glass -->
        <rect x="162" y="160" width="80" height="30" rx="4" fill="rgba(212,160,23,0.08)" stroke="rgba(212,160,23,0.2)" stroke-width="1"/>
        <!-- window reflection line -->
        <line x1="166" y1="163" x2="180" y2="185" stroke="rgba(255,255,255,0.05)" stroke-width="2"/>

        <!-- Gold stripe accent -->
        <rect x="140" y="190" width="170" height="4" rx="0" fill="rgba(212,160,23,0.5)"/>

        <!-- Exhaust pipe -->
        <rect x="272" y="140" width="10" height="22" rx="3" fill="#3a3835"/>
        <ellipse cx="277" cy="140" rx="7" ry="3.5" fill="#4a4845"/>
        <!-- smoke puff from exhaust (snore) -->
        <circle cx="277" cy="132" r="5" fill="rgba(245,245,240,0.04)"/>
        <circle cx="280" cy="124" r="4" fill="rgba(245,245,240,0.03)"/>

        <!-- Robot face area inside windshield -->
        <!-- Left eye (closed / sleeping line) -->
        <g id="excLeftEye">
          <line x1="181" y1="172" x2="192" y2="169" stroke="var(--gold)" stroke-width="2.5" stroke-linecap="round"/>
        </g>
        <!-- Right eye (closed / sleeping line) -->
        <g id="excRightEye">
          <line x1="200" y1="169" x2="211" y2="172" stroke="var(--gold)" stroke-width="2.5" stroke-linecap="round"/>
        </g>
        <!-- Smile / snoring mouth -->
        <path d="M183 180 Q196 186 209 180" fill="none" stroke="rgba(212,160,23,0.6)" stroke-width="2" stroke-linecap="round"/>
        <!-- snore dot mouth open -->
        <ellipse cx="196" cy="183" rx="4" ry="2.5" fill="rgba(212,160,23,0.15)"/>

        <!-- Hard hat -->
        <g id="excHat">
          <!-- brim -->
          <ellipse cx="220" cy="160" rx="48" ry="7" fill="#D4A017"/>
          <!-- dome -->
          <path d="M178 160 Q178 128 220 124 Q262 128 262 160Z" fill="#D4A017"/>
          <!-- hat stripe -->
          <path d="M182 155 Q220 151 258 155" fill="none" stroke="rgba(0,0,0,0.2)" stroke-width="3"/>
          <!-- hat rim highlight -->
          <path d="M180 160 Q220 156 260 160" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="1.5"/>
        </g>

        <!-- Arm / boom system -->
        <g id="excArm">
          <!-- boom base -->
          <rect x="275" y="178" width="18" height="58" rx="4" fill="#3a3835" transform="rotate(-8, 284, 200)"/>
          <!-- stick (second arm) -->
          <rect x="320" y="178" width="14" height="52" rx="4" fill="#2a2825" transform="rotate(20, 327, 200)"/>
          <!-- hydraulic cylinders -->
          <rect x="280" y="185" width="6" height="30" rx="2" fill="#4a4845" transform="rotate(-8, 284, 200)"/>
          <rect x="322" y="182" width="5" height="22" rx="2" fill="#3a3835" transform="rotate(20, 327, 200)"/>
          <!-- bucket / scoop — drooping down lazily holding the sign -->
          <path d="M338 230 L360 228 L368 248 L330 250 Z" fill="#3a3835" stroke="rgba(212,160,23,0.3)" stroke-width="1.5"/>
          <!-- bucket teeth -->
          <line x1="340" y1="250" x2="337" y2="258" stroke="#4a4845" stroke-width="2.5" stroke-linecap="round"/>
          <line x1="349" y1="251" x2="347" y2="259" stroke="#4a4845" stroke-width="2.5" stroke-linecap="round"/>
          <line x1="358" y1="250" x2="357" y2="258" stroke="#4a4845" stroke-width="2.5" stroke-linecap="round"/>
          <!-- sign hanging from bucket -->
          <line x1="349" y1="258" x2="349" y2="268" stroke="rgba(212,160,23,0.4)" stroke-width="1.5"/>
          <rect x="316" y="265" width="68" height="22" rx="2" fill="#1a1815" stroke="rgba(212,160,23,0.35)" stroke-width="1"/>
          <text x="350" y="280" text-anchor="middle" font-family="Bebas Neue, sans-serif" font-size="11" fill="rgba(212,160,23,0.9)" letter-spacing="1">404 NOT FOUND</text>
        </g>

        <!-- Coffee thermos tipped over -->
        <g transform="translate(148, 228) rotate(75)">
          <rect x="-5" y="-14" width="11" height="22" rx="3" fill="#c04a00"/>
          <rect x="-5" y="-14" width="11" height="6" rx="2" fill="#8a3200"/>
          <rect x="-4" y="6" width="9" height="3" rx="1" fill="#8a3200"/>
        </g>
        <!-- coffee drip -->
        <g id="coffeeDrip">
          <ellipse cx="155" cy="243" rx="5" ry="2" fill="rgba(160,80,20,0.5)"/>
          <path d="M154 234 Q156 238 155 243" fill="none" stroke="rgba(160,80,20,0.6)" stroke-width="2" stroke-linecap="round"/>
        </g>

      </g>
      <!-- end excBody -->

      <!-- ── ZZZ FLOATERS ── -->
      <g id="zz1" style="opacity:0">
        <text x="258" y="148" font-family="Bebas Neue,sans-serif" font-size="18" fill="var(--gold)" opacity="0.8" letter-spacing="1">Z</text>
      </g>
      <g id="zz2" style="opacity:0">
        <text x="270" y="126" font-family="Bebas Neue,sans-serif" font-size="24" fill="var(--gold)" opacity="0.6" letter-spacing="1">Z</text>
      </g>
      <g id="zz3" style="opacity:0">
        <text x="285" y="100" font-family="Bebas Neue,sans-serif" font-size="30" fill="var(--gold)" opacity="0.4" letter-spacing="1">Z</text>
      </g>

    </svg>
  </div>
  <!-- end excavator-wrap -->

  <!-- ── ERROR CONTENT ── -->
  <div class="err-code"><?php echo htmlspecialchars($errTitle); ?></div>
  <div class="err-subtitle"><?php echo htmlspecialchars($errSubtitle); ?></div>
  <div class="err-joke">"<?php echo htmlspecialchars($errEmoji); ?>"</div>
  <p class="err-desc"><?php echo htmlspecialchars($errDesc); ?></p>

  <div class="err-actions">
    <a href="./index.php" class="btn-home">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Back to Home
    </a>
    <button class="btn-wake" onclick="wakeUp()">Wake Him Up</button>
  </div>

</div>

<script>
  let sleeping = true;
  let timeout;

  function wakeUp() {
    if (!sleeping) return;
    sleeping = false;
    const el = document.getElementById('excavator');
    el.classList.add('woken');

    // swap sign text
    const signText = el.querySelector('text');
    if (signText) signText.textContent = 'SEARCHING...';

    clearTimeout(timeout);
    timeout = setTimeout(() => {
      el.classList.remove('woken');
      if (signText) signText.textContent = '404 NOT FOUND';
      sleeping = true;
    }, 2800);
  }

  document.getElementById('excavator').addEventListener('click', wakeUp);
</script>

</body>
</html>
