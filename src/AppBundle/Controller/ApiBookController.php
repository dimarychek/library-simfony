<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use BookBundle\Entity\Book;
use AuthorBundle\Entity\Author;

class ApiBookController extends FOSRestController
{
    /**
     * @Rest\Get("/api/v1/books")
     */
    public function getAction()
    {
        $restData = $this->getDoctrine()->getRepository('BookBundle:Book')->findAll();

        if ($restData === null) {
            return new View("There are no books exist", Response::HTTP_NOT_FOUND);
        }

        return $restData;
    }

    /**
     * @Rest\Get("/api/v1/books/{id}")
     */
    public function idAction($id)
    {
        $restData = $this->getDoctrine()->getRepository('BookBundle:Book')->find($id);

        if ($restData === null) {
            return new View("Book not found", Response::HTTP_NOT_FOUND);
        }

        return $restData;
    }

    /**
     * @Rest\Post("/api/v1/books/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $name = $request->get('name');
        $author = (int) $request->get('author_id');

        $manager = $this->getDoctrine()->getManager();
        $book = $this->getDoctrine()->getRepository('BookBundle:Book')->find($id);

        if (!empty($author)) {
            $author = $this->getDoctrine()->getRepository('AuthorBundle:Author')->find($author);
        }

        if (empty($book)) {
            return new View("Book not found", Response::HTTP_NOT_FOUND);

        } else if (!empty($name) && !empty($author)) {
            $book->setName($name);
            $book->setAuthorId($author);
            $manager->flush();

            return new View("Book updated successfully", Response::HTTP_OK);

        } else if (empty($name) && !empty($author)) {
            $book->setAuthorId($author);
            $manager->flush();

            return new View("Book author updated successfully", Response::HTTP_OK);

        } else if (!empty($name) && empty($author)) {
            $book->setName($name);
            $manager->flush();

            return new View("Book name updated successfully", Response::HTTP_OK);

        } else {
            return new View("Book name or author cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @Rest\Delete("/api/v1/books/{id}")
     */
    public function deleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $book = $this->getDoctrine()->getRepository('BookBundle:Book')->find($id);

        if (empty($book)) {
            return new View("Book not found", Response::HTTP_NOT_FOUND);
        } else {
            $manager->remove($book);
            $manager->flush();
        }

        return new View("Book deleted successfully", Response::HTTP_OK);
    }
}