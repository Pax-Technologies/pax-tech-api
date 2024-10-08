<?php

namespace App\Command;

use App\Service\InvoiceService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(name: 'app:send-invoice-email')]
class SendInvoiceEmailCommand extends Command
{

    public function __construct(private InvoiceService $invoiceService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:send-invoice-email')
            ->setDescription('Check invoices and send emails.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->invoiceService->checkInvoicesAndSendEmails();

        return Command::SUCCESS;
    }
}