<?php 
namespace App\Controller;

use App\Repository\PropertiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 
class HomeController extends AbstractController{

    /**
     * @Route ("/", name="home")
     * @param PropertiesRepository $repository
     * @return Response
     */

    public function index(PropertiesRepository $repository): Response
    {
        $properties = $repository->findLatest(); 
        return $this->render('pages/home.html.twig', ['properties' => $properties]);
    }
}

