<?php

declare(strict_types=1);

/*
 * This file is part of the "messenger" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Messenger\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Messenger\MessageBusInterface;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use WapplerSystems\Messenger\Configuration\MessengerConfiguration;

class MessengerBaseConfigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (! interface_exists(MessageBusInterface::class)) {
            throw new LogicException(
                'Messenger support cannot be enabled as the Messenger component is not installed. Try running "composer require symfony/messenger".'
            );
        }

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/../Configuration/Configurator'));
        $loader->load('messenger_patch.php');
        $loader->load('messenger.php');

        /* use typo3 YamlFileLoader because currently there is no way to extend the container configuration by extension */

        $yamlLoader = new YamlFileLoader();
        $config = $yamlLoader->load('typo3conf/ext/messenger/Configuration/Messenger.yaml');
        MessengerConfiguration::getInstance()->setConfig($config);
    }
}
