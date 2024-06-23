<?php

namespace App\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:review-analysis',
    description: 'Displays the day or month with the highest number of published reviews.',
)]
final class ReviewAnalysisCommand extends Command
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Displays the day or month with the highest number of published reviews.')
            ->addOption(
                'month',
                null,
                InputOption::VALUE_NONE,
                'Display the month with the highest number of published reviews'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $monthOption = $input->getOption('month');

        if ($monthOption) {
            $query = $this->entityManager->createQuery(
                'SELECT r.publishedAt AS date, COUNT(r.id) AS reviewCount
                 FROM App\Entity\Review r
                 GROUP BY date
                 ORDER BY reviewCount DESC, date DESC'
            );
        } else {
            $query = $this->entityManager->createQuery(
                'SELECT r.publishedAt AS date, COUNT(r.id) AS reviewCount
                 FROM App\Entity\Review r
                 GROUP BY date
                 ORDER BY reviewCount DESC, date DESC'
            );
        }

        $results = $query->getResult();

        if (empty($results)) {
            $output->writeln('No reviews found.');
            return Command::SUCCESS;
        }

        $maxReviewCount = 0;
        $maxDate = null;

        foreach ($results as $result) {
            $date = $result['date']->format('Y-m-d'); // Adjust the format as needed

            if ($result['reviewCount'] > $maxReviewCount) {
                $maxReviewCount = $result['reviewCount'];
                $maxDate = $date;
            }
        }

        if ($maxDate) {
            if ($monthOption) {
                $output->writeln('Month with the highest number of reviews: ' . substr($maxDate, 0, 7)); // Output YYYY-MM
            } else {
                $output->writeln('Day with the highest number of reviews: ' . $maxDate); // Output YYYY-MM-DD
            }
        } else {
            $output->writeln('No reviews found.');
        }


        return Command::SUCCESS;
    }
}
