<?php

namespace WapplerSystems\Messenger\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Command\AbstractFailedMessagesCommand;


class FailedMessagesShowDummyCommand extends AbstractFailedMessagesCommand
{
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
