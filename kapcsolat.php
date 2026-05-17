<?php
// kapcsolat.php
$feedback = "";

if (isset($_POST['kapcsolat_submit'])) {
    $nev = trim($_POST['nev']);
    $uzenet = trim($_POST['uzenet']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // SZERVEROLDALI VALIDÁCIÓ (PHP)
    if (strlen($nev) < 3 || strlen($uzenet) < 5) {
        $feedback = "<p style='color:red; font-weight:bold;'>Szerver hiba: A név min. 3, az üzenet min. 5 karakter legyen!</p>";
    } else {
        $stmt = $dbh->prepare("INSERT INTO uzenetek (felhasznaloid, nev, uzenet) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $nev, $uzenet])) {
            $feedback = "<p style='color:green; font-weight:bold;'>Sikeresen elküldve! Az üzenet rögzítésre került az adatbázisban.</p>";
        } else {
            $feedback = "<p style='color:red;'>Hiba történt a mentés során.</p>";
        }
    }
}
?>

<main class="shop-container" style="margin-top:20px;">
    <h2>Kapcsolatfelvétel</h2>
    <p>Írjon nekünk üzenetet! (HTML5 validáció kikapcsolva, JS + PHP ellenőrzés aktív)</p>
    
    <?= $feedback ?>
    
    <form id="contactForm" action="index.php?oldal=kapcsolat" method="POST" style="display:flex; flex-direction:column; gap:15px; max-width:500px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <div>
            <label style="display:block; margin-bottom:5px;">Az Ön neve:</label>
            <input type="text" id="nev" name="nev" value="<?= isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>" style="width:100%; padding:8px;">
        </div>
        
        <div>
            <label style="display:block; margin-bottom:5px;">Üzenet szövege:</label>
            <textarea id="uzenet" name="uzenet" rows="6" style="width:100%; padding:8px;"></textarea>
        </div>
        
        <button type="submit" name="kapcsolat_submit" style="padding:10px; background:#2c3e50; color:white; border:none; cursor:pointer;">Üzenet elküldése</button>
    </form>
</main>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    var nev = document.getElementById('nev').value.trim();
    var uzenet = document.getElementById('uzenet').value.trim();
    var hibak = "";

    if (nev.length < 3) {
        hibak += "- A névnek legalább 3 karakternek kell lennie!\n";
    }
    if (uzenet.length < 5) {
        hibak += "- Az üzenetnek legalább 5 karakternek kell lennie!\n";
    }

    if (hibak !== "") {
        e.preventDefault(); // Megállítja a form beküldését
        alert("Kérjük, javítsa a hibákat:\n" + hibak);
    }
});
</script>