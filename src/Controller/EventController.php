<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManager;
use App\Repository\EventRepository;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/EventController.php',
        ]);
    }

    /**
     * Route qui renvoie tous les events
     *
     * @param EventRepository $repo
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/event', name: 'event.getAll', methods: ['GET'])]
    public function getAllEvent(EventRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $events=$repo->findAll();
        $jsonEvent = $serializer->serialize($events, 'json', ["groups" => "getAllEvent"]);

        return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
    }

    /**
     * Route qui renvoit un event en fonction de son ID
     * 
     * @param EventRepository $repo
     * @param SerializerInterface $serializer
     * @return JsonResponse
     
    #[Route('/api/event/{id}', name: 'event.get', methods: ['GET'])]
    public function getEvent(int $id, EventRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $event=$repo->find($id);
        $jsonEvent = $serializer->serialize($event, 'json');

        return $event ?
        new JsonResponse($jsonEvent, Response::HTTP_OK, [], true) :
        new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
    */

    #[Route('/api/event/{idEvent}', name: 'event.get', methods: ['GET'])]
    #[ParamConverter("event", options : ["id" => "idEvent"])]
    public function getEvent(Event $event, SerializerInterface $serializer): JsonResponse
    {
        $jsonEvent = $serializer->serialize($event, 'json', ["groups" => "getEvent"]);

        return new JsonResponse($jsonEvent, Response::HTTP_OK, ['accept'=>'json'], true);
    }

    /**
     * Route qui supprime un event en fonction de son ID
     * 
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/event/delete/{idEvent}', name: 'event.delete', methods: ['DELETE'])]
    #[ParamConverter("event", options : ["id" => "idEvent"])]
    public function deleteEvent(Event $event, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($event);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Route qui met à jour le status d'un event en fonction de son ID (true or false)
     * 
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/event/{idEvent}', name: 'event.turnonoff', methods: ['DELETE'])]
    #[ParamConverter("event", options : ["id" => "idEvent"])]
    public function deleteEventStatus(Event $event, EntityManagerInterface $entityManager): JsonResponse
    {
        if($event->isStatus()){
            $event->setStatus(false);
        } else {
            $event->setStatus(true);
        }
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Route pour créer un event
     *
     * @param Request $request
     * @param ArtisteRepository $artisteRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/event', name: 'event.create', methods: ['POST'])]
    public function createEvent(Request $request, ArtisteRepository $artisteRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $event = $serializer->deserialize(
            $request->getContent(),
            Event::class,
            'json'
        );
        $event->setStatus(true);

        $content = $request->toArray();
        $artist = $artisteRepository->find($content['idArtiste'] ?? -1);
        $event->setArtist($artist);

        $errors = $validator->validate($event);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($event);
        $entityManager->flush();

        $jsonEvent = $serializer->serialize($event, 'json', ['groups'=>'getEvent']);
        $location = $urlGenerator->generate('event.get', ['idEvent'=> $event->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ['Location'=>$location], true);
    }    

    
    /**
     * Route qui permet de modifier l'event
     *
     * @param Event $event
     * @param Request $request
     * @param ArtisteRepository $artisteRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/event/{idEvent}', name: 'event.update', methods: ['PUT'])]
    #[ParamConverter("event", options : ["id" => "idEvent"])]
    public function updateEvent(Event $event, Request $request, ArtisteRepository $artisteRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updateEvent = $serializer->deserialize(
            $request->getContent(),
            Event::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $event]
        );

        $updateEvent->setStatus(true);

        $content = $request->toArray();
        $artist = $artisteRepository->find($content['idArtist'] ?? -1);
        $updateEvent->setArtist($artist);

        $entityManager->persist($updateEvent);
        $entityManager->flush();

        $jsonEvent = $serializer->serialize($updateEvent, 'json', ['groups'=>'getEvent']);
        $location = $urlGenerator->generate('event.get', ['idEvent'=> $updateEvent->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ['Location'=>$location], true);
    }    

}
