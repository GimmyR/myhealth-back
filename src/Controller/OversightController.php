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
            'status' => 0,
            'message' => null
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
            'status' => 0,
            'message' => null
        ];

        $session = $requestStack->getSession();
        $account = $session->get('account');

        if($account == false) {
            $model["status"] = -1;
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

        } catch(Exception $e) {

            $model['status'] = -2;
            $model['message'] = $e->getMessage();

        } finally {

            return $model;

        }

    }

    protected function getParameters(int $oversightId, ParameterRepository $parameterRep) {

        $parameters = $parameterRep->findAllByOversightId($oversightId);

        if(!$parameters)
            return [];
        else
            return $parameters;

    }

    protected function getEntryDetailsByParameters(array $parameters, EntryDetailRepository $entryDetailRep): array {

        $entryDetails = [];
        
        foreach($parameters as $parameter) {
            $detail = $entryDetailRep->findAllByParameterId($parameter['id']);
            if(!$detail)
                $entryDetails[$parameter['name']] = [];
            else $entryDetails[$parameter['name']] = $detail;
        } return $entryDetails;

    }

    protected function getOversightEntries(int $oversightId, OversightEntryRepository $entryRep) {

        $entries = $entryRep->findAllByOversightId($oversightId);

        if(!$entries)
            return [];
        else
            return $entries;

    }

    protected function getEntryDetailsByEntries(int $oversightId, $entries, EntryDetailRepository $entryDetailRep): array {

        $entryDetails = [];
        
        foreach($entries as $entry) {
            $data = $entryDetailRep->findAllByEntryId($entry['id']);
            if(!$data) {
                $entryDetails[] = [
                    'id' => $entry['id'],
                    'date' => $entry['date'],
                    'data' => []
                ];
            } else {
                $entryDetails[] = [
                    'id' => $entry['id'],
                    'date' => $entry['date'],
                    'data' => $data
                ];
            }
        } return $entryDetails;

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
    public function create_GET_API(RequestStack $reqStack): JsonResponse {

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

    #[Route('/api/edit-oversight/get/{id}', name: 'oversight_edit_get_api')]
    public function edit_GET_API(int $id, 
                                    RequestStack $reqStack, 
                                    OversightRepository $oversightRep,
                                    ParameterRepository $parameterRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $reqStack->getSession();
        $account = $session->get('account');

        if($account == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {
            try {
                $model["oversight"] = $oversightRep->findByIdAndAccountId($id, $account->getId());
                $model["parameters"] = $parameterRep->findAllByOversightId($model["oversight"]->getId());
            } catch(RepositoryException $e) {
                $model["status"] = -2;
                $model["message"] = $e->getMessage();
            }
        } return $this->json($model);

    }

    #[Route('/api/edit-oversight/post/', name: 'oversight_edit_post_api')]
    public function edit_POST_API(RequestStack $reqStack, OversightRepository $oversightRep): JsonResponse {

        $model = [ "status" => 0, "message" => null ];

        $session = $reqStack->getSession();
        $account = $session->get('account');

        if($account == null) {
            $model["status"] = -1;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {

            try {
            
                $content = $reqStack->getCurrentRequest()->getContent();
                $reqData = json_decode($content);
                if($content != null && $reqData != null && $reqData->id != null && $reqData->date != null && $reqData->title != null) {

                    $oversight = new Oversight($account->getId(), $reqData->date, $reqData->title, 1);
                    $oversight->setId($reqData->id);
                    $oversight->validate();
                    $parameters = [];
                    foreach($reqData->parameters as $parameter) {
                        if($parameter->name != null) {
                            $param = new Parameter($oversight->getId(), $parameter->name, $parameter->unit, 1);
                            $param->setId($parameter->id);
                            $param->validate();
                            $parameters[] = $param;
                        } else throw new ControllerException("Veuillez bien remplir les formulaires, s'il vous plaît !");
                    } $oversightRep->edit($account->getId(), $oversight, $parameters);

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

            }

        } return $this->json($model);

    }

}

?>