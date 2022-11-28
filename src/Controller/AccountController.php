<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\RepositoryException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController {

    #[Route("/api/account-edit", name: "account_edit_api")]
    public function edit(RequestStack $reqStack, AccountRepository $accountRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];
        $session = $reqStack->getSession();
        $account = $session->get("account");
        if($account == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {
            $reqData = json_decode($reqStack->getCurrentRequest()->getContent());
            if($reqData == null) {
                $model["status"] = -2;
                $model["message"] = "Données passées invalides !";
            } else {
                try {
                    $editedAccount = $accountRep->editAccount($account, $reqData);
                    $editedAccount->hidePassword();
                    $session->set("account", $editedAccount);
                    $model["account"] = $editedAccount;
                } catch(RepositoryException $e) {
                    $model["status"] = -3;
                    $model["message"] = $e->getMessage();
                }
            }
        } return $this->json($model);

    }

    #[Route("/api/forgotten-password", name: "account_forgotten_password_api")]
    public function forgottenPassword(RequestStack $reqStack, 
                                        AccountRepository $accountRep,
                                            MailerInterface $mailer): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $reqData = json_decode($reqStack->getCurrentRequest()->getContent());
        if($reqData != null && isset($reqData->email) && $reqData->email != null) {

            try {

                $account = $accountRep->findByEmail($reqData->email);
                $code = bin2hex(random_bytes(5));
                $email = (new Email())
                            ->from("myhealth068@gmail.com")
                            ->to($account->getEmail())
                            ->subject("Vérification de compte")
                            ->text("Code de vérification")
                            ->html("<p><code>". $code ."</code></p>");
                $mailer->send($email);
                $session = $reqStack->getSession();
                $session->set("account", $account);
                $session->set("code", $code);

            } catch(RepositoryException $e) {
                $model["status"] = -2;
                $model["message"] = $e->getMessage();
            } catch(Exception $e) {
                $model["status"] = -3;
                $model["message"] = $e->getMessage();
            }

        } else {
            $model["status"] = -1;
            $model["message"] = "Veuillez renseigner votre adresse email !";
        } return $this->json($model);

    }

    #[Route("/api/forgotten-password/get", name: "forgotten_password_get_api")]
    public function forgottenPasswordGet(RequestStack $reqStack): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $reqStack->getSession();
        $account = $session->get("account");
        $code = $session->get("code");

        if($account == null || $code == null) {

            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";

        } return $this->json($model);

    }

    #[Route("/api/forgotten-password/post", name: "forgotten_password_post_api")]
    public function forgottenPasswordPost(RequestStack $reqStack, AccountRepository $accountRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $reqStack->getSession();
        $account = $session->get("account");
        $code = $session->get("code");
        if($account == null || $code == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {

            $reqData = json_decode($reqStack->getCurrentRequest()->getContent());
            if($reqData != null && isset($reqData->password) && isset($reqData->code)
                    && $reqData->password != null && $reqData->code != null) {
                
                try {
                    $account = $accountRep->editAccountByCode(
                        $account->getEmail(), 
                        $reqData->code, 
                        $code, 
                        $reqData->password
                    );
                    
                    $session->set("account", $account);
                    $session->remove("code");
                } catch(RepositoryException $e) {
                    $model["status"] = -3;
                    $model["message"] = $e->getMessage();
                }

            } else {
                $model["status"] = -2;
                $model["message"] = "Données passées invalides !";
            }

        } return $this->json($model);

    }

}

?>