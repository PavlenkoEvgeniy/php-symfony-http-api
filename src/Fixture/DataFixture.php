<?php

namespace App\Fixture;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Publisher;

class DataFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // creating publishers
        for ($i = 1; $i <= 5; $i++) {
            $publisher = new Publisher();
            $publisher->setPublisherName('Publisher ' . $i);
            $publisher->setPublisherAddress('Publisher Address ' . $i);
            $publisher->setCreatedAt(new \DateTime('now'));
            $manager->persist($publisher);
        }

        $manager->flush();

        // creating books
        $publishers = $manager->getRepository(Publisher::class)->findAll();
        $publishers_qty = count($publishers);

        for ($i = 0; $i < 10; $i++) {
            $book = new Book();
            $book->setTitle('Book ' . $i);
            $book->setPublishYear(rand(2000, 2024));
            $book->setPublisher($manager->getRepository(Publisher::class)->find(rand(1, $publishers_qty)));
            $book->setCreatedAt(new \DateTime('now'));
            $manager->persist($book);
        }

        $manager->flush();

        // creating authors
        $books = $manager->getRepository(Book::class)->findAll();
        $books_qty = count($books);

        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setFamilyName('Family name ' . $i);
            $author->setFirstName('First name ' . $i);
            $author->addBook($manager->getRepository(Book::class)->find(rand(1, $books_qty)));
            $author->setCreatedAt(new \DateTime('now'));
            $manager->persist($author);
        }

        $manager->flush();
    }
}