<?php
declare(strict_types=1);
use DI\Container;
use App\Repository\UserRepository;
//...

return function(Container $container){
    $container->set('user_repository', new UserRepository($container));
}
?>
