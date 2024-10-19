<?php
define('AES_KEY', 'testestes');  
define('AES_METHOD', 'AES-256-CBC');

function decrypt_content($encrypted_content) {
    $data = base64_decode($encrypted_content);
    $iv_length = openssl_cipher_iv_length(AES_METHOD);
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, AES_METHOD, AES_KEY, 0, $iv);
}

if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    if (is_file($file)) {
        $encrypted_content = file_get_contents($file);
        
        $decrypted_content = decrypt_content($encrypted_content);
        
        if ($decrypted_content === false) {
            exit('Error: Unable to decrypt the file content.');
        }

        echo '<pre>' . htmlspecialchars($decrypted_content) . '</pre>';
    } else {
        exit('Error: File not found.');
    }
} else {
    exit('Error: No file specified.');
}
?>
