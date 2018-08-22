<?php 
    
/**
 * Require helper file
 */
require_once __DIR__ . '/src/helper.php';

/**
 * Root namespace for the application
 */
define('ROOT_NAMESPACE', 'Paynow\\');

/**
 * Simple PSR-4 compliant autoloader
 * 
 * @link (GitHub Gist, https://gist.github.com/melmups/a0800b07e58089297c1735cfcc9fd382)
 */
spl_autoload_register(function($class) {
        // Remove the root namespace
        if (substr($class, 0, strlen(ROOT_NAMESPACE)) == ROOT_NAMESPACE) {
            $relative = substr($class, strlen(ROOT_NAMESPACE));
        } 

        // Bring in the file
        $filename = __DIR__ . "/src/" . str_replace('\\', '/', $relative) . ".php";

        // Check if the file exists
        if (file_exists($filename)) {
            require_once($filename);
            if (class_exists($class)) {
                return true;
            }
        }

        return false;
});
