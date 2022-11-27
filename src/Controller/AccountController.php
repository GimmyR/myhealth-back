<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\RepositoryException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
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

}

?>