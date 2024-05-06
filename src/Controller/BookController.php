<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'show_all_books', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $books = $entityManager->getRepository(Book::class)->findAll();

        $data = [];


        foreach ($books as $book) {
            $authors = $book->getAuthors();
            $authors_data = [];
            foreach ($authors as $author) {
                $authors_data[] = [
                    'id' => $author->getId(),
                    'family_name' => $author->getFamilyName(),
                    'first_name' => $author->getFirstName(),
                ];
            }

            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'authors' => $authors_data,
                'publisher' => $book->getPublisher()->getPublisherName(),
                'publish_year' => $book->getPublishYear(),
            ];
        }

        return $this->json($data);
    }

    /**
     * @throws \ErrorException
     */
    #[Route('/api/book/create', name: 'create_book', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $book = $doctrine->getRepository(Book::class)->findOneBy(['title' => $request->request->get('title')]);

        // check if book exists in database
        if ($book !== null) {
            throw new \ErrorException("Книга с названием \"{$request->request->get('title')}\" уже есть в базе данных!", 500);
        }


        $book = new Book();
        $book->setTitle($request->request->get('title'));

        $publisher = $doctrine->getRepository(Publisher::class)->find(['id' => $request->request->get('publisher_id')]);

        if ($publisher == null) {
            throw new \ErrorException("Издателя с id {$request->request->get('publisher_id')} нет в базе данных");
        }

        $author = $doctrine->getRepository(Author::class)->find(['id' => $request->request->get('author_id')]);

        if ($author == null) {
            throw new \ErrorException("Издателя с id {$request->request->get('author_id')} нет в базе данных");
        }

        $book->setPublisher($request->request->get('publisher'));
        $book->setPublishYear($request->request->get('publish_year'));
        $book->addAuthor($author);
        $book->setPublisher($publisher);

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->json([
            'message' => 'Книга успешно создана!',
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/book/delete/{id}', name: 'delete_book', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $book = $doctrine->getRepository(Book::class)->find($id);

        if (!$book) {
            throw new \Exception("Книга с id {$id} не найден!");
        }

        $book->setDeletedAt(new \DateTime('now'));

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->json([
            'message' => 'Книга успешно удалена!',
        ]);
    }
}
