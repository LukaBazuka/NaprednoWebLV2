<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili Osoba</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .profile img {
            max-width: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .info {
            flex: 1;
        }
        .info h2 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 18px;
        }
        .info p {
            margin: 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Profili Osoba</h1>
        <?php
        // Učitavanje XML datoteke
        $xmlParser = simplexml_load_file("LV2.xml");

        // Iteriranje kroz svaku osobu u XML datoteci
        foreach ($xmlParser as $record) {
            echo "<div class='profile'>";
            
            // Prikazivanje informacija o osobi
            echo "<div class='info'>";
            echo "<h2>Ime: " . $record->ime . "</h2>";
            echo "<h2>Prezime: " . $record->prezime . "</h2>";
            echo "<p><strong>Email:</strong> " . $record->email . "</p>";
            echo "<p><strong>Životopis:</strong> " . $record->zivotopis . "</p>";
            echo "</div>";
            
            // Prikazivanje slike osobe
            echo "<img src='" . $record->slika . "' alt='" . $record->ime . " " . $record->prezime . "'>";
            
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
