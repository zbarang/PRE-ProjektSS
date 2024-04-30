<?php
session_start();
?>

<nav class="nav"> 
    <ul>
      
        <li class="li"><a href="index.php">Startseite</a></li>
        
        <?php if(isset($_SESSION['userid'])) { ?> <!-- Nur anzeigen, wenn Benutzer angemeldet ist -->
            <li class="li"><a href="eingabe.php">Kalorientracker</a></li>
            <li class="li"><a href="wassertracker.php">Wassertracker</a></li>
        <?php } else { ?>
            <li class="li"><a href="login.php">Login</a></li>
        <?php } ?>
    </ul> 
</nav>