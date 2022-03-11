<?php

namespace App\UserBundle\Controller\Administration;

use App\ServiceBundle\Service\UserService;
use App\ServiceBundle\Utils\Validate;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\Filter\UserFilterType;
use App\UserBundle\Form\UserType;
use App\UserBundle\Model\UserInterface;
use App\UserBundle\Repository\UserRepository;
use App\UserBundle\Security\CustomAuthenticator;
use App\UserBundle\Service\UserOperationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(
        Request $request
    ): Response {
        $filterForm = $this->createForm(UserFilterType::class);
        $filterForm->handleRequest($request);
        $search = $this->collectSearchData($filterForm);

        return $this->render('user/admin/user/index.html.twig', [
            "search" => $search,
            "filter_form" => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add("enabled");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "User created successfully");

            return $this->redirectToRoute("user_index");
        }

        return $this->render('user/admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->add("enabled");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "User updated successfully");

            return $this->redirectToRoute("user_index");
        }

        return $this->render('user/admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Show user.
     *
     * @Route("/{id}/show", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        return $this->render('user/admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="user_delete", methods={"GET", "POST"})
     */
    public function delete(User $user, UserService $userService, UserOperationService $userOperationService): Response
    {
        $userOperationService->deleteUser($user, $userService->getUserName());
        $this->addFlash("success", "Deleted Successfully");

        return $this->redirectToRoute("user_index");
    }

    /**
     * @Route("/change-state/{id}", name="user_change_state", methods={"POST"})
     */
    public function changeState(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $user->setEnabled(!$user->isEnabled());
        $em->persist($user);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="user_datatable", methods={"GET"})
     */
    public function dataTable(Request $request, UserRepository $userRepository): Response
    {
        $srch = $request->query->all("search");
        $start = $request->query->getInt("start");
        $length = $request->query->getInt("length");
        $ordr = $request->query->all("order");

        $filterForm = $this->createForm(UserFilterType::class);
        $filterForm->handleRequest($request);
        $search = $this->collectSearchData($filterForm);
        if (Validate::not_null($srch['value'])) {
            $search->string = $srch['value'];
        }
        $search->ordr = $ordr[0];

        $count = $userRepository->filter($search, true);
        $users = $userRepository->filter($search, false, $start, $length);

        return $this->render("user/admin/user/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "users" => $users,
            )
        );
    }


    /**
     * Deletes a Merchant entity.
     *
     * @Route("/mass-delete", name="user_mass_delete", methods={"POST"})
     */
    public function massDelete(
        Request $request,
        UserRepository $userRepository,
        UserService $userService,
        UserOperationService $userOperationService
    ): Response {
        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        $userIds = $request->request->get('userIds');
        if (!is_array($userIds)) {
            return $this->json(['error' => 1, "message" => "Please enter select"]);
        }

        foreach ($userIds as $userId) {
            $user = $userRepository->find($userId);
            if ($user == null) {
                continue;
            }
            $userOperationService->deleteUser($user, $userService->getUserName());
        }

        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/login-as/{id}", requirements={"id" = "\d+"}, name="user_login_as_user", methods={"GET"})
     */
    public function loginAsUser(
        Request $request,
        User $user,
        UserAuthenticatorInterface $userAuthenticator,
        CustomAuthenticator $authenticator
    ): Response {

        $this->denyAccessUnlessGranted(UserInterface::ROLE_ADMIN);

        if (!$user->isEnabled()) {
            $this->addFlash('error', "this user is blocked, so you can't login with this account");

            return $this->redirect($request->headers->get('referer'));
        }
        $session = $request->getSession();
        $currentUser = $this->getUser();
        if ($currentUser instanceof User) {
            $currentUserId = $currentUser->getId();
            $session->set("lastLoginAsAdminId", $currentUserId);
        }

        $userAuthenticator->authenticateUser($user, $authenticator, $request);

        if ($this->isGranted(User::ROLE_SUPER_ADMIN)) {
            return $this->redirectToRoute('dashboard');
        } elseif ($this->isGranted(User::ROLE_ADMIN)) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->redirectToRoute('fe_home');
    }

    private function collectSearchData(FormInterface $form): \stdClass
    {
        $search = new \stdClass;
        $search->deleted = 0;
        $search->string = $form->get("str")->getData();
        $search->regDateFrom = ($form->get("createdFrom")->getData()) ? $form->get("createdFrom")->getData()->format('d/m/Y') : null;
        $search->regDateTo = ($form->get("createdTo")->getData()) ? $form->get("createdTo")->getData()->format('d/m/Y') : null;
        $search->enabled = $form->get("enabled")->getData();
        $search->subscriptionNewsletter = $form->get("subscriptionNewsletter")->getData();
        $search->role = User::ROLE_DEFAULT;

        return $search;
    }
}