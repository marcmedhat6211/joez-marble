<?php

namespace App\UserBundle\Controller\Administration;

use App\ServiceBundle\Service\UserService;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\UserType;
use App\UserBundle\Model\UserInterface;
use App\UserBundle\Repository\UserRepository;
use App\UserBundle\Service\UserOperationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administrator")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $paginator = $this->getAdministrators($request, $userRepository);

        return $this->render('user/admin/admin/index.html.twig', [
            "paginator" => $paginator
        ]);
    }

    /**
     * @Route("/new", name="admin_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $user = new User();
        $user->addRole(User::ROLE_ADMIN);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("admin_index");
        }

        return $this->render('user/admin/admin/new.html.twig', [
            'form' => $form->createView(),
            'admin' => $user
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Administrator updated successfully");

            return $this->redirectToRoute("admin_index");
        }

        return $this->render('user/admin/admin/edit.html.twig', [
            'form' => $form->createView(),
            'admin' => $user,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_delete", methods={"GET", "POST"})
     */
    public function delete(User $user, UserService $userService, UserOperationService $userOperationService): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $userOperationService->deleteUser($user, $userService->getUserName());
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("admin_index");
    }

    private function getAdministrators(Request $request, UserRepository $userRepository)
    {
        $search = new \stdClass();
        $search->deleted = 0;
        $search->role = User::ROLE_ADMIN;

        return $userRepository->filter($search, false, true, 10, $request);
    }
}