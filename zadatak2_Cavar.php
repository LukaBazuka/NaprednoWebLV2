<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload i kriptiranje dokumenata</title>
</head>
<body>
    <h1>Upload i kriptiranje dokumenata</h1>
    <h2>Upload datoteke</h2>
    <!-- Forma za upload datoteke -->
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <button type="submit">Upload</button>
    </form>
    
    <hr>

    <h2>Dekriptirane datoteke</h2>
    <?php
    # Definiraj funkciju za provjeru ekstenzije datoteke
    function isAllowedFile($file_extension) {
        $allowedTypes = array('pdf', 'jpeg', 'jpg', 'png');
        return in_array(strtolower($file_extension), $allowedTypes);
    }

    # Funkcija za kreiranje direktorija ako ne postoje
    function createDirectories($directories) {
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir);
            }
        }
    }

    # Funkcija za generiranje nasumičnog ključa i IV
    function generateKeyAndIv() {
        return [
            'key' => openssl_random_pseudo_bytes(32),
            'iv' => openssl_random_pseudo_bytes(16)
        ];
    }

    # Funkcija za kriptiranje datoteke
    function encryptFile($file_path, $encryption_key, $iv, $encrypted_path) {
        $encrypted_data = openssl_encrypt(file_get_contents($file_path), 'aes-256-cbc', $encryption_key, 0, $iv);
        file_put_contents($encrypted_path, $encrypted_data);
        file_put_contents($encrypted_path . '.key', $encryption_key . $iv);
        unlink($file_path);  # Briše originalnu datoteku nakon kriptiranja
    }

    # Funkcija za dekriptiranje datoteke
    function decryptFile($encrypted_path, $decrypted_folder) {
        $key_iv = file_get_contents($encrypted_path . '.key');
        $encryption_key = substr($key_iv, 0, 32);
        $iv = substr($key_iv, 32, 16);
        $encrypted_data = file_get_contents($encrypted_path);
        $decrypted_data = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        $decrypted_path = $decrypted_folder . '/decrypted_' . basename($encrypted_path, '.enc');
        file_put_contents($decrypted_path, $decrypted_data);
        return $decrypted_path;
    }

    # Funkcija za prikaz linkova za preuzimanje dekriptiranih datoteka
    function displayDecryptedFiles($encrypted_folder, $decrypted_folder) {
        if (!file_exists($encrypted_folder)) {
            echo "Nema kriptiranih datoteka za dekriptiranje.";
            return;
        }

        $decrypted_files = array();
        foreach (glob($encrypted_folder . '/*.key') as $key_file) {
            $encrypted_file = str_replace('.key', '', $key_file);
            $decrypted_path = decryptFile($encrypted_file, $decrypted_folder);
            $decrypted_files[] = $decrypted_path;
        }

        foreach ($decrypted_files as $decrypted_file) {
            echo "<p><a href='$decrypted_file'>Preuzmi " . basename($decrypted_file) . "</a></p>";
        }
    }

    # Obrada POST zahtjeva za upload datoteke
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
        $file = $_FILES["file"];
        $file_name = $file["name"];
        $file_tmp = $file["tmp_name"];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        # Provjeri ekstenziju datoteke
        if (!isAllowedFile($file_extension)) {
            echo "Nedozvoljena ekstenzija datoteke.";
            exit();
        }

        $upload_folder = 'uploads';
        $encrypted_folder = 'encrypted';
        $decrypted_folder = 'decrypted';
        createDirectories([$upload_folder, $encrypted_folder, $decrypted_folder]);

        $upload_path = $upload_folder . '/' . $file_name;
        move_uploaded_file($file_tmp, $upload_path);

        $keys = generateKeyAndIv();
        $encrypted_path = $encrypted_folder . '/encrypted_' . $file_name;
        encryptFile($upload_path, $keys['key'], $keys['iv'], $encrypted_path);
    }

    # Prikaz linkova za preuzimanje dekriptiranih datoteka
    displayDecryptedFiles('encrypted', 'decrypted');
    ?>
</body>
</html>
