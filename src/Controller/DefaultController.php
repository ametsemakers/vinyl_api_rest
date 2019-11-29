<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Vinyl;

class DefaultController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $vinyls = $this->getDoctrine()
                ->getRepository(Vinyl::Class)
                ->find(1);

        $vinyls = $vinyls->getSongs();
        
        return $this->render('index.html.twig', [
            'vinyls' => $vinyls,
        ]);
    }
}