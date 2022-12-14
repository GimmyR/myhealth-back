<?php

namespace App\Controller;

use App\Entity\Account;
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
                $emailFrom = $this->getParameter("app.email_from");
                $email = (new Email())
                            ->from($emailFrom)
                            ->to($account->getEmail())
                            ->subject("Vérification de compte")
                            ->html("Code de vérification : <p><code>". $code ."</code></p>");
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

    #[Route("/api/create-account", name: "account_create_account_api")]
    public function createAccount(RequestStack $reqStack, 
                                    AccountRepository $accountRep,
                                        MailerInterface $mailer): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        try {

            $reqData = json_decode($reqStack->getCurrentRequest()->getContent());
            if($reqData == null || !isset($reqData->firstname) || !isset($reqData->lastname) ||
                !isset($reqData->email) || !isset($reqData->password))
                throw new ControllerException("Veuillez bien remplir les formulaires !");
            
            $account = new Account;
            $account->setFirstname($reqData->firstname);
            $account->setLastname($reqData->lastname);
            $account->setEmail($reqData->email);
            $account->setPassword($reqData->password);
            $account->setStatus(0);
            $account = $accountRep->createAccount($account);

            $session = $reqStack->getSession();
            $session->set("account", $account);
            $confirm = bin2hex(random_bytes(5));
            $session->set("confirm", $confirm);
            $emailFrom = $this->getParameter("app.email_from");
            $frontURL = $this->getParameter("app.front_url");
            
            $email = (new Email())
                        ->from($emailFrom)
                        ->to($account->getEmail())
                        ->subject("Confirmation de compte")
                        ->html('
                            Cliquer pour confirmer votre compte : 
                            <a href="' .$frontURL. 'confirm-account/' .$confirm. '">Confirmer votre compte</a>
                        ');
            $mailer->send($email);

        } catch(RepositoryException $e) {

            $model["status"] = -1;
            $model["message"] = $e->getMessage();

        } catch(Exception $e) {

            $model["status"] = -2;
            $model["message"] = $e->getMessage();

        } return $this->json($model);

    }

    #[Route("/api/confirm-account/{confirm}")]
    public function confirmAccount(string $confirm, 
                                        RequestStack $reqStack,
                                            AccountRepository $accountRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        try {

            $session = $reqStack->getSession();
            $account = $session->get("account");
            if($account == null)
                throw new ControllerException("Vous n'êtes pas authentifié !");

            if($session->get("confirm") == $confirm) {
                $account = $accountRep->confirmAccount($account);
                $session->set("account", $account);
                $session->remove("confirm");
            } else throw new ControllerException("Code de confirmation erroné !");

        } catch(ControllerException $e) {

            $model["status"] = -1;
            $model["message"] = $e->getMessage();

        } catch(RepositoryException $e) {

            $model["status"] = -2;
            $model["message"] = $e->getMessage();

        } catch(Exception $e) {

            $model["status"] = -3;
            $model["message"] = $e->getMessage();

        } return $this->json($model);

    }

}

?>