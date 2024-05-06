<?php

namespace App\Console;


use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;




#[AsCommand(name: 'app:delete-authors-without-books')]
class DeleteAuthorsWithoutBooks extends Command
{
    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $conn = DriverManager::getConnection(['driver' => 'pdo_mysql',
//                'user' => 'root',
//                'password' => 'root',
//                'host' => 'db',
//                'dbname' => 'symfony-http-api']);

        return Command::SUCCESS;
    }
}