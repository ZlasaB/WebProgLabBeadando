<?php

$siker_uzenet = "";
$hiba_uzenet = "";

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $dbh->prepare("DELETE FROM gep WHERE id = :id");
        $stmt->execute(array(':id' => $_GET['id']));
        $siker_uzenet = "A gép sikeresen törölve lett!";
    } catch (PDOException $e) {
        $hiba_uzenet = "Hiba a törlés során: " . $e->getMessage();
    }
}

if (isset($_POST['mentes'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $gyarto = trim($_POST['gyarto']);
    $tipus = trim($_POST['tipus']);
    $kijelzo = trim($_POST['kijelzo']);
    $memoria = intval($_POST['memoria']);
    $merevlemez = intval($_POST['merevlemez']);
    $videovezerlo = trim($_POST['videovezerlo']);
    $ar = intval($_POST['ar']);
    $processzorid = intval($_POST['processzorid']);
    $oprendszerid = intval($_POST['oprendszerid']);
    $db = intval($_POST['db']);

    if (empty($gyarto) || empty($tipus)) {
        $hiba_uzenet = "A gyártó és típus mezők kitöltése kötelező!";
    } else {
        try {
            if (empty($id)) {

                $sql = "INSERT INTO gep (gyarto, tipus, kijelzo, memoria, merevlemez, videovezerlo, ar, processzorid, oprendszerid, db) 
                        VALUES (:gyarto, :tipus, :kijelzo, :memoria, :merevlemez, :videovezerlo, :ar, :processzorid, :oprendszerid, :db)";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array(
                    ':gyarto' => $gyarto, ':tipus' => $tipus, ':kijelzo' => $kijelzo,
                    ':memoria' => $memoria, ':merevlemez' => $merevlemez, ':videovezerlo' => $videovezerlo,
                    ':ar' => $ar, ':processzorid' => $processzorid, ':oprendszerid' => $oprendszerid, ':db' => $db
                ));
                $siker_uzenet = "Új gép sikeresen hozzáadva!";
            } else {

                $sql = "UPDATE gep SET gyarto = :gyarto, tipus = :tipus, kijelzo = :kijelzo, memoria = :memoria, 
                        merevlemez = :merevlemez, videovezerlo = :videovezerlo, ar = :ar, processzorid = :processzorid, 
                        oprendszerid = :oprendszerid, db = :db WHERE id = :id";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array(
                    ':id' => $id, ':gyarto' => $gyarto, ':tipus' => $tipus, ':kijelzo' => $kijelzo,
                    ':memoria' => $memoria, ':merevlemez' => $merevlemez, ':videovezerlo' => $videovezerlo,
                    ':ar' => $ar, ':processzorid' => $processzorid, ':oprendszerid' => $oprendszerid, ':db' => $db
                ));
                $siker_uzenet = "A gép adatai sikeresen frissítve lettek!";
            }
        } catch (PDOException $e) {
            $hiba_uzenet = "Adatbázis hiba: " . $e->getMessage();
        }
    }
}


$szerkesztendo_gep = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $dbh->prepare("SELECT * FROM gep WHERE id = :id");
    $stmt->execute(array(':id' => $_GET['id']));
    $szerkesztendo_gep = $stmt->fetch(PDO::FETCH_ASSOC);
}


$gepek = array();
try {
    $stmt = $dbh->query("SELECT * FROM gep ORDER BY id DESC");
    $gepek = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $hiba_uzenet = "Nem sikerült a gépek listázása: " . $e->getMessage();
}
?>

<h2>Laptopok / Gépek kezelése (CRUD)</h2>

<?php if (!empty($siker_uzenet)): ?>
    <div style="background:#efe; color:green; padding:10px; margin-bottom:15px; border-radius:4px;"><?php echo $siker_uzenet; ?></div>
<?php endif; ?>
<?php if (!empty($hiba_uzenet)): ?>
    <div style="background:#fee; color:red; padding:10px; margin-bottom:15px; border-radius:4px Meso;"><?php echo $hiba_uzenet; ?></div>
