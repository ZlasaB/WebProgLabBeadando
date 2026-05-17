<?php
$mappa = "pictures/";

if (!file_exists($mappa)) {
    mkdir($mappa, 0777, true);
}

$siker_uzenet = "";
$hiba_uzenet = "";

if (isset($_POST['feltoltes']) && isset($_SESSION['user_id'])) {
    if (isset($_FILES['uj_kep']) && $_FILES['uj_kep']['error'] === 0) {
        
        $fajl_nev = basename($_FILES['uj_kep']['name']);
        $cel_fajl = $mappa . time() . "_" . $fajl_nev; 
        $fajl_tipus = strtolower(pathinfo($cel_fajl, PATHINFO_EXTENSION));
        
        $ellenorzes = getimagesize($_FILES['uj_kep']['tmp_name']);
        if ($ellenorzes !== false) {
            if (in_array($fajl_tipus, ['jpg', 'jpeg', 'png'])) {
                if (move_uploaded_file($_FILES['uj_kep']['tmp_name'], $cel_fajl)) {
                    $siker_uzenet = "A kép sikeresen fel lett töltve!";
                } else {
                    $hiba_uzenet = "Sajnáljuk, hiba történt a fájl mozgatásakor.";
                }
            } else {
                $hiba_uzenet = "Csak JPG, JPEG és PNG formátumok engedélyezettek!";
            }
        } else {
            $hiba_uzenet = "A kiválasztott fájl nem valódi kép.";
        }
    } else {
        $hiba_uzenet = "Kérjük, válassz ki egy érvényes fájlt!";
    }
}

$kepek = [];
if (is_dir($mappa)) {
    $fajlok = scandir($mappa);
    foreach ($fajlok as $fajl) {
        $kiterjesztes = strtolower(pathinfo($fajl, PATHINFO_EXTENSION));
        if (in_array($kiterjesztes, ['jpg', 'jpeg', 'png'])) {
            $kepek[] = $mappa . $fajl;
        }
    }
}
?>

<div style="padding: 20px; font-family: sans-serif;">
    <h2>Laptopok Galériája</h2>

    <?php if (!empty($siker_uzenet)): ?>
        <div style="background:#efe; color:green; padding:10px; margin-bottom:15px; border-radius:4px;"><?php echo $siker_uzenet; ?></div>
    <?php endif; ?>
    <?php if (!empty($hiba_uzenet)): ?>
        <div style="background:#fee; color:red; padding:10px; margin-bottom:15px; border-radius:4px;"><?php echo $hiba_uzenet; ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div style="background:#f9f9f9; padding:15px; border:1px solid #ddd; border-radius:8px; margin-bottom:30px; max-width: 500px;">
            <h3 style="margin-top:0;">Új kép feltöltése</h3>
            <form action="index.php?oldal=galeria" method="post" enctype="multipart/form-data">
                <input type="file" name="uj_kep" required style="margin-bottom:10px; display:block;">
                <input type="submit" name="feltoltes" value="Kép feltöltése" style="padding:8px 15px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
            </form>
        </div>
    <?php else: ?>
        <p style="color:#666; font-style:italic;">* Ha szeretnél új képet feltölteni, kérjük jelentkezz be! *</p>
    <?php endif; ?>

    <h3>Feltöltött fotók</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 15px;">
        
        <?php if (!empty($kepek)): ?>
            <?php foreach ($kepek as $kep): ?>
                <div style="border: 1px solid #ddd; padding: 10px; background: #fff; border-radius: 6px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <img src="<?php echo $kep; ?>" style="max-width: 100%; height: 160px; object-fit: cover; border-radius: 4px;">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#999; grid-column: span 3;">A mappa jelenleg még üres. Tölts fel egy képet a megjelenítéshez!</p>
        <?php endif; ?>

    </div>
</div>