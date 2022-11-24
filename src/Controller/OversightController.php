<?php

namespace App\Controller;

use App\Entity\EntityException;
use App\Entity\Oversight;
use App\Entity\Parameter;
use App\Repository\EntryDetailRepository;
use App\Repository\OversightEntryRepository;
use App\Repository\OversightRepository;
use App\Repository\ParameterRepository;
use App\Repository\RepositoryException;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/oversight/{oversightId}', name: 'oversight_show_API')]
    public function show_API(int $oversightId, 
                            RequestStack $requestStack,
                                OversightRepository $oversightRep, 
                                    ParameterRepository $parameterRep,
                                        OversightEntryRepository $entryRep,
                                            EntryDetailRepository $entryDetailRep): JsonResponse {

        $model = [ 
            'status' => -1,
            'message' => 'Problème inconnu ! Appelez un administrateur !'
        ];

        $session = $requestStack->getSession();
        $account = $session->get('account');

        if($account == false) {
            $model["status"] = -2;
            $model["Vous n'êtes pas authentifié !"];
        } else $model = $this->getModel(
            $model, 
            $account->getId(),
            $oversightId, 
            $oversightRep, 
            $parameterRep, 
            $entryRep, 
            $entryDetailRep
        ); return $this->json($model);

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
            $model['message'] = null;

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
                'id' => $entry['id'],
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

    #[Route('/api/create-oversight/get', name: 'oversight_create_get_api')]
    public function create_GET_API(RequestStack $reqStack, OversightRepository $oversightRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $reqStack->getSession();
        $account = $session->get('account');

        if($account == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } return $this->json($model);

    }

    #[Route('/api/create-oversight/post', name: 'oversight_create_post_api')]
    public function create_POST_API(RequestStack $reqStack, OversightRepository $oversightRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $reqStack->getSession();
        $account = $session->get('account');

        if($account == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
            return $this->json($model);
        } else {

            try {
            
                $content = $reqStack->getCurrentRequest()->getContent();
                $reqData = json_decode($content);
                if($content != null && $reqData != null && $reqData->date != null && $reqData->title != null) {

                    $oversight = new Oversight($account->getId(), $reqData->date, $reqData->title, 1);
                    $oversight->validate();
                    $parameters = [];
                    foreach($reqData->parameters as $parameter) {
                        if($parameter->name != null) {
                            $param = new Parameter(0, $parameter->name, $parameter->unit, 1);
                            $param->validate();
                            $parameters[] = $param;
                        } else throw new ControllerException("Veuillez bien remplir les formulaires, s'il vous plaît !");
                    } $oversightRep->create($oversight, $parameters);

                } else {

                    $model["status"] = -2;
                    $model["message"] = "Veuillez bien remplir les formulaires, s'il vous plaît !";

                }

            } catch(ControllerException | RepositoryException | EntityException $e) {

                $model["status"] = -3;
                $model["message"] = $e->getMessage();

            } catch(Exception $e) {

                $model["status"] = -4;
                $model["message"] = "Appelez un administrateur !";
                $model["technical-issues"] = $e->getMessage();

            } finally {

                return $this->json($model);

            }

        }

    }

}

?>