<?php endif; ?>

<div style="background:#f9f9f9; padding:15px; border:1px solid #ddd; border-radius:8px; margin-bottom:30px;">
    <h3><?php echo $szerkesztendo_gep ? "Gép adatainak módosítása (ID: ".$szerkesztendo_gep['id'].")" : "Új gép felvétele"; ?></h3>
    <form action="index.php?oldal=crud_notebooks" method="post" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
        
        <?php if ($szerkesztendo_gep): ?>
            <input type="hidden" name="id" value="<?php echo $szerkesztendo_gep['id']; ?>">
        <?php endif; ?>

        <div>
            <label style="display:block; font-weight:bold;">Gyártó:</label>
            <input type="text" name="gyarto" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['gyarto']) : ''; ?>" required style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Típus:</label>
            <input type="text" name="tipus" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['tipus']) : ''; ?>" required style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Kijelző (pl: 15.6):</label>
            <input type="text" name="kijelzo" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['kijelzo']) : ''; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Memória (MB):</label>
            <input type="number" name="memoria" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['memoria']) : '2048'; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Merevlemez (GB):</label>
            <input type="number" name="merevlemez" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['merevlemez']) : '250'; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Videóvezérlő:</label>
            <input type="text" name="videovezerlo" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['videovezerlo']) : ''; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Ár (Ft):</label>
            <input type="number" name="ar" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['ar']) : '0'; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Darabszám (db):</label>
            <input type="number" name="db" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['db']) : '1'; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Processzor ID:</label>
            <input type="number" name="processzorid" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['processzorid']) : '1'; ?>" style="width:90%; padding:5px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold;">Oprendszer ID:</label>
            <input type="number" name="oprendszerid" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['oprendszerid']) : '1'; ?>" style="width:90%; padding:5px;">
        </div>

        <div style="grid-column: span 2; margin-top:10px;">
            <input type="submit" name="mentes" value="<?php echo $szerkesztendo_gep ? 'Módosítások mentése' : 'Gép hozzáadása'; ?>" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
            <?php if ($szerkesztendo_gep): ?>
                <a href="index.php?oldal=crud_notebooks" style="padding:9px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:4px; margin-left:10px; font-size:0.9em;">Mégse</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<h3>Adatbázisban lévő gépek listája</h3>
<div style="overflow-x:auto;">
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse; background:#fff; font-size:0.9em; border:1px solid #ddd;">
        <thead>
            <tr style="background:#f2f2f2;">
                <th>ID</th>
                <th>Gyártó</th>
                <th>Típus</th>
                <th>Kijelző</th>
                <th>RAM</th>
                <th>HDD</th>
                <th>VGA</th>
                <th>Ár</th>
                <th>Készlet</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($gepek)): ?>
                <?php foreach ($gepek as $g): ?>
                    <tr>
                        <td><?php echo $g['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($g['gyarto']); ?></strong></td>
                        <td><?php echo htmlspecialchars($g['tipus']); ?></td>
                        <td><?php echo htmlspecialchars($g['kijelzo']); ?>"</td>
                        <td><?php echo $g['memoria']; ?> MB</td>
                        <td><?php echo $g['merevlemez']; ?> GB</td>
                        <td><?php echo htmlspecialchars($g['videovezerlo']); ?></td>
                        <td><?php echo number_format($g['ar'], 0, ',', ' '); ?> Ft</td>
                        <td><?php echo $g['db']; ?> db</td>
                        <td>
                            <a href="index.php?oldal=crud_notebooks&action=edit&id=<?php echo $g['id']; ?>" style="color:#007bff; text-decoration:none; font-weight:bold; margin-right:10px;">Módosítás</a>
                            <a href="index.php?oldal=crud_notebooks&action=delete&id=<?php echo $g['id']; ?>" onclick="return confirm('Biztosan törölni akarod ezt a gépet?');" style="color:red; text-decoration:none; font-weight:bold;">Törlés</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align:center; color:#999;">Nincsenek adatok a táblában.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>