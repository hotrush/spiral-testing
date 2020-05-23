<?php

declare(strict_types=1);

namespace Hotrush\Spiral\Testing\Bootloader;

use Hotrush\Spiral\Testing\EntitiesFactory\EntitiesFactory;
use Hotrush\Spiral\Testing\EntitiesFactory\EntitiesFactoryInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\Container;
use Spiral\Files\FilesInterface;

class EntitiesFactoryBootloader extends Bootloader
{
    public function boot(Container $container)
    {
        if (env('TESTING_MODE', false)) {
            $container->bindSingleton(EntitiesFactoryInterface::class, EntitiesFactory::class);
            $this->loadFactories($container);
        }
    }

    protected function loadFactories(Container $container): void
    {
        $files = $container->get(FilesInterface::class);

        foreach ($files->getFiles(directory('testingFactories'), '*.php') as $file) {
            require $file;
        }
    }
}