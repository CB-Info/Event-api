<?php

namespace App\Controller;

use App\Entity\Artiste;
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
    public function getAllArtiste(Request $request, ArtisteRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $limit = $limit < 1 ? 1 : $limit; 
        $artists = $repo->findWithPagination($page, $limit);
        $jsonArtist = $serializer->serialize($artists, 'json', ["groups" => "getAllArtiste"]);

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
        if ($artiste->isStatus()){
            $jsonArtist = $serializer->serialize($artiste, 'json', ["groups" => "getArtiste"]);

            return new JsonResponse($jsonArtist, Response::HTTP_OK, ['accept'=>'json'], true);
        } else {
            return new JsonResponse(array('EVENT NOT AVAILABLE'));
        }   
        
    }

    /**
     * Route qui supprime un artiste en fonction de son ID
     * 
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/artiste/delete/{idArtiste}', name: 'artiste.delete', methods: ['DELETE'])]
    #[ParamConverter("artiste", options : ["id" => "idArtiste"])]
    public function deleteArtiste(Artiste $artiste, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($artiste);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Route qui met à jour le status d'un artiste en fonction de son ID (true or false)
     * 
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/artiste/{idArtiste}', name: 'artiste.turnonoff', methods: ['DELETE'])]
    #[ParamConverter("artiste", options : ["id" => "idArtiste"])]
    public function deleteArtisteStatus(Artiste $artiste, EntityManagerInterface $entityManager): JsonResponse
    {
        if($artiste->isStatus()){
            $artiste->setStatus(false);
        } else {
            $artiste->setStatus(true);
        }
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Route pour créer un artiste
     *
     * @param Request $request
     * @param ArtisteRepository $artisteRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/artiste', name: 'artiste.create', methods: ['POST'])]
    public function createArtiste(Request $request, EventRepository $eventRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $artiste = $serializer->deserialize(
            $request->getContent(),
            Artiste::class,
            'json'
        );
        $artiste->setStatus(true);

        // $content = $request->toArray();
        // $event = $eventRepository->find($content['idEvent'] ?? -1);
        // $artiste->setEvent($event);

        $errors = $validator->validate($artiste);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($artiste);
        $entityManager->flush();

        $jsonArtiste = $serializer->serialize($artiste, 'json', ['groups'=>'getEvent']);
        $location = $urlGenerator->generate('artiste.get', ['idArtiste'=> $artiste->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        return new JsonResponse($jsonArtiste, Response::HTTP_CREATED, ['Location'=>$location], true);
    }

}
