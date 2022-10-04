<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Repository\ArtisteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ArtisteController extends AbstractController
{
    #[Route('/artiste', name: 'app_artiste')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArtisteController.php',
        ]);
    }

    /**
     * Route qui renvoie tous les artistes
     *
     * @param ArtisteRepository $repo
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/artiste', name: 'artiste.getAll', methods: ['GET'])]
    public function getAllArtiste(ArtisteRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $artists=$repo->findAll();
        $jsonArtist = $serializer->serialize($artists, 'json', ["groups" => "getAllEvent"]);

        return new JsonResponse($jsonArtist, Response::HTTP_OK, [], true);
    }

     /**
     * Route qui renvoit un artsite en fonction de son ID
     * 
     * @param ArtisteRepository $repo
     * @param SerializerInterface $serializer
     * @return JsonResponse
    */
    #[Route('/api/artiste/{idArtiste}', name: 'artiste.get', methods: ['GET'])]
    #[ParamConverter("artiste", options : ["id" => "idArtiste"])]
    public function getArtsite(Artiste $artiste, SerializerInterface $serializer): JsonResponse
    {
        $jsonArtist = $serializer->serialize($artiste, 'json', ["groups" => "getArtiste"]);

        return new JsonResponse($jsonArtist, Response::HTTP_OK, ['accept'=>'json'], true);
    }

}
