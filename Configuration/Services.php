<?php

declare(strict_types=1);

/*
 * This file is part of the "messenger" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace TYPO3\CMS\Core;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcherInterfaceComponentAlias;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WapplerSystems\Messenger\DependencyInjection\MessengerBaseConfigPass;
use WapplerSystems\Messenger\DependencyInjection\MessengerConfigPass;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $container->services()
        ->set('event_dispatcher', EventDispatcher::class)
        ->public()
        ->tag('container.hot_path')
        ->tag('event_dispatcher.dispatcher', [
            'name' => 'event_dispatcher',
        ])
        ->alias(EventDispatcherInterfaceComponentAlias::class, 'event_dispatcher')
        ->alias(EventDispatcherInterface::class, 'event_dispatcher');

    $containerBuilder->addCompilerPass(new RegisterListenersPass(), PassConfig::TYPE_BEFORE_REMOVING);
    $containerBuilder->addCompilerPass(new MessengerBaseConfigPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 900);
    $containerBuilder->addCompilerPass(new MessengerConfigPass());
    $containerBuilder->addCompilerPass(new MessengerPass());
};
