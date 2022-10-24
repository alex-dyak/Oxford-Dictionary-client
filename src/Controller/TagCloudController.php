<?php

namespace App\Controller;

use App\Entity\Searches;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TagCloudController extends AbstractController
{

    public function tagCloud(ManagerRegistry $doctrine, int $max = 50): Response
    {
        $repository = $doctrine->getRepository(Searches::class);

        $words = $repository->findAllOrdered($max);

        $response = $this->render('tagCloud.html.twig', [
            'words' => $words,
        ]);

        // cache publicly for 3600 seconds
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
