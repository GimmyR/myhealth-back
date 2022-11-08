<?php

namespace App\Controller;

use App\Repository\OversightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OversightController extends AbstractController {

    #[Route('/oversight/{oversightId}', name: 'oversight_show')]
    public function show(int $oversightId, OversightRepository $oversightRep): Response {

        $oversight = $oversightRep->find($oversightId);

        return $this->render('oversight/oversight.html.twig', [
            'oversight' => $oversight
        ]);

    }

}

?>