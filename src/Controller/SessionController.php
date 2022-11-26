<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController {

    #[Route("/api/session-check", name: "session_check_api")]
    public function check(RequestStack $reqStack): JsonResponse {

        $model = [ "status" => 0, "message" => null ];
        $session = $reqStack->getSession();
        $account = $session->get("account");
        if($account == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {
            $model["account"] = $account;
        } return $this->json($model);

    }

}

?>