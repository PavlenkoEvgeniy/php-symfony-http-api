<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Publisher;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PublisherController extends AbstractController
{
    #[Route('/api/publishers', name: 'show_all_publishers', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $publishers = $doctrine->getRepository(Publisher::class)->findAll();

        $data = [];

        foreach ($publishers as $publisher) {
            if ($publisher->getDeletedAt() === null) {
                $data[] = [
                    'id' => $publisher->getId(),
                    'publisher_name' => $publisher->getPublisherName(),
                ];
            }
        }

        return $this->json($data);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/publisher/update/{id}', name: 'update_publisher', methods: ['PUT'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $publisher = $doctrine->getRepository(Publisher::class)->find($id);

        if (!$publisher) {
            throw new \Exception("Publisher with id {$id} was not found!", 500);
        }

        $publisher_books = $publisher->getBooks();
        $data = [];
        foreach ($publisher_books as $publisher_book) {
            $data[] = $publisher_book->getId();
        }

        foreach ($data as $id) {
            $book = $doctrine->getRepository(Book::class)->find($id);
            $publisher->removeBook($book);
            $entityManager->persist($publisher);
        }
        $entityManager->flush();

        $books_ids = explode(',', $request->request->get('books_ids'));

        foreach ($books_ids as $books_id) {
            $book = $doctrine->getRepository(Book::class)->find($books_id);

            if ($book === null) {
                throw new \Exception("Book with id {$books_id} was not found in database");
            }

            $publisher->addBook($book);
            $entityManager->persist($publisher);
        }

        if ($request->request->get('publisher_name')) {
            $publisher->setPublisherName($request->request->get('publisher_name'));
        }

        if ($request->request->get('publisher_address')) {
            $publisher->setPublisherAddress($request->request->get('publisher_address'));
        }

        $publisher->setUpdatedAt(new \DateTime('now'));

        $entityManager->persist($publisher);
        $entityManager->flush();


        return $this->json([
            'message' => "Publisher {$publisher->getPublisherName()} was updated successfully! Changes were saved to database",
        ]);
    }

    #[Route('/api/publisher/delete/{id}', name: 'delete_publisher', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $publisher = $doctrine->getRepository(Publisher::class)->find($id);

        if (!$publisher) {
            throw new \Exception("Publisher with id {$id} was not found", 500);
        }

        $publisher->setDeletedAt(new \DateTime('now'));

        $entityManager->persist($publisher);
        $entityManager->flush();

        return $this->json([
            'message' => 'Publisher was deleted successfully! (Soft Delete)',
        ]);
    }
}
