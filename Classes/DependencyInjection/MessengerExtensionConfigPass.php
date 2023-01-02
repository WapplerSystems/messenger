<?php

declare(strict_types=1);

/*
 * This file is part of the "messenger" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Messenger\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use WapplerSystems\Messenger\Configuration\MessengerConfiguration;

class MessengerExtensionConfigPass implements CompilerPassInterface
{
    private string $extKey;

    public function __construct($extKey)
    {
        $this->extKey = $extKey;
    }

    public function process(ContainerBuilder $container)
    {
        $yamlLoader = new YamlFileLoader();
        $config = $yamlLoader->load('typo3conf/ext/' . $this->extKey . '/Configuration/Messenger.yaml');

        MessengerConfiguration::getInstance()->mergeConfig($config);
    }
}
