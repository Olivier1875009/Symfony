<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Borrow;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Status;
use App\Tools;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

ini_set('date.timezone', 'America/New_York');
header('Access-Control-Allow-Origin: *');

class BorrowController extends AbstractController
{
    private $em = null;

    #[Route('/borrows/borrow/{idBorrow}')]
    public function getOne($idBorrow, Request $request, Connection $connexion)
    {
        $query = 
        "SELECT * FROM borrows b
        INNER JOIN books o ON b.idBook = o.idBook
        WHERE idBorrow = $idBorrow";

        $borrowData = $connexion->fetchAllAssociative($query)[0];

        $borrow = [
            "idBorrow" => $borrowData["idBorrow"],
            "borrowedDate" => $borrowData["borrowedDate"],
            "dueDate" => $borrowData["dueDate"],
            "returnedDate" => $borrowData["returnedDate"],
        ];

        $book = [
            "idBook" => $borrowData["idBook"],
            "idGenre" => $borrowData["idGenre"],
            "idAuthor" => $borrowData["idAuthor"],
            "title" => $borrowData["title"],
            "description" => $borrowData["description"],
            "isbn" => $borrowData["isbn"],
            "isBorrowed" => $borrowData["isBorrowed"],
            "cover" => $borrowData["cover"],
            "publishedDate" => $borrowData["publishedDate"],
            "originalLanguage" => $borrowData["originalLanguage"],
        ];

        $borrow["book"] = $book;

        return $this->json($borrow);
    }

    //--------------------------------
    // Route to get all the borrows
    //--------------------------------
    #[Route('/borrows')]
    public function getAllBorrows(Request $request, Connection $connexion): JsonResponse
    {
        $query = "SELECT b.*, u.*, o.* 
            FROM borrows b 
            INNER JOIN users u ON b.idUser = u.idUser 
            INNER JOIN books o ON b.idBook = o.idBook";

        $borrowsData = $connexion->fetchAllAssociative($query);

        $borrows = [];
        foreach ($borrowsData as $row) {
            $borrow = [
                "idBorrow" => $row["idBorrow"],
                "borrowedDate" => $row["borrowedDate"],
                "dueDate" => $row["dueDate"],
                "returnedDate" => $row["returnedDate"],
            ];

            $user = [
                "idUser" => $row["idUser"],
                "memberNumber" => $row["memberNumber"],
                "firstName" => $row["firstName"],
                "lastName" => $row["lastName"],
                "roles" => $row["roles"],
            ];

            $book = [
                "idBook" => $row["idBook"],
                "title" => $row["title"],
            ];

            $borrow["user"] = $user;
            $borrow["book"] = $book;
            $borrows[] = $borrow;
        }
        return $this->json($borrows);
    }

    //--------------------------------
    // Route to get all the borrows
    //--------------------------------
    #[Route('/active-borrows')]
    public function getActiveBorrows(Request $request, Connection $connection): JsonResponse
    {
        $query = "SELECT b.*, u.*, o.* 
            FROM borrows b 
            INNER JOIN users u ON b.idUser = u.idUser 
            INNER JOIN books o ON b.idBook = o.idBook
            WHERE returnedDate IS NULL";

        $borrowsData = $connection->fetchAllAssociative($query);

        $borrows = [];
        foreach ($borrowsData as $row) {
            $borrow = [
                "idBorrow" => $row["idBorrow"],
                "borrowedDate" => $row["borrowedDate"],
                "dueDate" => $row["dueDate"],
                "returnedDate" => $row["returnedDate"],
            ];

            $user = [
                "idUser" => $row["idUser"],
                "memberNumber" => $row["memberNumber"],
                "firstName" => $row["firstName"],
                "lastName" => $row["lastName"],
                "roles" => $row["roles"],
            ];

            $book = [
                "idBook" => $row["idBook"],
                "title" => $row["title"],
            ];

            $borrow["user"] = $user;
            $borrow["book"] = $book;
            $borrows[] = $borrow;
        }
        return $this->json($borrows);
    }

