<?php

namespace App\Console;

use App\Controller\AuthorController;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'app:delete-authors-without-books')]
class DeleteAuthorsWithoutBooks extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $conn = new Connection();

        return Command::SUCCESS;

    }
}