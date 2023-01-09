<?php

declare(strict_types=1);

/*
 * This file is part of the "messenger" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
    public function __construct(
        ?string $globalReceiverName,
        ServiceProviderInterface $failureTransports,
        MessageBusInterface $messageBus,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger = null
    ) {
        parent::__construct($globalReceiverName, $failureTransports);
    }


    protected function configure(): void
    {
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
