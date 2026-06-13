<?php
$files = glob('admin/*.php');
foreach ($files as $file) {
    if ($file == 'admin/index.php' || $file == 'admin/dashboard.php') continue; // Skip index and dash
    $content = file_get_contents($file);
    if (strpos($content, 'css/admin-theme.css') === false) {
        $content = str_replace(
            '<link href="css/style.css" rel=\'stylesheet\' type=\'text/css\' />',
            "<link href=\"css/style.css\" rel='stylesheet' type='text/css' />\n<link href=\"css/admin-theme.css\" rel='stylesheet' type='text/css' />",
            $content
        );
        file_put_contents($file, $content);
    }
}
echo "Done";
