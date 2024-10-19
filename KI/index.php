<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$initial_directory = '';
$current_directory = $initial_directory;

if (isset($_GET['file'])) {
    if (is_dir($_GET['file'])) {
        $current_directory = $_GET['file'] . '/';
    } else {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($_GET['file']) . '"');
        readfile($_GET['file']);
        exit;
    }
}

$results = glob(str_replace(['[',']',"\f[","\f]"], ["\f[","\f]",'[[]','[]]'], ($current_directory ? $current_directory : $initial_directory)) . '*');
$directory_first = true;

if ($directory_first) {
    usort($results, function($a, $b){
        $a_is_dir = is_dir($a);
        $b_is_dir = is_dir($b);
        if ($a_is_dir === $b_is_dir) {
            return strnatcasecmp($a, $b);
        } else if ($a_is_dir && !$b_is_dir) {
            return -1;
        } else if (!$a_is_dir && $b_is_dir) {
            return 1;
        }
    });
}

function convert_filesize($bytes, $precision = 2) {
    $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function get_filetype_icon($filetype) {
    if (is_dir($filetype)) {
        return '<i class="fa-solid fa-folder"></i>';
    } else if (preg_match('/image\/*/', mime_content_type($filetype))) {
        return '<i class="fa-solid fa-file-image"></i>';
    } else if (preg_match('/video\/*/', mime_content_type($filetype))) {
        return '<i class="fa-solid fa-file-video"></i>';
    } else if (preg_match('/audio\/*/', mime_content_type($filetype))) {
        return '<i class="fa-solid fa-file-audio"></i>';
    }
    return '<i class="fa-solid fa-file"></i>';
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
            <h1><?php echo $current_directory; ?></h1>
            <a href="create.php?directory=<?php echo $current_directory; ?>"><i class="fa-solid fa-plus"></i></a>
        </div>
        <table class="file-manager-table">
            <thead>
                <tr>
                    <td class="selected-column">Name<i class="fa-solid fa-arrow-down-long fa-xs"></i></td>
                    <td>Size</td>
                    <td>Modified</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($_GET['file']) && realpath($current_directory) != realpath($initial_directory)): ?>
                <tr>
                    <td colspan="10" class="name"><i class="fa-solid fa-folder"></i><a href="?file=<?php echo urlencode($_GET['file']) . '/..'; ?>">...</a></td>
                </tr>
                <?php endif; ?>
                <?php foreach ($results as $result): ?>
                <tr class="file">
                    <td class="name"><?php echo get_filetype_icon($result); ?><a class="view-file" href="?file=<?php echo urlencode($result); ?>"><?php echo basename($result); ?></a></td>
                    <td><?php echo is_dir($result) ? 'Folder' : convert_filesize(filesize($result)); ?></td>
                    <td class="date"><?php echo str_replace(date('F j, Y'), 'Today,', date('F j, Y H:ia', filemtime($result))); ?></td>
                    <td class="actions">
                        <a href="rename.php?file=<?php echo urlencode($result); ?>" class="btn"><i class="fa-solid fa-pen fa-xs"></i></a>
                        <a href="delete.php?file=<?php echo urlencode($result); ?>" class="btn red" onclick="return confirm('Are you sure you want to delete <?php echo basename($result); ?>?')"><i class="fa-solid fa-trash fa-xs"></i></a>
                        <?php if (!is_dir($result)): ?>
                            <a href="?file=<?php echo urlencode($result); ?>" class="btn green"><i class="fa-solid fa-download fa-xs"></i></a>   
                            <a href="viewer.php?file=<?php echo urlencode($result); ?>" class="btn blue"><i class="fa-solid fa-eye fa-xs"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
