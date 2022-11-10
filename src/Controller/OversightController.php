<?php

namespace App\Controller;

use App\Entity\Oversight;
use App\Repository\EntryDetailRepository;
use App\Repository\OversightRepository;
use App\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OversightController extends AbstractController {

    #[Route('/oversight/{oversightId}', name: 'oversight_show')]
    public function show(int $oversightId, 
                            OversightRepository $oversightRep, 
                                ParameterRepository $parameterRep,
                                    EntryDetailRepository $entryDetailRep): Response {

        $model = [ 
            'status' => -1,
            'message' => 'Unknown Error ! Call an administrator !'
        ];
        
        return $this->render(
            'oversight/oversight.html.twig', 
            $this->getModel($model, $oversightId, $oversightRep, $parameterRep, $entryDetailRep)
        );

    }

    protected function getModel(array $model, int $oversightId, 
                                    OversightRepository $oversightRep, 
                                        ParameterRepository $parameterRep,
                                            EntryDetailRepository $entryDetailRep) {

        try {

            $model['oversight'] = $this->getOversight($oversightId, $oversightRep);
            $parameters = $this->getParameters($oversightId, $parameterRep);
            $entryDetails = $this->getEntryDetails($oversightId, $parameters, $entryDetailRep);
            $model['chartDatas'] = $this->getChartDatas($parameters, $entryDetails);
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

    protected function getEntryDetails(int $oversightId, array $parameters, EntryDetailRepository $entryDetailRep): array {

        $entryDetails = [];
        
        foreach($parameters as $parameter) 
            $entryDetails[$parameter->getName()] = $entryDetailRep->findAllByParameterId($parameter->getId());

        if(!$entryDetails)
            throw new ControllerException("Entry Details not found !");
        else
            return $entryDetails;

    }

    protected function getChartDatas(array $parameters, array $entryDetails): array {

        $chartDatas = [];

        foreach($parameters as $parameter) {

            $labels = [];
            $data = [];

            foreach($entryDetails[$parameter->getName()] as $entry) {

                $labels[] = $entry['date'];
                $data[] = $entry['value'];

            } $chartDatas[] = [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $parameter->getName(),
                    'data' => $data
                ]]
            ];

        } return $chartDatas;

    }

}

?>