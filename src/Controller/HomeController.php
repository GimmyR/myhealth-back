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
        $twigPage = 'home/home.html.twig';

        if(!$account)
            $twigPage = 'home/sign-in.html.twig';

        return $this->render($twigPage, []);

    }

    #[Route('/sign-in', name: 'home_sign_in')]
    public function signIn(RequestStack $requestStack, AccountRepository $accountRep): Response {

        $email = $requestStack->getCurrentRequest()->request->get('email');
        $password = $requestStack->getCurrentRequest()->request->get('password');
        $account = $accountRep->checkAccount($email, $password);
        
        if($account) {

            $session = $requestStack->getSession();
            $session->set('account', $account);

        } return $this->redirectToRoute('home_index');

    }

}

?>