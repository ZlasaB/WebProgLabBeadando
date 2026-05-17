<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notebook Webshop</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h2>Notebook Webshop</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php?oldal=fooldal">Főoldal</a></li>
                    <li><a href="index.php?oldal=galeria">Galéria</a></li>
                    <li><a href="index.php?oldal=kapcsolat">Kapcsolat</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?oldal=uzenetek">Üzenetek</a></li>
                        <li><a href="index.php?oldal=crud_notebooks">Notebook Kezelő</a></li>
                        <li><a href="index.php?oldal=kijelentkezes" class="menu-auth">Kijelentkezés (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="index.php?oldal=bejelentkezes" class="menu-auth">Bejelentkezés</a></li>
                        <li><a href="index.php?oldal=bejelentkezes" class="menu-auth">Regisztráció</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>