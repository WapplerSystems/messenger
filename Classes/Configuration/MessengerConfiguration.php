<?php

declare(strict_types=1);

/*
 * This file is part of the "messenger" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Messenger\Configuration;

final class MessengerConfiguration
{
    private static ?MessengerConfiguration $instance = null;

    private array $config;

    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function mergeConfig(array $config)
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
