<?php
// src/Controller/ClientController.php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Entity\Dette;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   
    #[Route("/clients", name:"client.index", methods:["GET"])]
    #[Route("/clients", name: "client.index", methods: ["GET"])]
    #[Route("/clients", name: "client.index", methods: ["GET"])]
public function index(Request $request, ClientRepository $clientRepository): Response
{
    $surname = $request->query->get('surname');
    $telephone = $request->query->get('telephone');

    $clients = $clientRepository->findByFilters($surname, $telephone);

    if ($request->isXmlHttpRequest()) {
        $clientData = array_map(function ($client) {
            return [
                //'id' => $client->getId(),
                'surname' => $client->getSurname(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
            ];
        }, $clients);

        return $this->json(['clients' => $clientData]);
    }

    return $this->render('client/index.html.twig', [
        'clients' => $clients,
    ]);
}

     #[Route("/client/{id}/dettes", name:"client.dettes", methods:["GET"])]
     
    public function showDebts(int $id, ClientRepository $clientRepository): Response
    {
       
        $client = $clientRepository->find($id);

        if (!$client) {
            throw $this->createNotFoundException('Client non trouvé');
        }

        // Récupérer les dettes du client
        $dettes = $client->getDettes(); 

        
        return $this->render('client/debts.html.twig', [
            'client' => $client,
            'dettes' => $dettes, 
        ]);
    }

    
     #[Route("/client/add", name:"client.add", methods:["POST" ]) ]
     
    public function addClient(Request $request, ClientRepository $clientRepository): Response
    {
       
        $nickname = $request->request->get('nickname');
        $phone = $request->request->get('phone');
        $address = $request->request->get('address');

        $client = new Client();
        $client->setSurname($nickname);
        $client->setTelephone($phone);
        $client->setAdresse($address);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'client' => [
                'surname' => $client->getSurname(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
            ],
        ]);
    }


    

    

    



    #[Route('/debts/create/{clientId}', name: 'debt.create', methods: ['POST'])]
    public function createDebt(Request $request, int $clientId, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        // Récupère le client à partir de l'identifiant fourni
        $client = $entityManager->getRepository(Client::class)->find($clientId);
        if (!$client) {
            return $this->json(['success' => false, 'error' => 'Client non trouvé'], 404);
        }
    
        $montant = $data['montant'];
        $montantVerser = $data['montantVerser'];
    
        // Crée une nouvelle dette et l'associe au client
        $dette = new Dette();
        $dette->setMontant($montant);
        $dette->setMontantVerser($montantVerser);
        $dette->setCreateAt(new \DateTimeImmutable());
        $dette->setClient($client);
    
        // Persist et sauvegarde la dette
        $entityManager->persist($dette);
        $entityManager->flush();
    
        return $this->json(['success' => true]);
    }
}
    
