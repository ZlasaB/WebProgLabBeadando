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
        $stmt = $dbh->prepare("SELECT id FROM felhasznalok WHERE login_nev = ?");
        $stmt->execute([$login]);
        
        if ($stmt->fetch()) {
            $reg_error = "Ez a felhasználónév már foglalt!";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $dbh->prepare("INSERT INTO felhasznalok (login_nev, jelszo, vezeteknev, keresztnev) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$login, $hashed_pass, $vnev, $knev])) {
                $reg_success = "Sikeres regisztráció! Kérjük, jelentkezzen be a bal oldali űrlapon.";
            } else {
                $reg_error = "Hiba történt a mentés során.";
            }
        }
    } else {
        $reg_error = "Minden mező kitöltése kötelező!";
    }
}
?>

<main class="shop-container">
    <div style="display: flex; justify-content: space-between; gap: 40px; margin-top: 30px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 280px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3>Bejelentkezés</h3>
            <?php if(!empty($login_error)) echo "<p style='color:red;'>$login_error</p>"; ?>
            
            <form action="index.php?oldal=bejelentkezes" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                <label>Felhasználónév:</label>
                <input type="text" name="login_nev" style="padding: 8px;" required>
                
                <label>Jelszó:</label>
                <input type="password" name="jelszo" style="padding: 8px;" required>
                
                <button type="submit" name="login_submit" style="padding: 10px; background: #2c3e50; color: white; border: none; cursor: pointer;">Belépés</button>
            </form>
        </div>

        <div style="flex: 1; min-width: 280px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3>Regisztráció</h3>
            <?php 
                if(!empty($reg_error)) echo "<p style='color:red;'>$reg_error</p>";
                if(!empty($reg_success)) echo "<p style='color:green;'>$reg_success</p>";
            ?>
            
            <form action="index.php?oldal=bejelentkezes" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                <label>Választott felhasználónév (Login):</label>
                <input type="text" name="reg_login" style="padding: 8px;" required>
                
                <label>Vezetéknév:</label>
                <input type="text" name="reg_vnev" style="padding: 8px;" required>
                
                <label>Keresztnév:</label>
                <input type="text" name="reg_knev" style="padding: 8px;" required>
                
                <label>Jelszó:</label>
                <input type="password" name="reg_jelszo" style="padding: 8px;" required>
                
                <button type="submit" name="register_submit" style="padding: 10px; background: #27ae60; color: white; border: none; cursor: pointer;">Regisztráció</button>
            </form>
        </div>

    </div>
</main>