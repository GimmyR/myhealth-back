<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    #[Route('/', name: 'home_index')]
    public function index(RequestStack $requestStack, AccountRepository $accountRep) : Response {

        $session = $requestStack->getSession();
        $account = $session->get('account');
        $twigPage = 'home/sign-in.html.twig';
        $models = [];

        if($account != false) {
            $twigPage = 'home/home.html.twig';
            $models = [
                'account' => $account
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

    #[Route('/sign-out', name: 'home_sign_out')]
    public function signOut(RequestStack $requestStack): Response {

        $session = $requestStack->getSession();
        $session->remove('account');

        return $this->redirectToRoute('home_index');

    }

}

?>