    #[Route('/borrows/{idUser}')]
    public function getBorrowsFromUser($idUser, Request $request, Connection $connexion): JsonResponse
    {
        $borrowsData = $connexion->fetchAllAssociative("
        SELECT * FROM borrows b
        INNER JOIN books g ON b.idBook = g.idBook
        WHERE idUser = $idUser 
        AND returnedDate IS NULL
        ");

        $borrows = [];
        foreach ($borrowsData as $row) {
            $borrow = [
                "idBorrow" => $row["idBorrow"],
                "idUser" => $row["idUser"],
                "borrowedDate" => $row["borrowedDate"],
                "dueDate" => $row["dueDate"],
                "returnedDate" => $row["returnedDate"],
            ];

            $book = [
                "idBook" => $row["idBook"],
                "idGenre" => $row["idGenre"],
                "idAuthor" => $row["idAuthor"],
                "title" => $row["title"],
                "description" => $row["description"],
                "isbn" => $row["isbn"],
                "isBorrowed" => $row["isBorrowed"],
                "cover" => $row["cover"],
                "publishedDate" => $row["publishedDate"],
                "originalLanguage" => $row["originalLanguage"],
            ];

            $borrow["book"] = $book;
            $borrows[] = $borrow;
        }

        return $this->json($borrows);
    }

    #[Route('/borrows/history/{idUser}')]
    public function getBorrowsHistoryFromUser($idUser, Request $request, Connection $connexion): JsonResponse
    {
        //$borrows = $connexion->fetcha("SELECT * FROM borrows WHERE idUser=$idUser");
        $borrowsData = $connexion->fetchAllAssociative("
        SELECT * FROM borrows b
        INNER JOIN books g ON b.idBook = g.idBook
        WHERE idUser = $idUser
        AND returnedDate IS NOT NULL
        ");

        $borrows = [];
        foreach ($borrowsData as $row) {
            $borrow = [
                "idBorrow" => $row["idBorrow"],
                "idUser" => $row["idUser"],
                "borrowedDate" => $row["borrowedDate"],
                "dueDate" => $row["dueDate"],
                "returnedDate" => $row["returnedDate"],
            ];

            $book = [
                "idBook" => $row["idBook"],
                "idGenre" => $row["idGenre"],
                "idAuthor" => $row["idAuthor"],
                "title" => $row["title"],
                "description" => $row["description"],
                "isbn" => $row["isbn"],
                "isBorrowed" => $row["isBorrowed"],
                "cover" => $row["cover"],
                "publishedDate" => $row["publishedDate"],
                "originalLanguage" => $row["originalLanguage"],
            ];

            $borrow["book"] = $book;
            $borrows[] = $borrow;
        }

        return $this->json($borrows);
    }

    #[Route('/borrows/{idUser}/{order}')]
    public function getBorrowsOrderedBy($idUser, $order, Request $request, Connection $connexion): JsonResponse
    {
        //$borrows = $connexion->fetcha("SELECT * FROM borrows WHERE idUser=$idUser");
        $borrowsData = $connexion->fetchAllAssociative("
        SELECT * FROM borrows b
        INNER JOIN books g ON b.idBook = g.idBook
        WHERE idUser = $idUser
        AND returnedDate IS NULL
        ORDER BY b.$order
        ");

        $borrows = [];
        foreach ($borrowsData as $row) {
            $borrow = [
                "idBorrow" => $row["idBorrow"],
                "idUser" => $row["idUser"],
                "borrowedDate" => $row["borrowedDate"],
                "dueDate" => $row["dueDate"],
                "returnedDate" => $row["returnedDate"],
            ];

            $book = [
                "idBook" => $row["idBook"],
                "idGenre" => $row["idGenre"],
                "idAuthor" => $row["idAuthor"],
                "title" => $row["title"],
                "description" => $row["description"],
                "isbn" => $row["isbn"],
                "isBorrowed" => $row["isBorrowed"],
                "cover" => $row["cover"],
                "publishedDate" => $row["publishedDate"],
                "originalLanguage" => $row["originalLanguage"],
            ];

            $borrow["book"] = $book;
            $borrows[] = $borrow;
        }

        return $this->json($borrows);
    }

    #[Route('/borrows/history/{idUser}/{order}')]
    public function getBorrowsHistoryOrderedBy($idUser, $order, Request $request, Connection $connexion): JsonResponse
    {
        //$borrows = $connexion->fetcha("SELECT * FROM borrows WHERE idUser=$idUser");
        $borrowsData = $connexion->fetchAllAssociative("
        SELECT * FROM borrows b
        INNER JOIN books g ON b.idBook = g.idBook
        WHERE idUser = $idUser
        AND returnedDate IS NOT NULL
        ORDER BY b.$order
        ");

        $borrows = [];
        foreach ($borrowsData as $row) {
            $borrow = [
                "idBorrow" => $row["idBorrow"],
                "idUser" => $row["idUser"],
                "borrowedDate" => $row["borrowedDate"],
                "dueDate" => $row["dueDate"],
                "returnedDate" => $row["returnedDate"],
            ];

            $book = [
                "idBook" => $row["idBook"],
                "idGenre" => $row["idGenre"],
                "idAuthor" => $row["idAuthor"],
                "title" => $row["title"],
                "description" => $row["description"],
                "isbn" => $row["isbn"],
                "isBorrowed" => $row["isBorrowed"],
                "cover" => $row["cover"],
                "publishedDate" => $row["publishedDate"],
                "originalLanguage" => $row["originalLanguage"],
            ];

            $borrow["book"] = $book;
            $borrows[] = $borrow;
        }

        return $this->json($borrows);
    }

    #[Route('renew/{idBorrow}')]
    public function renouvellement($idBorrow, Request $request, Connection $connexion): JsonResponse
    {
        //$borrows = $connexion->fetcha("SELECT * FROM borrows WHERE idUser=$idUser");
        $borrow = $connexion->executeStatement("
        UPDATE borrows
        SET dueDate = DATE_ADD(dueDate, INTERVAL 1 MONTH)
        WHERE idBorrow = $idBorrow;
        ");

        return $this->json($borrow);
    }

    //je vais repasser pour split la fonction en deux avec setStatusBorrowed
    #[Route('/create-Borrow')]
    public function createBorrow(Request $req, ManagerRegistry $doctrine): JsonResponse
    {

        if ($req->getMethod() == 'POST') {

            $this->em = $doctrine->getManager();
            $borrow = new Borrow();
            $borrow = $this->setBorrow($req, $borrow);
           
            //$this->setStatusBorrowed($req);
            $book = $this->em->getRepository(Book::class)->find($req->request->get('idBook'));
            $status = $this->em->getRepository(Status::class)->find(2);
            $book->setStatus($status);
            if($book->getStatus()->getIdStatus()==2){
                return new JsonResponse([]); 
            }
            else{
                $this->em->persist($borrow);
                $this->em->flush();
                $this->em->persist($book);
                $this->em->flush();
                return new JsonResponse(['message' => 'Borrow created successfully']);
            }
            
        }
    }

    function setBorrow($req, $borrow)
    {
        $book = $this->em->getRepository(Book::class)->find($req->request->get('idBook'));
        $user = $this->em->getRepository(User::class)->find($req->request->get('idUser'));

        $borrow->setUser($user);
        $borrow->setBook($book);

        $borrow->setBorrowedDate(new \DateTime());
        $borrow->setDueDate(new \DateTime('+1 week'));

        return $borrow;
    }


    //dans la requete jai le id du livre, donc jenvoie la requete pis je change le status du livre que son id est dans la requete
    // function setStatusBorrowed($req,ManagerRegistry $doctrine){
    ///     $this->em = $doctrine->getManager();
    ///     $book = $this->em->getRepository(Book::class)->find($req->request->get('idBook'));
    ///     $book->setStatus(2);
    ///     $this->$em->persist($book);
    ///     $this->$em->flush();
    //  }

    #[Route('/return-borrow/{idBorrow}')]
    public function returnBorrow($idBorrow, Request $req, ManagerRegistry $doctrine): JsonResponse
    {

        if ($req->getMethod() == 'POST') {
            $this->em = $doctrine->getManager();

            $borrow = $this->em->getRepository(Borrow::class)->find($idBorrow);


            if (!$borrow) {
                return new JsonResponse(['error' => 'Borrow not found'], 404);
            }

            $dateTime = new DateTimeImmutable();
            $borrow->setReturnedDate($dateTime);

            $this->em->persist($borrow);
            $this->em->flush();

            return new JsonResponse(['message' => 'Borrow returned successfully']);
        }
    }
}
