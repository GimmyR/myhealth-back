<?php

namespace App\Controller;

use App\Entity\Oversight;
use App\Repository\EntryDetailRepository;
use App\Repository\OversightEntryRepository;
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
                                    OversightEntryRepository $entryRep,
                                        EntryDetailRepository $entryDetailRep): Response {

        $model = [ 
            'status' => -1,
            'message' => 'Unknown Error ! Call an administrator !'
        ];
        
        return $this->render(
            'oversight/oversight.html.twig', 
            $this->getModel($model, $oversightId, $oversightRep, $parameterRep, $entryRep, $entryDetailRep)
        );

    }

    protected function getModel(array $model, int $oversightId, 
                                    OversightRepository $oversightRep, 
                                        ParameterRepository $parameterRep,
                                            OversightEntryRepository $entryRep,
                                                EntryDetailRepository $entryDetailRep) {

        try {

            $model['oversight'] = $this->getOversight($oversightId, $oversightRep);
            $model['parameters'] = $this->getParameters($oversightId, $parameterRep);
            $entryDetails = $this->getEntryDetailsByParameters($oversightId, $model['parameters'], $entryDetailRep);
            $model['chartDatas'] = $this->getChartDatas($model['parameters'], $entryDetails);
            $entries = $this->getOversightEntries($oversightId, $entryRep);
            $model['entryDetails'] = $this->getEntryDetailsByEntries($oversightId, $entries, $entryDetailRep);
            $model['status'] = 0;

        } catch(ControllerException $e1) {

            $model['message'] = $e1->message;

        } finally {

            return $model;

        }

    }

    protected function getOversight(int $oversightId, OversightRepository $oversightRep) {

        $oversight = $oversightRep->findById($oversightId);

        if(!$oversight)
            throw new ControllerException("Oversight not found !");
        else
            return $oversight;

    }

    protected function getParameters(int $oversightId, ParameterRepository $parameterRep) {

        $parameters = $parameterRep->findAllByOversightId($oversightId);

        if(!$parameters)
            throw new ControllerException("Parameters not found !");
        else
            return $parameters;

    }

    protected function getEntryDetailsByParameters(int $oversightId, array $parameters, EntryDetailRepository $entryDetailRep): array {

        $entryDetails = [];
        
        foreach($parameters as $parameter) 
            $entryDetails[$parameter['name']] = $entryDetailRep->findAllByParameterId($parameter['id']);

        if(!$entryDetails)
            throw new ControllerException("Entry Details not found !");
        else
            return $entryDetails;

    }

    protected function getOversightEntries(int $oversightId, OversightEntryRepository $entryRep) {

        $entries = $entryRep->findAllByOversightId($oversightId);

        if(!$entries)
            throw new ControllerException("Parameters not found !");
        else
            return $entries;

    }

    protected function getEntryDetailsByEntries(int $oversightId, $entries, EntryDetailRepository $entryDetailRep): array {

        $entryDetails = [];
        
        foreach($entries as $entry) 
            $entryDetails[] = [
                'date' => $entry['date'],
                'data' => $entryDetailRep->findAllByEntryId($entry['id'])
            ];

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

            foreach($entryDetails[$parameter['name']] as $entry) {

                $labels[] = $entry['date'];
                $data[] = $entry['value'];

            } $chartDatas[] = [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $parameter['name'],
                    'data' => $data,
                    'fill' => false,
                    'borderColor' => 'rgb(79, 55, 216)',
                    'tension' => 0.1
                ]]
            ];

        } return $chartDatas;

    }

}

?>