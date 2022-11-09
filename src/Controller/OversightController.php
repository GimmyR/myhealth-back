<?php

namespace App\Controller;

use App\Entity\Oversight;
use App\Repository\OversightRepository;
use App\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OversightController extends AbstractController {

    #[Route('/oversight/{oversightId}', name: 'oversight_show')]
    public function show(int $oversightId, OversightRepository $oversightRep, ParameterRepository $parameterRep): Response {

        return $this->render(
            'oversight/oversight.html.twig', 
            $this->getModel($oversightId, $oversightRep, $parameterRep)
        );

    }

    protected function getModel(int $oversightId, OversightRepository $oversightRep, ParameterRepository $parameterRep) {

        $model = [ 
            'status' => -1,
            'message' => 'Unknown Error ! Call an administrator !'
        ];

        try {

            $model['oversight'] = $this->getOversight($oversightId, $oversightRep);
            $model['parameters'] = $this->getParameters($oversightId, $parameterRep);
            $model['status'] = 0;

        } catch(ControllerException $e1) {

            $model['message'] = $e1->message;

        } finally {

            return $model;

        }

    }

    protected function getOversight(int $oversightId, OversightRepository $oversightRep): Oversight {

        $oversight = $oversightRep->findById($oversightId);

        if(!$oversight)
            throw new ControllerException("Oversight not found !");
        else
            return $oversight;

    }

    protected function getParameters(int $oversightId, ParameterRepository $parameterRep): array {

        $parameters = $parameterRep->findAllByOversightId($oversightId);

        if(!$parameters)
            throw new ControllerException("Parameters not found !");
        else
            return $parameters;

    }

}

?>