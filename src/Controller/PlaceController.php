<?php

namespace App\Controller;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PlaceController extends AbstractController
{
    #[Route('/place', name: 'app_place')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlaceController.php',
        ]);
    }

    /**
     * Route qui renvoie tous les lieux
     *
     * @param PlaceRepository $repo
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/place', name: 'place.getAll', methods: ['GET'])]
    public function getAllPlace(PlaceRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $events=$repo->findAll();
        $jsonPlace = $serializer->serialize($events, 'json', ["groups" => "getAllEvent"]);

        return new JsonResponse($jsonPlace, Response::HTTP_OK, [], true);
    }

    /**
     * Route qui renvoit un lieu en fonction de son ID
     * 
     * @param PlaceRepository $repo
     * @param SerializerInterface $serializer
     * @return JsonResponse
     
    #[Route('/api/place/{id}', name: 'place.get', methods: ['GET'])]
    public function getPlace(int $id, PlaceRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $place=$repo->find($id);
        $jsonPlace = $serializer->serialize($place, 'json');

        return $place ?
        new JsonResponse($jsonPlace, Response::HTTP_OK, [], true) :
        new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
    */

    #[Route('/api/place/{idPlace}', name: 'place.get', methods: ['GET'])]
    #[ParamConverter("place", options : ["id" => "idPlace"])]
    public function getPlace(Place $place, SerializerInterface $serializer): JsonResponse
    {
        $jsonPlace = $serializer->serialize($place, 'json');

        return new JsonResponse($jsonPlace, Response::HTTP_OK, ['accept'=>'json'], true);
    }
}
