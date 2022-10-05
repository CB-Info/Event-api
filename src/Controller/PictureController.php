<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Serializer;

class PictureController extends AbstractController
{
    #[Route('/picture', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }

    #[Route('/api/picture/{idPicture}', name: 'picture.get', methods:['GET'])]
    #[ParamConverter("picture", options : ["id" => "idPicture"])]
    public function getPicture(Picture $picture, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $RlLocation = $picture->getPublicPath() . '/' . $picture->getRealPath();
        $location = $request->getUriForPath('/');
        $location = $location . str_replace('/assets', 'assets', $RlLocation);

        return new JsonResponse($serializer->serialize($picture, 'json', ['groups' => 'getPicture']), Response::HTTP_OK, ['Location'=>$location], true);
    }
    
    #[Route('/api/picture', name: 'picture.create', methods: ['POST'])]
    public function createPicture(Request $request, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $picture = new Picture();
        $files = $request->files->get('file');
        $picture->setFile($files)
            ->setMimeType($files->getClientMimeType())
            ->setRealName($files->getClientOriginalName())
            ->setPublicPath('/assets/pictures') 
            ->setStatus(true)
            ->setUploadDate(new \DateTime());

        $entityManagerInterface->persist($picture);
        $entityManagerInterface->flush();

        $jsonPicture = $serializer->serialize($picture, 'json', ["groups" => "getPicture"]);
        $location = $urlGenerator->generate('picture.get', ['idPicture'=> $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonPicture, JsonResponse::HTTP_CREATED, ['Location'=> $location], "json");

    }
}
