<?php


$login_error = "";
$reg_success = "";
$reg_error = "";

if (isset($_POST['login_submit'])) {
    $login = trim($_POST['login_nev']);
    $password = $_POST['jelszo'];

    if (!empty($login) && !empty($password)) {
        $stmt = $dbh->prepare("SELECT * FROM felhasznalok WHERE login_nev = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['jelszo'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['vezeteknev'] . ' ' . $user['keresztnev'];
            $_SESSION['user_login'] = $user['login_nev'];
            
            header("Location: index.php?oldal=fooldal");
            exit();
        } else {
            $login_error = "Hibás felhasználónév vagy jelszó!";
        }
    } else {
        $login_error = "Minden mezőt ki kell tölteni!";
    }
}

if (isset($_POST['register_submit'])) {
    $login = trim($_POST['reg_login']);
    $vnev = trim($_POST['reg_vnev']);
    $knev = trim($_POST['reg_knev']);
    $pass = $_POST['reg_jelszo'];

    if (!empty($login) && !empty($vnev) && !empty($knev) && !empty($pass)) {
        $checkStmt = $dbh->prepare("SELECT id FROM felhasznalok WHERE login_nev = ?");
        $checkStmt->execute([$login]);
        
        if ($checkStmt->fetch()) {
            $reg_error = "Ez a felhasználónév már foglalt! Kérjük, válasszon másikat.";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            
            $insertStmt = $dbh->prepare("INSERT INTO felhasznalok (login_nev, vezeteknev, keresztnev, jelszo) VALUES (?, ?, ?, ?)");
            if ($insertStmt->execute([$login, $vnev, $knev, $hashed_pass])) {
                $reg_success = "Sikeres regisztráció! Most már bejelentkezhet.";
            } else {
                $reg_error = "Hiba történt a regisztráció során. Kérjük, próbálja újra később.";
            }
        }
    } else {
        $reg_error = "Minden mező kitöltése kötelező a regisztrációhoz!";
    }
}
?>

<main class="shop-container" style="margin-top: 30px;">
    <h2>Felhasználói fiók</h2>
    <p style="margin-bottom: 20px;">A Notebook Kezelő és az Üzenetek menüpontok eléréséhez kérjük, jelentkezzen be.</p>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        
        <div style="flex: 1; min-width: 280px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3>Bejelentkezés</h3>
            <?php if(!empty($login_error)) echo "<p style='color:red; font-weight:bold; margin-bottom:10px;'>$login_error</p>"; ?>
            
            <form action="index.php?oldal=bejelentkezes" method="POST" style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                <label>Felhasználónév:</label>
                <input type="text" name="login_nev" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                
                <label>Jelszó:</label>
                <input type="password" name="jelszo" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                
                <button type="submit" name="login_submit" style="padding: 10px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 10px;">Belépés</button>
            </form>
        </div>

        <div style="flex: 1; min-width: 280px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3>Regisztráció</h3>
            <?php 
                if(!empty($reg_error)) echo "<p style='color:red; font-weight:bold; margin-bottom:10px;'>$reg_error</p>";
                if(!empty($reg_success)) echo "<p style='color:green; font-weight:bold; margin-bottom:10px;'>$reg_success</p>";
            ?>
            
            <form action="index.php?oldal=bejelentkezes" method="POST" style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                <label>Választott felhasználónév (Login):</label>
                <input type="text" name="reg_login" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                
                <label>Vezetéknév:</label>
                <input type="text" name="reg_vnev" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                
                <label>Keresztnév:</label>
                <input type="text" name="reg_knev" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                
                <label>Jelszó:</label>
                <input type="password" name="reg_jelszo" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                
                <button type="submit" name="register_submit" style="padding: 10px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 10px;">Regisztráció beküldése</button>
            </form>
        </div>

    </div>
</main>