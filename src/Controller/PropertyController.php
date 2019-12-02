<?php 

namespace App\Controller;

use App\Entity\Properties;
use App\Repository\PropertiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PropertyController extends AbstractController
{

    // On récupère notre enregistrement par injection dans le construteur

    /**
     * @var PropertiesRepositoy
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    public function __construct(PropertiesRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    
    }

    // On paramètre notre vue

    /**
     * @Route ("/biens", name="property.index")
     * @return Response
     */

    public function index(): Response
    {
        // Récupération de l'enregistrement via find(), findAll(),findOneBy() qui prend un tableau de critère (exemple = findOneby(['floor' => 3])) ou en appelant une méthode du Repository
        // $property = $this->repository->findAllVisible();
        // $property[0]->setSold(true);
        // $this->em->flush();

        // // On cré une nouvelle entité
        // $property = new Properties();
        // $property->setTitle('Mon premier bien')
        //     ->setPrice(200000)
        //     ->setRooms(4)
        //     ->setBedrooms(3)
        //     ->setDescription("Ma petite annonce")
        //     ->setSurface(60)
        //     ->setFloor(3)
        //     ->setHeat(1)
        //     ->setCity('Muret')
        //     ->setAdress('10 Avenue Jacques Douzans')
        //     ->setPostalCode('31600');
        // // Pour envoyer notre entité dans la bdd on a besoin d'un entityManager
        // $em = $this->getDoctrine()->getManager();
        // // On persiste notre entité
        // $em->persist($property);
        // // On porte tous les changements fait dans l'entityManager dans la bdd
        // $em->flush();
        $properties = $this->repository->findAll();
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties' =>$properties,
            ]);
    }

    /**
     * @Route ("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Properties $property
     * @param string $slug
     * @return Response
     */

    public function show(Properties $property, string $slug): Response
    {
        if ($property->getSlug() !== $slug){ // On vérifie si le slug correspond...
            // Si ce n'est pas le cas on redirige vers la route
            return $this->redirectToRoute('property.show', [
                'id' =>$property->getId(),
                'slug'=>$property->getSlug(),
                ], 301);
        }
        return $this->render('property/show.html.twig', [
            'property' => $property, 
        ]);
    }
}

