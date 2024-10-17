<?php
// Ensure captured GET param exists
if (isset($_GET['file'])) {
    // Check if file is a directory
    if (is_dir($_GET['file'])) {
        // Attempt to delete the directory
        if (rmdir($_GET['file'])) {
            // Delete success! Redirect to file manager page
            header('Location: index.php');
            exit;
        } else {
            // Delete failed - directory is empty or insufficient permissions
            exit('Directory must be empty!');
        }
    } else {
        // Delete the file
        unlink($_GET['file']);
        // Delete success! Redirect to file manager page
        header('Location: index.php');
        exit;
    }
} else {
    exit('Invalid Request!');
}
?>

