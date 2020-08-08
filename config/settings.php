<?php

declare(strict_types=1);
use DI\Container;

date_default_timezone_set('America/New_York');
return function(Container $container){
    $container->set('settings', function() {
        return [
            'domain' => 'panjaco.com',
            'error' => [
                'display_error_details' => true,
                'log_error_details' => true,
                'log_errors' => true,
            ],
            'views' => [
                'path' => __DIR__ . '/../src/Views',
                'settings' => ['cache' => false],
            ],
            'connection' => [
                'host' => 'localhost',
                'dbname' => 'radiant',
                'dbuser' => 'root',
                'dbpass' => '',
            ],
            'jwt' => [
                'issuer' => 'www.panjaco.com/radiant/',
                'lifetime' => 900, //15 minutes
                'r_lifetime' => 172800, //2 days
                'secret' => 'my_very_super_99secret_key',
            ],
            'about' => [
                'name' => 'Radiant API',
                'version' => '0.1.0',
            ],
            'root' => dirname(__DIR__ . '/../'),
        ];
    });
};

?>
