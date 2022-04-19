<?php

namespace WapplerSystems\Messenger\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Command\AbstractFailedMessagesCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

class FailedMessagesRetryDummyCommand extends AbstractFailedMessagesCommand
{


    public function __construct(?string $globalReceiverName, ServiceProviderInterface $failureTransports, MessageBusInterface $messageBus, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger = null)
    {


        parent::__construct($globalReceiverName, $failureTransports);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        return 0;
    }

}
