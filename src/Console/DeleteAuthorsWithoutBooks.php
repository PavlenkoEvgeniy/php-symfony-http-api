<?php

namespace App\Console;



use App\Repository\AuthorRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'app:delete-authors-without-books')]
class DeleteAuthorsWithoutBooks extends Command
{
    private EntityRepository $entityRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
       $this->entityRepository = $authorRepository;

        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $authors = $this->entityRepository->findAll();

        $entityManager = $this->entityRepository->getEntityManager();


        foreach ($authors as $author) {
            $books = $author->getBooks();

            $books_data = [];
            foreach ($books as $book) {
                $books_data[] = [
                    $book->getTitle(),
                ];
            }

            if ($books_data === []) {
                $author->setDeletedAt(new \DateTime('now'));
                $entityManager->persist($author);
            }

        }
        $entityManager->flush();

        echo "Authors without books were deleted successfully! (Soft Delete)" . PHP_EOL;
        return Command::SUCCESS;
    }
}