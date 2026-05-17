<?php
$query = "SELECT u.*, f.login_nev 
          FROM uzenetek u 
          LEFT JOIN felhasznalok f ON u.felhasznaloid = f.id 
          ORDER BY u.kuldes_ideje DESC";
$stmt = $dbh->query($query);
$uzenetek = $stmt->fetchAll();
?>
 
<main class="shop-container" style="margin-top:20px;">
<h2>Beérkezett üzenetek (Adminisztráció)</h2>
<p>A legfrissebb üzenetek láthatóak legfelül.</p>
<table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse; background:#fff; margin-top:15px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
<tr style="background:#2c3e50; color:white;">
<th>Időpont</th>
<th>Küldő neve (Felhasználóneve)</th>
<th>Üzenet</th>
</tr>
<?php if(empty($uzenetek)): ?>
<tr><td colspan="3" style="text-align:center;">Még nem érkezett üzenet.</td></tr>
<?php else: ?>
<?php foreach ($uzenetek as $u): ?>
<tr>
<td style="color:#666; font-size:0.9rem;"><?= $u['kuldes_ideje'] ?></td>
<td>
<?php 
                        if ($u['felhasznaloid'] === null) {
                            echo "<strong>Vendég</strong> (" . htmlspecialchars($u['nev']) . ")";
                        } else {
                            echo htmlspecialchars($u['nev']) . " (<em>" . htmlspecialchars($u['login_nev']) . "</em>)";
                        }
                        ?>
</td>
<td><?= nl2br(htmlspecialchars($u['uzenet'])) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</main>