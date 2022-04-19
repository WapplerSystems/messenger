<?php

declare(strict_types=1);

namespace TYPO3\CMS\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcherInterfaceComponentAlias;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WapplerSystems\Messenger\DependencyInjection\MessengerConfig;


return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {


    $container->services()->set('event_dispatcher', EventDispatcher::class)
        ->public()
        ->tag('container.hot_path')
        ->tag('event_dispatcher.dispatcher', ['name' => 'event_dispatcher'])
        ->alias(EventDispatcherInterfaceComponentAlias::class, 'event_dispatcher')
        ->alias(EventDispatcherInterface::class, 'event_dispatcher');


    $containerBuilder->addCompilerPass(new MessengerConfig());
    $containerBuilder->addCompilerPass(new MessengerPass());

};
