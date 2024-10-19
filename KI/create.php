<?php
define('AES_KEY', 'testestes');  
define('AES_METHOD', 'AES-256-CBC');


function encrypt_content($content) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_METHOD));
    $encrypted = openssl_encrypt($content, AES_METHOD, AES_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}


if (isset($_GET['directory'])) {
    if (isset($_POST['filename'], $_POST['type'])) {
        if (preg_match('/^[\w\-. ]+$/', $_POST['filename'])) {
            if ($_POST['type'] == 'directory') {
                mkdir($_GET['directory'] . $_POST['filename']);
            } else {
                $file_content = isset($_POST['file_content']) ? $_POST['file_content'] : '';
                $encrypted_content = encrypt_content($file_content);
                
                file_put_contents($_GET['directory'] . $_POST['filename'], $encrypted_content);
            }
            if ($_GET['directory']) {
                header('Location: index.php?file=' . urlencode($_GET['directory']));
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            exit('Please enter a valid name!');
        }
    }
} else {
    exit('Invalid directory!');
}
?>



<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>File Management System</title>
		<link href="style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body>
        <div class="file-manager">

            <div class="file-manager-header">
                <h1>Create</h1>
            </div>

            <form action="" method="post">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="directory">Directory</option>
                    <option value="file">File</option>
                </select>

                <label for="filename">Name</label>
                <input id="filename" name="filename" type="text" placeholder="Name" required>

                <label for="file_content">File Content (optional)</label>
                <textarea id="file_content" name="file_content" placeholder="Enter file content here..."></textarea>

                <button type="submit">Save</button>
            </form>

        </div>
    </body>
</html>