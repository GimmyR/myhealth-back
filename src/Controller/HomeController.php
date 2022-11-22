<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\OversightRepository;
use App\Repository\RepositoryException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    #[Route('/', name: 'home_index')]
    public function index(RequestStack $requestStack, OversightRepository $oversightRep) : Response {

        $session = $requestStack->getSession();
        $account = $session->get('account');
        $twigPage = 'home/sign-in.html.twig';
        $models = [
            'status' => -1,
            'message' => 'Vous n\'êtes pas connecté !'
        ];

        if($account != false) {
            $oversights = $oversightRep->findAllByAccountId($account->getId());
            $twigPage = 'home/home.html.twig';
            $models = [
                'status' => 0,
                'message' => null,
                'account' => $account,
                'oversights' => $oversights
            ];
        } return $this->render($twigPage, $models);

    }

    #[Route('/api/home-index', name: 'home_index_API')]
    public function index_API(RequestStack $requestStack, OversightRepository $oversightRep) : JsonResponse {

        $session = $requestStack->getSession();
        $account = $session->get('account');
        $model = [
            'status' => -1,
            'message' => 'Vous n\'êtes pas authentifié !'
        ];

        if($account != false) {
            $oversights = $oversightRep->findAllByAccountId($account->getId());
            $model = [
                'status' => 0,
                'message' => null,
                'account' => $account,
                'oversights' => $oversights
            ];
        } return $this->json($model);

    }

    #[Route('/sign-in', name: 'home_sign_in')]
    public function signIn(RequestStack $requestStack, AccountRepository $accountRep): Response {

        $model = [ "status" => 0, "message" => null ];
        $email = $requestStack->getCurrentRequest()->request->get('email');
        $password = $requestStack->getCurrentRequest()->request->get('password');

        try {

            $account = $accountRep->checkAccount($email, $password);
            $session = $requestStack->getSession();
            $session->set('account', $account);
            return $this->redirectToRoute('home_index', $model);
        
        } catch(RepositoryException $e) {

            $model["status"] = -1;
            $model["message"] = $e->getMessage();
            return $this->render("home/sign-in.html.twig", $model);

        }

    }

    #[Route('/api/sign-in', name: 'home_sign_in_API')]
    public function signIn_API(RequestStack $requestStack, AccountRepository $accountRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        try {

            $requestContent = json_decode($requestStack->getCurrentRequest()->getContent());
            $account = $accountRep->checkAccount($requestContent->email, $requestContent->password);
            $session = $requestStack->getSession();
            $session->set("account", $account);

        } catch(RepositoryException $e) {

            $model["status"] = -1;
            $model["message"] = $e->getMessage();

        } finally {

            return $this->json($model);

        }

    }

    #[Route('/sign-out', name: 'home_sign_out')]
    public function signOut(RequestStack $requestStack): Response {

        $session = $requestStack->getSession();
        $session->remove('account');

        return $this->redirectToRoute('home_index');

    }

    #[Route('/api/sign-out', name: 'home_sign_out_API')]
    public function signOut_API(RequestStack $requestStack): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $requestStack->getSession();
        if($session->remove('account') == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } return $this->json($model);

    }

}

?>