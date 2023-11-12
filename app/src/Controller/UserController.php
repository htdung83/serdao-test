<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userService
    ) {
    }

    #[Route('/users', methods: ['GET'])]
    public function index(Request $request)
    {
        $users = $this->userService->search();

        $errors = $request->getSession()->get('errors');

        return $this->render('user.html.twig', [
            'users' => $users,
            'errors' => $errors
        ]);
    }

    #[Route('/user', methods: ['POST'])]
    public function store(Request $request)
    {
        $session = $request->getSession();

        $errors = $this->validateRequiredData($request, [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'address' => 'Address',
        ]);

        if (count($errors) > 0) {
            $session->set('errors', $errors);

            return $this->redirect(
                $this->generateUrl('app_user_index'),
                301
            );
        } else {
            $session->remove('errors');
        }

        $this->userService->save(
            $request->get('firstName'),
            $request->get('lastName'),
            $request->get('address')
        );

        return $this->redirect('/users');
    }

    #[Route('/user/{id}/delete', methods: ['POST'])]
    public function delete($id)
    {
        $this->userService->delete($id);

        return $this->redirectToRoute('app_user_index');
    }

    public function validateRequiredData(Request $request, array $attributes): array
    {
        $errors = [];

        $queries = array_merge(
            $request->request->all(),
            $request->attributes->get('_route_params')
        );

        foreach ($attributes as $attribute => $name) {
            if (empty($queries[$attribute])) {
                $errors[$attribute] = $name . ' is required.';
            } else {
                unset($errors[$attribute]);
            }
        }

        return $errors;
    }
}