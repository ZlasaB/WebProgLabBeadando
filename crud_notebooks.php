<?php

try {
    $conn = new PDO('mysql:host=localhost;dbname=beadandoadatb;charset=utf8', 'beadandoadatb', 'WebProgBeadando2026Szem4');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
}

$siker_uzenet = "";
$hiba_uzenet = "";

$be_van_lepve = isset($_SESSION['user_id']);

$processzorok = [];
$oprendszerek = [];
if ($be_van_lepve) {
    try {
        $stmt_proci = $conn->query("SELECT id, CONCAT(gyarto, ' ', tipus) AS teljes_nev FROM processzor ORDER BY gyarto ASC, tipus ASC");
        $processzorok = $stmt_proci->fetchAll(PDO::FETCH_ASSOC);

        $stmt_oprendszer = $conn->query("SELECT id, nev AS teljes_nev FROM oprendszer ORDER BY nev ASC");
        $oprendszerek = $stmt_oprendszer->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $hiba_uzenet = "Hiba a legördülők betöltésekor: " . $e->getMessage();
    }
}

if ($be_van_lepve && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM gep WHERE id = :id");
        $stmt->execute(array(':id' => $_GET['id']));
        $siker_uzenet = "A gép sikeresen törölve lett!";
    } catch (PDOException $e) {
        $hiba_uzenet = "Hiba a törlés során: " . $e->getMessage();
    }
}

if ($be_van_lepve && isset($_POST['mentes'])) {
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
                $stmt = $conn->prepare($sql);
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
                $stmt = $conn->prepare($sql);
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
if ($be_van_lepve && isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM gep WHERE id = :id");
    $stmt->execute(array(':id' => $_GET['id']));
    $szerkesztendo_gep = $stmt->fetch(PDO::FETCH_ASSOC);
}

$gepek = array();
try {

    $sql_list = "SELECT g.*, 
                        CONCAT(p.gyarto, ' ', p.tipus) AS proci_nev, 
                        o.nev AS op_nev 
                 FROM gep g
                 LEFT JOIN processzor p ON g.processzorid = p.id
                 LEFT JOIN oprendszer o ON g.oprendszerid = o.id
                 ORDER BY g.id DESC";
    $stmt = $conn->query($sql_list);
    $gepek = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $hiba_uzenet = "Nem sikerült a gépek listázása: " . $e->getMessage();
}
?>

<div style="padding: 20px; font-family: sans-serif;">
    <h2>Notebookok Kezelése (CRUD modul)</h2>

    <?php if (!empty($siker_uzenet)): ?>
        <div style="background:#efe; color:green; padding:10px; margin-bottom:15px; border-radius:4px;"><?php echo $siker_uzenet; ?></div>
    <?php endif; ?>
    <?php if (!empty($hiba_uzenet)): ?>
        <div style="background:#fee; color:red; padding:10px; margin-bottom:15px; border-radius:4px;"><?php echo $hiba_uzenet; ?></div>
    <?php endif; ?>

    <?php if ($be_van_lepve): ?>
        <div style="background:#f4f4f4; padding:20px; border:1px solid #ddd; border-radius:8px; margin-bottom:30px;">
            <h3><?php echo $szerkesztendo_gep ? "Gép adatainak módosítása (ID: ".$szerkesztendo_gep['id'].")" : "Új notebook hozzáadása"; ?></h3>
            <form action="index.php?oldal=crud_notebooks" method="post" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                
                <?php if ($szerkesztendo_gep): ?>
                    <input type="hidden" name="id" value="<?php echo $szerkesztendo_gep['id']; ?>">
                <?php endif; ?>

                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Gyártó:</label>
                    <input type="text" name="gyarto" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['gyarto']) : ''; ?>" required style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Típus:</label>
                    <input type="text" name="tipus" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['tipus']) : ''; ?>" required style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Kijelző méret:</label>
                    <input type="text" name="kijelzo" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['kijelzo']) : ''; ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Memória (MB):</label>
                    <input type="number" name="memoria" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['memoria']) : '2048'; ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Merevlemez (GB):</label>
                    <input type="number" name="merevlemez" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['merevlemez']) : '250'; ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Videóvezérlő:</label>
                    <input type="text" name="videovezerlo" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['videovezerlo']) : ''; ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Ár (Ft):</label>
                    <input type="number" name="ar" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['ar']) : '0'; ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Készlet darabszám:</label>
                    <input type="number" name="db" value="<?php echo $szerkesztendo_gep ? htmlspecialchars($szerkesztendo_gep['db']) : '1'; ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                </div>

                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Processzor:</label>
                    <select name="processzorid" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                        <?php foreach ($processzorok as $p): ?>
                            <?php $selected = ($szerkesztendo_gep && $szerkesztendo_gep['processzorid'] == $p['id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($p['teljes_nev']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Operációs rendszer:</label>
                    <select name="oprendszerid" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                        <?php foreach ($oprendszerek as $o): ?>
                            <?php $selected = ($szerkesztendo_gep && $szerkesztendo_gep['oprendszerid'] == $o['id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $o['id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($o['teljes_nev']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="grid-column: span 2; margin-top:10px;">
                    <input type="submit" name="mentes" value="<?php echo $szerkesztendo_gep ? 'Módosítások mentése' : 'Notebook mentése'; ?>" style="padding:10px 20px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
                    <?php if ($szerkesztendo_gep): ?>
                        <a href="index.php?oldal=crud_notebooks" style="padding:9px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:4px; margin-left:10px;">Mégse</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php else: ?>
        <p style="color:#666; font-style:italic; background:#fff3cd; padding:10px; border:1px solid #ffeeba; border-radius:4px; margin-bottom:20px;">
            * Az árukészlet módosításához (Új gép felvétele, Módosítás, Törlés) kérjük, jelentkezz be! *
        </p>
    <?php endif; ?>

    <h3>Aktuális árukészlet</h3>
    <div style="overflow-x:auto;">
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse; background:#fff; border:1px solid #ddd;">
            <thead>
                <tr style="background:#e9ecef;">
                    <th>ID</th>
                    <th>Gyártó</th>
                    <th>Típus</th>
                    <th>Kijelző</th>
                    <th>RAM</th>
                    <th>HDD</th>
                    <th>Videóvezérlő</th>
                    <th>Ár</th>
                    <th>Processzor</th>
                    <th>Op. Rendszer</th>
                    <th>Készlet</th>
                    <?php if ($be_van_lepve): ?><th>Műveletek</th><?php endif; ?>
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
                            <td style="color:#2c3e50; font-weight:bold;"><?php echo htmlspecialchars($g['proci_nev'] ?? 'Ismeretlen CPU'); ?></td>
                            <td style="color:#2c3e50; font-weight:bold;"><?php echo htmlspecialchars($g['op_nev'] ?? 'Ismeretlen OS'); ?></td>
                            <td><?php echo $g['db']; ?> db</td>
                            <?php if ($be_van_lepve): ?>
                            <td>
                                <a href="index.php?oldal=crud_notebooks&action=edit&id=<?php echo $g['id']; ?>" style="color:#007bff; text-decoration:none; font-weight:bold; margin-right:10px;">Módosítás</a>
                                <a href="index.php?oldal=crud_notebooks&action=delete&id=<?php echo $g['id']; ?>" onclick="return confirm('Biztosan törölni akarod ezt a gépet?');" style="color:red; text-decoration:none; font-weight:bold;">Törlés</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo $be_van_lepve ? '12' : '11'; ?>" style="text-align:center; color:#999;">Nincsenek adatok a táblában.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>