<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/client/{id}', name: 'client_show')]
    public function show(Client $client): JsonResponse
    {
        $addresses = $client->getAddresses();
        $addressDetails = null;

        foreach ($addresses as $address) {
            if ($address->getType() == 1) {
                $addressDetails = [
                    'number' => $address->getNumber(),
                    'cptInfo' => $address->getCptInfo(),
                    'street' => $address->getStreet(),
                    'postalCode' => $address->getPostalCode(),
                    'city' => $address->getCity(),
                    'country' => $address->getCountry()
                ];
                break;
            }
        }

        return new JsonResponse([
            'id' => $client->getId(),
            'company' => $client->getCompany(),
            'vatNumber' => $client->getVatNumber(),
            'phone' => $client->getPhone(),
            'email' => $client->getEmail(),
            'address' => $addressDetails,
        ]);
    }
}