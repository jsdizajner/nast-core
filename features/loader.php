<?php
defined( 'ABSPATH' ) || exit;

$directories = glob('*', GLOB_ONLYDIR);
// Loop through each directory
foreach ($directories as $directory) {
    // Create the file path based on the directory name
    $filePath = $directory . '/' . $directory . '.php';

    // Check if the file exists in the current directory
    if (file_exists($filePath)) {
        // Include the file
        include $filePath;
    }
}

