<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController {

    #[Route("/api/session-check", name: "session_check_api")]
    public function check(RequestStack $reqStack): JsonResponse {

        $model = [
            "status" => 0,
            "message" => null
        ];

        $session = $reqStack->getSession();
        $model["account"] = $session->get("account");

        return $this->json($model);

    }

}

?>