<?php

namespace App\Controller;

use App\Repository\EntryDetailRepository;
use App\Repository\OversightEntryRepository;
use App\Repository\OversightRepository;
use App\Repository\ParameterRepository;
use App\Repository\RepositoryException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OversightController extends AbstractController {

    #[Route('/oversight/{oversightId}', name: 'oversight_show')]
    public function show(int $oversightId, 
                            RequestStack $requestStack,
                                OversightRepository $oversightRep, 
                                    ParameterRepository $parameterRep,
                                        OversightEntryRepository $entryRep,
                                            EntryDetailRepository $entryDetailRep): Response {

        $model = [ 
            'status' => -1,
            'message' => 'Problème inconnu ! Appelez un administrateur !'
        ];

        $session = $requestStack->getSession();
        $account = $session->get('account');

        if($account == false)
            return $this->redirectToRoute('home_index');
        else {
            return $this->render(
                'oversight/oversight.html.twig', 
                $this->getModel(
                    $model, 
                    $account->getId(),
                    $oversightId, 
                    $oversightRep, 
                    $parameterRep, 
                    $entryRep, 
                    $entryDetailRep
                )
            );
        }

    }

    protected function getModel(array $model, 
                                    int $accountId,
                                        int $oversightId, 
                                            OversightRepository $oversightRep, 
                                                ParameterRepository $parameterRep,
                                                    OversightEntryRepository $entryRep,
                                                        EntryDetailRepository $entryDetailRep) {

        try {

            $model['oversight'] = $oversightRep->findByIdAndAccountId($oversightId, $accountId);
            $model['parameters'] = $this->getParameters($oversightId, $parameterRep);
            $entryDetails = $this->getEntryDetailsByParameters($model['parameters'], $entryDetailRep);
            $model['chartDatas'] = $this->getChartDatas($model['parameters'], $entryDetails);
            $entries = $this->getOversightEntries($oversightId, $entryRep);
            $model['entryDetails'] = $this->getEntryDetailsByEntries($oversightId, $entries, $entryDetailRep);
            $model['status'] = 0;

        } catch(ControllerException | RepositoryException $e) {

            $model['message'] = $e->getMessage();

        } finally {

            return $model;

        }

    }

    protected function getParameters(int $oversightId, ParameterRepository $parameterRep) {

        $parameters = $parameterRep->findAllByOversightId($oversightId);

        if(!$parameters)
            throw new ControllerException("Aucun paramètre n'est associé à cette surveillance !");
        else
            return $parameters;

    }

    protected function getEntryDetailsByParameters(array $parameters, EntryDetailRepository $entryDetailRep): array {

        $entryDetails = [];
        
        foreach($parameters as $parameter) 
            $entryDetails[$parameter['name']] = $entryDetailRep->findAllByParameterId($parameter['id']);

        if(!$entryDetails)
            throw new ControllerException("Détails introuvables (1) !");
        else
            return $entryDetails;

    }

    protected function getOversightEntries(int $oversightId, OversightEntryRepository $entryRep) {

        $entries = $entryRep->findAllByOversightId($oversightId);

        if(!$entries)
            throw new ControllerException("Entrées introuvables !");
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
            throw new ControllerException("Détails introuvables (2) !");
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