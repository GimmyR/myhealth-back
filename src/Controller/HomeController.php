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

        if(!$account)
            return $this->redirectToRoute('sign_in');
        else
            return $this->render('home/home.html.twig', []);

    }

    #[Route('/sign-in', name: 'sign_in')]
    public function signIn(): Response {

        return $this->render('home/sign-in.html.twig', []);

    }

}

?>