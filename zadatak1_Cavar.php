<?php
// Naziv baze podataka
$databaseName = "Radovi";

// Direktorij za backup baze podataka
$backupDirectory = "backup/$databaseName";

// Provjera postoji li direktorij za backup, ako ne postoji stvori ga
if (!is_dir($backupDirectory)) {
    if (!@mkdir($backupDirectory)) { 
        die("<p>Ne možemo stvoriti direktorij: $backupDirectory</p>");
    }
}

// Vrijeme
$timestamp = time();

// Povezivanje na bazu podataka
$databaseConnect = mysqli_connect('localhost', 'root', '', $databaseName) OR die("<p>Ne možemo se povezati na bazu podataka: $databaseName</p>");

// SQL upit za dohvaćanje svih tablica u bazi podataka
$tableResult = mysqli_query($databaseConnect, "SHOW TABLES");

// Provjera postoji li neka tablica u bazi
if(mysqli_num_rows($tableResult) > 0) {
    echo "<p>Radimo backup za bazu podataka: $databaseName</p>";

    // Iteriranje kroz sve tablice u bazi podataka
    while($tableRow = mysqli_fetch_row($tableResult)) {
        $tableName = $tableRow[0];
        $query = "SELECT * FROM $tableName";
        $dataResult = mysqli_query($databaseConnect, $query);

        // Provjera postoje li podaci u tablici
        if(mysqli_num_rows($dataResult) > 0) {
            $backupFilePath = "$backupDirectory/{$tableName}_{$timestamp}.sql.gz";

            // Otvaranje datoteke za pisanje
            if($fileHandle = gzopen($backupFilePath, 'w9')) {
                $columnInfo = mysqli_fetch_fields($dataResult);
                $columns = array_map(function($column) {
                    return $column->name;
                }, $columnInfo);
                // Iteriranje kroz svaki redak u tablici
                while($row = mysqli_fetch_array($dataResult,MYSQLI_NUM)) {
                    gzwrite($fileHandle, "INSERT INTO $tableName (".implode(",", $columns).")\n");
                    gzwrite($fileHandle, "VALUES ('".implode("', '", array_map('addslashes', $row))."');\n");
                }
                // Zatvaranje datoteke
                gzclose($fileHandle);
                echo "<p>Backup za tablicu $tableName uspješno obavljen.</p>";
            } else {
                echo "<p>Datoteka $backupFilePath se ne može otvoriti.</p>";
                break;
            }
        }
    }
} else {
    echo "<p>Baza $databaseName ne sadrži tablice.</p>";
}
?>
