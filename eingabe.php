<?php
include_once("navigation.php"); 

// Überprüfen, ob der Benutzer nicht angemeldet ist
if (!isset($_SESSION['userid'])) {
    // Wenn nicht, weiterleiten zur Login-Seite
    header("Location: login.php");
    exit;
}

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Daten aus dem Formular abrufen
    $lebensmittel_id = $_POST['lebensmittel'];
    $menge = $_POST['menge'];
    $datum = $_POST['datum'];

    // Kalorien pro 100g für das ausgewählte Lebensmittel abrufen
    $kalorien_pro_100g_query = mysqli_query($db, "SELECT kalorien_pro_100g FROM lebensmittel WHERE id = $lebensmittel_id");
    $kalorien_pro_100g_row = mysqli_fetch_assoc($kalorien_pro_100g_query);
    $kalorien_pro_100g = $kalorien_pro_100g_row['kalorien_pro_100g'];

     // Kalorien für die eingegebene Menge berechnen
     $kalorien = ($menge / 100) * $kalorien_pro_100g;

    $existing_entry = mysqli_query($db, "SELECT * FROM nutzer_mahlzeiten WHERE user_id = {$_SESSION['userid']} AND lebensmittel_id = $lebensmittel_id AND datum = '$datum'");
    if (mysqli_num_rows($existing_entry)> 0) {
        $existing_row = mysqli_fetch_assoc($existing_entry);  
        $updated_menge = $existing_row['menge_gramm'] + $menge; 
        mysqli_query($db, "UPDATE nutzer_mahlzeiten SET menge_gramm = menge_gramm + $kalorien WHERE id = {$existing_row['id']}");
        echo "Daten erfolgreich aktualisiert";
    } else {
    // SQL-Insert-Befehl vorbereiten und ausführen
    $sql = "INSERT INTO nutzer_mahlzeiten (user_id, lebensmittel_id, menge_gramm, datum) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iiis", $_SESSION['userid'], $lebensmittel_id, $kalorien, $datum); // Hier wird $kalorien anstelle von $menge eingefügt

    if ($stmt->execute()) {
        echo "Daten erfolgreich eingefügt.";
    } else {
        echo "Fehler beim Einfügen der Daten: " . $stmt->error;
    }

    // Statement schließen
    $stmt->close();
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eingabe</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
   

    <div class="container">
    <h2>Lebensmittel eingeben</h2>

    <form action="" method="post">
        <label for="lebensmittel">Lebensmittel auswählen: </label>

        <?php
        require_once "config.php";
        
       

        $selectedValue = isset($_POST['lebensmittel']) ? $_POST['lebensmittel'] : ''; // Hier wird überprüft, ob 'lebensmittel' im $_POST-Array existiert

        $result = mysqli_query($db, "SELECT id, name FROM lebensmittel");

        if (!$result) {
            die("Abfrage fehlgeschlagen: " . mysqli_error($db));
        }
        ?>
        <select name="lebensmittel" style="width: 100%; padding: 10px; font-size: 16px; appearance: none; height: 40px;" required>
        <?php
                $result = mysqli_query($db, "SELECT id, name FROM lebensmittel");
                if (!$result) {
                    die("Abfrage fehlgeschlagen: " . mysqli_error($db));
                }
                while ($row = mysqli_fetch_assoc($result)) {
                    $value = $row['id'];
                    $description = $row['name'];
                    $selected = ($value == $_POST['lebensmittel']) ? 'selected' : '';
                    echo "<option value='$value' $selected>$description</option>";
                }
                ?>
            </select>

            <label for="menge">Menge (in Gramm):</label>
            <input type="text" name="menge" class="form-control" required>

            <label for="datum" style="width: 100%">Datum:</label>
            <input type="date" name="datum" class="form-control" required>  

            <?php 
                require_once "config.php";
                $result = mysqli_query($db, "SELECT id, menge_gramm FROM nutzer_mahlzeiten");
                if (!$result) {
                    die("Abfrage gehlgeschlagen: " . mysqli_error( $db));
                }
                while ($row = mysqli_fetch_assoc($result)) {
                    // HTML-Paragraphen mit dem abgerufenen Inhalt erstellen und ausgeben
                    echo "<p>" . "gegessene Kalorien: " . $row["menge_gramm"] . "</p>";
                }
            
            ?>

            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
    </form>
</div>

</body>
</html>

