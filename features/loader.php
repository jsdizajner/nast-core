<?php
namespace CORE_LOADER;

defined( 'ABSPATH' ) || exit;

class CORE_LOADER
{
    private $directories = [];

    public function __constructor()
    {
        // Set directories
        $this->directories = glob('*', GLOB_ONLYDIR);

    }

    /**
     * Load all features form the list
     *
     * @return void
     */
    public static function initiate_features()
    {
        // Loop through each directory
        foreach ($this->directories as $directory) {
            // Create the file path based on the directory name
            $filePath = $directory . '/' . $directory . '.php';

            // Check if the file exists in the current directory
            if (file_exists($filePath)) {
                // Include the file
                include $filePath;
            }
        }
    }


}

