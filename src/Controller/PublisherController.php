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
        $entityManager = $doctrine->getManager();
        $filters = $entityManager->getFilters();
        dd($filters);
        $publishers = $doctrine->getRepository(Publisher::class)->findAll();

        $data = [];

        foreach ($publishers as $publisher) {
            $data[] = [
                'id' => $publisher->getId(),
                'publisher_name' => $publisher->getPublisherName(),
            ];
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
            throw new \Exception("Издатель с id {$id} не найден!");
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
                throw new \Exception("Книги с id {$books_id} нет в базе данных");
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
            'message' => "Издатель {$publisher->getPublisherName()} изменен успешно! Изменения сохранены в базу данных",
        ]);
    }

    #[Route('/api/publisher/delete/{id}', name: 'delete_publisher', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $publisher = $doctrine->getRepository(Publisher::class)->find($id);

        if (!$publisher) {
            throw new \Exception("Издатель с id {$id} не найден!");
        }

        $publisher->setDeletedAt(new \DateTime('now'));

        $entityManager->persist($publisher);
        $entityManager->flush();

        return $this->json([
            'message' => 'Издатель успешно удален!',
        ]);
    }
}
