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

    #[Route('/api/home-index', name: 'home_index_API')]
    public function index_API(RequestStack $requestStack, OversightRepository $oversightRep) : JsonResponse {

        $session = $requestStack->getSession();
        $account = $session->get('account');
        $model = [
            'status' => -1,
            'message' => 'Vous n\'êtes pas authentifié !'
        ];

        if($account != null) {
            $oversights = $oversightRep->findAllByAccountId($account->getId());
            $model = [
                'status' => 0,
                'message' => null,
                'oversights' => $oversights
            ];
        } return $this->json($model);

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