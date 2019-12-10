<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $repository)
    {
        $this->em =$em;
        $this->encoder=$encoder;
        $this->repository=$repository;
    }
    /**
     * @Route("/inscription", name="register.index")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Votre compte a été créer avec succès');
            return $this->redirectToRoute('login');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/superadmin", name="superadmin.index")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index()
    {
        $user = $this->repository->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $user,
        ]);
    }

    /**
     * @Route("/superadmin/user/create", name="superadmin.user.new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    // Méthode qui permet créer des utilisateurs
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'L\'utilisateur a été créer avec succès');
            return $this->redirectToRoute('superadmin.index');
        }
        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/superadmin/user/{id}", name="superadmin.user.delete", methods="DELETE")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    // Méthode qui permet supprimer les utilisateus
    public function delete(User $user,  Request $request)
    {
        // On vérifie si le token csrf est valide grâce à la méthode qui prend l'id du token et le token grâce à la méthode get
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))){
            //$this->em->remove($property);
            //$this->em->flush();;
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès');
            return $this->redirectToRoute('superadmin.index');
        }
        return $this->redirectToRoute('superadmin.index');
    }
}