<?php

namespace App\Controller\Admin;

use App\Entity\Properties;
use App\Form\PropertyType;
use App\Repository\PropertiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{

    /**
     * @var PropertiesRepository
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

    /**
     * @Route("/admin", name="admin.property.index")
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', [
            'properties' => $properties,
            'current_menu' => 'management',
        ]);
    }

    /**
     * @Route("/admin/property/create", name="admin.property.new")
     * @param Request $request
     * @return Response
     */
    // Méthode qui permet créer des biens
    public function new(Request $request)
    {
        $property = new Properties();
        // Utilisation du formulaire grâce à la méthode createForm() qui prend en paramètre le type puis les données
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){ // On vérifie le formulaire
            // Avant de flush() il faut persister la nouvelle entité afin qu'elle soit traqué par l'entityManager
            $this->em->persist($property);
            // Si ok, on apporte les changement à la base de données...
            $this->em->flush();
            $this->addFlash('success', 'Votre bien a été créer avec succès');

            // ... puis en redirige l'utilisateur
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Properties $property
     * @param Request $request
     * @return Response
     */
    // Méthode qui permet l'édition des biens
    public function edit(Properties $property, Request $request)
    {
        // Utilisation du formulaire grâce à la méthode createForm() qui prend en paramètre le type puis les données
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){ // On vérifie le formulaire
            // Si ok, on apporte les changement à la base de données...
            $this->em->flush();
            $this->addFlash('success', 'Votre bien a été modifié avec succès');
            // ... puis en redirige l'utilisateur
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            // Envoie du formulaire à la vue grâce à la méthode createView()
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
     * @param Properties $property
     * @param Request $request
     * @return Response
     */
    // Méthode qui permet supprimer des biens
    public function delete(Properties $property, Request $request)
    {
        // On vérifie si le token csrf est valide grâce à la méthode qui prend l'id du token et le token grâce à la méthode get
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))){
            //$this->em->remove($property);
            //$this->em->flush();;
            $this->addFlash('success', 'Votre bien a été supprimé avec succès');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->redirectToRoute('admin.property.index');
    }
}