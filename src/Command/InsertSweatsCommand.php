<?php

namespace App\Command;

use App\Entity\Sweatshirt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:insert-sweats',
    description: 'Ajoute 3 sweats mis en avant dans la base de données.',
)]
class InsertSweatsCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sweats = [
            ['Blackbelt', 29.90, 'blackbelt.jpg'],
            ['Pokeball', 45.00, 'pokeball.jpg'],
            ['BornInUsa', 59.90, 'borninusa.jpg'],
        ];

        foreach ($sweats as [$name, $price, $image]) {
            $sweat = new Sweatshirt();
            $sweat->setName($name)
                  ->setPrice($price)
                  ->setImage($image)
                  ->setIsFeatured(true) // <- ici on corrige le nom
                  ->setStockXS(5)
                  ->setStockS(5)
                  ->setStockM(5)
                  ->setStockL(5)
                  ->setStockXL(5);

            $this->em->persist($sweat);
        }

        $this->em->flush();
        $output->writeln('<info>3 sweats ajoutés avec succès !</info>');

        return Command::SUCCESS;
    }
}
