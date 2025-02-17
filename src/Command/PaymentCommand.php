<?php

namespace App\Command;

use App\Service\PaymentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:example')]
class PaymentCommand extends Command
{
    public function __construct(private PaymentService $paymentService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Process a payment via Shift4 or ACI')
            ->addArgument('provider', InputArgument::REQUIRED, 'The payment provider (aci|shift4)')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount')
            ->addArgument('currency', InputArgument::REQUIRED, 'Currency')
            ->addArgument('card_number', InputArgument::REQUIRED, 'Card Number')
            ->addArgument('exp_year', InputArgument::REQUIRED, 'Expiration Year')
            ->addArgument('exp_month', InputArgument::REQUIRED, 'Expiration Month')
            ->addArgument('cvv', InputArgument::REQUIRED, 'CVV');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $input->getArgument('provider');
        $data = [
            'amount' => $input->getArgument('amount'),
            'currency' => $input->getArgument('currency'),
            'card_number' => $input->getArgument('card_number'),
            'exp_year' => $input->getArgument('exp_year'),
            'exp_month' => $input->getArgument('exp_month'),
            'cvv' => $input->getArgument('cvv')
        ];

        try {
            $response = $this->paymentService->processPayment($provider, $data);
            $output->writeln(json_encode($response, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
}
