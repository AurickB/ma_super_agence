<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LandController extends  AbstractController
{
    /**
     * @Route ("/terrain", name="land.index")
     * @return Response
     */

    public function index(): Response
    {
        return $this->render('land/index.html.twig', [
            'current_menu' => 'land',
        ]);
    }
}