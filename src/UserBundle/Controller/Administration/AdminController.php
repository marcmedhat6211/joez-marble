<?php

namespace App\UserBundle\Controller\Administration;

use App\ServiceBundle\Service\UserService;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\AdministrationType;
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
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $administrators = $this->getAdministrators($userRepository);

        return $this->render('user/admin/admin/index.html.twig', [
            "administrators" => $administrators
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
        $form = $this->createForm(AdministrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute("admin_index");
        }

        return $this->render('user/admin/admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);
        $form = $this->createForm(AdministrationType::class, $user);
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

    /**
     * Lists all Category entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="admin_datatable", methods={"GET"})
     */
    public function dataTable(Request $request, UserRepository $userRepository)
    {
        $srch = $request->query->all("search");
        $start = $request->query->getInt("start");
        $length = $request->query->getInt("length");
        $ordr = $request->query->all("order");

        $search = new \stdClass;
        $search->deleted = 0;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];
        $search->role = 'ROLE_ADMIN';

        $count = $userRepository->filter($search, true);
        $admins = $userRepository->filter($search, false, $start, $length);

        return $this->render("user/admin/admin/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "admins" => $admins,
            )
        );
    }

    private function getAdministrators(UserRepository $userRepository): array
    {
        $search = new \stdClass();
        $search->enabled = 1;
        $search->role = User::ROLE_ADMIN;

        return $userRepository->filter($search);
    }
}