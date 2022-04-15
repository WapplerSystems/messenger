<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use TYPO3\CMS\Core\Core\Environment;

return static function (ContainerConfigurator $container) {

    $container->services()

        ->set('cache.adapter.filesystem', FilesystemAdapter::class)
            ->abstract()
            ->args([
                '', // namespace
                0, // default lifetime
                Environment::getProjectPath().'/var/cache/data/',
                service('cache.default_marshaller')->ignoreOnInvalid(),
            ])
            ->call('setLogger', [service('logger')->ignoreOnInvalid()])
            ->tag('cache.pool', ['clearer' => 'cache.default_clearer', 'reset' => 'reset'])
            ->tag('monolog.logger', ['channel' => 'cache'])

        ->set('cache.app')
            ->parent('cache.adapter.filesystem')
            ->public()
            ->tag('cache.pool', ['clearer' => 'cache.app_clearer'])

        ->set('cache.messenger.restart_workers_signal')
            ->parent('cache.app')
            ->private()
            ->tag('cache.pool');

};
