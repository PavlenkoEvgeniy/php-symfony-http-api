<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'show_all_authors', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $authors = $entityManager->getRepository(Author::class)->findAll();

        $data = [];

        foreach ($authors as $author) {
            if ($author->getDeletedAt() === null) {
                $books = $author->getBooks();
                $books_data = [];
                foreach ($books as $book) {
                    if ($book->getDeletedAt() === null) {
                        $books_data[] = [
                            'id' => $book->getId(),
                            'title' => $book->getTitle(),
                            'publisher' => $book->getPublisher()->getPublisherName(),
                            'publish_year' => $book->getPublishYear(),
                        ];
                    }
                }

                $data[] = [
                    'id' => $author->getId(),
                    'family_name' => $author->getFamilyName(),
                    'first_name' => $author->getFirstName(),
                    'books' => $books_data,
                ];
            }
        }

        return $this->json($data);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/author/create', name: 'create_author', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $author = $doctrine->getRepository(Author::class)->findOneBy(['family_name' => $request->request->get('family_name')]);

        // check if author exists in database
        if ($author !== null) {

            $family_name = $author->getFamilyName();
            $fist_name = $author->getFirstName();

            //check if family_name and fist_name are equal to record in database
            if ($family_name == $request->request->get('family_name') && $fist_name == $request->request->get('first_name')) {
                throw new \ErrorException('Этот автор уже есть в базе данных!', 500);
            }
        }

        $author = new Author();
        $author->setFamilyName($request->request->get('family_name'));
        $author->setFirstName($request->request->get('first_name'));

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->json([
            'message' => 'Author created successfully!',
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/author/delete/{id}', name: 'delete_author', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $author = $doctrine->getRepository(Author::class)->find($id);

        if (!$author) {
            throw new \Exception("Автор с id {$id} не найден!");
        }

        $author->setDeletedAt(new \DateTime('now'));

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->json([
            'message' => 'Автор успешно удален!',
        ]);
    }

}