<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-product-fixtures',
    description: 'Generate fixture products with random data'
)]
class GenerateProductFixturesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('count', InputArgument::OPTIONAL, 'Number of products to generate', 10)
            ->setHelp('This command generates random product fixtures for testing purposes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = (int) $input->getArgument('count');

        // Check if products already exist
        $existingProducts = $this->entityManager->getRepository(Product::class)->count([]);
        if ($existingProducts > 0) {
            $io->warning("Products already exist in the database ({$existingProducts} found). Skipping generation.");
            return Command::SUCCESS;
        }

        $io->title('Generating Product Fixtures');
        $io->text("Generating {$count} products...");

        $progressBar = $io->createProgressBar($count);
        $progressBar->start();

        for ($i = 0; $i < $count; $i++) {
            $product = new Product(
                price: $this->generateRandomPrice(),
                vat: $this->generateRandomVat()
            );

            $this->entityManager->persist($product);
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->newLine(2);
        $io->success("Successfully generated {$count} product fixtures!");

        return Command::SUCCESS;
    }

    private function generateRandomPrice(): float
    {
        return round(rand(100, 10000) / 100, 2);
    }

    private function generateRandomVat(): float
    {
        $vatRates = [0.0, 5.5, 10.0, 20.0];
        return $vatRates[array_rand($vatRates)];
    }
} 