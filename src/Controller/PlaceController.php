<?php

namespace App\Controller;

use App\Entity\Place;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\Serializer\SerializerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public function getAllPlace(Request $request, PlaceRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 5);
        $limit = $limit < 1 ? 1 : $limit; 
        $places = $repo->findWithPagination($page, $limit);

        $context = SerializationContext::create()->setGroups(['getAllEvent']);
        $jsonPlace = $serializer->serialize($places, 'json', $context);

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
        if ($place->isStatus()){
            $jsonPlace = $serializer->serialize($place, 'json');

            return new JsonResponse($jsonPlace, Response::HTTP_OK, ['accept'=>'json'], true);
        } else {
            return new JsonResponse(array('PLACE NOT AVAILABLE'));
        }   
    }


    /**
     * Route pour créer un place
     *
     * @param Request $request
     * @param ArtisteRepository $artisteRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/place', name: 'place.create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN", message: 'Chech, vous n\'avez pas le bon rôle')]
    public function createPlace(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $place = $serializer->deserialize(
            $request->getContent(),
            Place::class,
            'json'
        );
        $place->setStatus(true);

        $errors = $validator->validate($place);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($place);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(['getPlace']);

        $jsonPlace = $serializer->serialize($place, 'json', $context);
        $location = $urlGenerator->generate('place.get', ['idPlace'=> $place->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        return new JsonResponse($jsonPlace, Response::HTTP_CREATED, ['Location'=>$location], true);
    }

    /**
     * Route qui supprime un place en fonction de son ID
     * 
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/place/delete/{idPlace}', name: 'place.delete', methods: ['DELETE'])]
    #[ParamConverter("place", options : ["id" => "idPlace"])]
    public function deletePlace(Place $place, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($place);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Route qui permet de modifier l'place
     *
     * @param Place $place
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/place/{idPlace}', name: 'place.update', methods: ['PUT'])]
    #[ParamConverter("place", options : ["id" => "idPlace"])]
    public function updateEvent(Place $place, Request $request, EventRepository $eventRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatePlace = $serializer->deserialize(
            $request->getContent(),
            Place::class,
            'json',
        );

        $place->setPlaceName($updatePlace->getPlaceName() ?? $place->getPlaceName());
        $place->setPlaceAddress($updatePlace->getPlaceAddress() ?? $place->getPlaceAddress());
        $place->setPlaceRegion($updatePlace->getPlaceRegion() ?? $place->getPlaceRegion());
        $place->setStatus(true);

        $entityManager->persist($place);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(['getPlace']);
        $jsonPlace = $serializer->serialize($place, 'json', $context);
        $location = $urlGenerator->generate('place.get', ['idPlace'=> $place->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        return new JsonResponse($jsonPlace, Response::HTTP_CREATED, ['Location'=>$location], true);
    }    
}
