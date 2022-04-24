<?php

namespace WapplerSystems\Messenger\Configuration;

final class MessengerConfiguration
{

    private static ?MessengerConfiguration $instance = null;

    private array $config;

    public static function getInstance(): MessengerConfiguration
    {
        if (static::$instance === null) {
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
        $this->config = array_merge_recursive($this->config,$config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

}
