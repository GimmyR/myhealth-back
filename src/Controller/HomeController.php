<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\OversightRepository;
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

    #[Route('/sign-in', name: 'home_sign_in')]
    public function signIn(RequestStack $requestStack, AccountRepository $accountRep): Response {

        $email = $requestStack->getCurrentRequest()->request->get('email');
        $password = $requestStack->getCurrentRequest()->request->get('password');
        $account = $accountRep->checkAccount($email, $password);
        
        if($account) {

            $session = $requestStack->getSession();
            $session->set('account', $account[0]);

        } return $this->redirectToRoute('home_index', []);

    }

    #[Route('/api/sign-in', name: 'home_sign_in_API')]
    public function signIn_API(RequestStack $requestStack, AccountRepository $accountRep): JsonResponse {

        $model = [ "status" => -1, "message" => "Problème inconnu !" ];
        // TRUE ici permet à la fonction de retourner un tableau associatif et non un objet
        $data = json_decode($requestStack->getCurrentRequest()->getContent(), true);
        $account = $accountRep->checkAccount($data["email"], $data["password"]);
        
        if($account) {

            $session = $requestStack->getSession();
            $token = bin2hex(random_bytes(10));
            while($session->get($token) != null)
                $token = bin2hex(random_bytes(10));
            
            $model["token"] = $token;
            $session->set($model["token"], $account[0]);
            $model["test"] = $session->get($model["token"]);
            $model["status"] = 0;
            $model["message"] = null;

        } return $this->json($model);

    }

    #[Route('/sign-out', name: 'home_sign_out')]
    public function signOut(RequestStack $requestStack): Response {

        $session = $requestStack->getSession();
        $session->remove('account');

        return $this->redirectToRoute('home_index');

    }

    #[Route('/api/sign-out', name: 'home_sign_out_API')]
    public function signOut_API(RequestStack $requestStack): JsonResponse {

        $model = [ "status" => -1, "message" => "Problème inconnu !" ];
        $data = json_decode($requestStack->getCurrentRequest()->getContent(), true);
        if($data != null) {
            $session = $requestStack->getSession();
            $account = $session->remove($data["token"]);
            $model["test"] = $account;
            if($account != null) {
                $model["status"] = 0;
                $model["message"] = null;
            }
        } return $this->json($model);

    }

}

?>