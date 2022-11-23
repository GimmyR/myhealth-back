<?php

namespace App\Controller;

use App\Entity\EntryDetail;
use App\Entity\OversightEntry;
use App\Repository\EntryDetailRepository;
use App\Repository\OversightEntryRepository;
use App\Repository\OversightRepository;
use App\Repository\ParameterRepository;
use App\Repository\RepositoryException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OversightEntryController extends AbstractController {

    #[Route('/oversight-entry/{oversightId}', name: 'oversight_entry_index')]
    public function index(int $oversightId,
                            RequestStack $requestStack, 
                            OversightRepository $oversightRep,
                            ParameterRepository $parameterRep): Response {

        $model = [ 'status' => 0, 'message' => null ];

        $session = $requestStack->getSession();
        $account = $session->get('account');
        if($account == null)
            return $this->redirectToRoute('home_index');
        else {
            try {
                $model['oversight'] = $oversightRep->findByIdAndAccountId($oversightId, $account->getId());
                $model['parameters'] = $parameterRep->findAllByOversightId($oversightId);
            } catch(RepositoryException $e) {
                $model['status'] = -1;
                $model['message'] = $e->getMessage();
            } finally {
                return $this->render('oversight-entry/add.html.twig', $model);
            }
        }

    }

    #[Route('/api/oversight-entry/{oversightId}', name: 'oversight_entry_index_API')]
    public function index_API(int $oversightId,
                            RequestStack $requestStack, 
                            OversightRepository $oversightRep,
                            ParameterRepository $parameterRep): JsonResponse {

        $model = [ 'status' => 0, 'message' => null ];

        $session = $requestStack->getSession();
        $account = $session->get('account');
        if($account == null){
            $model["status"] = -2;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {
            try {
                $model['oversight'] = $oversightRep->findByIdAndAccountId($oversightId, $account->getId());
                $model['parameters'] = $parameterRep->findAllByOversightId($oversightId);
            } catch(RepositoryException $e) {
                $model['status'] = -1;
                $model['message'] = $e->getMessage();
            } finally {
                return $this->json($model);
            }
        }

    }

    #[Route('/oversight-entry/add/{oversightId}', name: 'oversight_entry_add')]
    public function add(int $oversightId, 
                            RequestStack $requestStack,
                            OversightRepository $oversightRep,
                            ParameterRepository $parameterRep,
                            OversightEntryRepository $oversightEntryRep): Response {

        $model = [ 'status' => 0, 'message' => null ];

        $session = $requestStack->getSession();
        $account = $session->get('account');
        if($account == null)
            return $this->redirectToRoute('home_index');
        else {

            try {

                $model['oversight'] = $oversightRep->findByIdAndAccountId($oversightId, $account->getId());
                $model['parameters'] = $parameterRep->findAllByOversightId($oversightId);
                $entry = new OversightEntry(
                    $oversightId,
                    $requestStack->getCurrentRequest()->request->get('date'),
                    1
                ); $entry->validate();

                $details = [];

                foreach($model['parameters'] as $parameter) {

                    $detail = new EntryDetail(
                        0, 
                        $parameter['id'], 
                        $requestStack->getCurrentRequest()->request->get('parameter-'. $parameter['id']),
                        1
                    ); $detail->validate();

                    $details[] = $detail;

                } $oversightEntryRep->add($entry, $details);

            } catch(RepositoryException $e) {

                $model['status'] = -1;
                $model['message'] = $e->getMessage();

            } finally {

                return $this->render('oversight-entry/add.html.twig', $model);

            }
        }

    }

    #[Route('/api/oversight-entry/add/{oversightId}', name: 'oversight_entry_add_API')]
    public function add_API(int $oversightId, 
                            RequestStack $requestStack,
                            OversightRepository $oversightRep,
                            ParameterRepository $parameterRep,
                            OversightEntryRepository $oversightEntryRep): Response {

        $model = [ 'status' => 0, 'message' => null ];

        $session = $requestStack->getSession();
        $account = $session->get('account');
        if($account == null) {
            $model["status"] = -2;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {

            try {

                $model['oversight'] = $oversightRep->findByIdAndAccountId($oversightId, $account->getId());
                $model['parameters'] = $parameterRep->findAllByOversightId($oversightId);
                $requestData = json_decode($requestStack->getCurrentRequest()->getContent());
                
                if($requestData != null) {

                    $entry = new OversightEntry(
                        $oversightId,
                        $requestData->date,
                        1
                    ); $entry->validate();

                    $details = [];

                    foreach($requestData->parameters as $parameter) {

                        $detail = new EntryDetail(
                            0, 
                            $parameter->id, 
                            $parameter->value,
                            1
                        ); $detail->validate();

                        $details[] = $detail;

                    } $oversightEntryRep->add($entry, $details);

                }

            } catch(RepositoryException $e) {

                $model['status'] = -1;
                $model['message'] = $e->getMessage();

            } finally {

                return $this->json($model);

            }
        }

    }

    #[Route('/oversight-entry/edit/{entryId}', name: 'oversight_entry_edit')]
    public function edit(int $entryId, 
                        RequestStack $requestStack,
                        OversightEntryRepository $oversightEntryRep,
                        OversightRepository $oversightRep,
                        ParameterRepository $parameterRep,
                        EntryDetailRepository $entryDetailRep): Response {

        $model = [ "status" => 0, "message" => null ];
        $session = $requestStack->getSession();
        $account = $session->get('account');
        if($account == null)
            return $this->redirectToRoute('home_index');
        else {

            try {
            
                $entry = $oversightEntryRep->findByIdAndAccountId($entryId, $account->getId());
                $oversight = $oversightRep->findByIdAndAccountId($entry['oversightId'], $account->getId());
                $parameters = $parameterRep->findAllByOversightId($oversight->getId());
                $details = $entryDetailRep->findAllByEntryId($entryId);
                $date = $requestStack->getCurrentRequest()->request->get('date');

                if($date != null) {

                    $entry['date'] = $date;
                    for($i = 0; $i < count((array)$details); $i++) {
                        foreach($parameters as $parameter) {
                            $value = $requestStack->getCurrentRequest()->request->get('parameter-'. $parameter['id']);
                            if($value != null && $details[$i]['parameterId'] == $parameter['id'] && $details[$i]['value'] != $value) {
                                $details[$i]['value'] = $value;
                                break;
                            }
                        }
                    } $oversightEntryRep->edit($entry, $details);

                    return $this->redirectToRoute(
                        'oversight_show', 
                        [ 'oversightId' => $oversight->getId() ]
                    );

                } else {

                    $model['oversight'] = $oversight;
                    $model['entry'] = $entry;
                    $model['parameters'] = $parameters;
                    $model['details'] = $details;
                    return $this->render('oversight-entry/edit.html.twig', $model);

                }

            } catch(RepositoryException $e) {

                $model['status'] = -1;
                $model['message'] = $e->getMessage();
                return $this->render('error.html.twig', $model);

            }

        }

    }

    #[Route('/api/oversight-entry/edit/{entryId}', name: 'oversight_entry_edit_API')]
    public function edit_API(int $entryId, 
                        RequestStack $requestStack,
                        OversightEntryRepository $oversightEntryRep,
                        OversightRepository $oversightRep,
                        ParameterRepository $parameterRep,
                        EntryDetailRepository $entryDetailRep): Response {

        $model = [ "status" => 0, "message" => null ];
        $session = $requestStack->getSession();
        $account = $session->get('account');
        if($account == null) {
            $model["status"] = -2;
            $model["message"] = "Vous n'êtes pas authentifié !";
        } else {

            try {
            
                $entry = $oversightEntryRep->findByIdAndAccountId($entryId, $account->getId());
                $oversight = $oversightRep->findByIdAndAccountId($entry['oversightId'], $account->getId());
                $parameters = $parameterRep->findAllByOversightId($oversight->getId());
                $details = $entryDetailRep->findAllByEntryId($entryId);
                $requestData = json_decode($requestStack->getCurrentRequest()->getContent());

                if($requestData != null && $requestData->date != null) {

                    $entry['date'] = $requestData->date;
                    for($i = 0; $i < count((array)$details); $i++) {
                        foreach($requestData->parameters as $parameter) {
                            if($parameter->value != null && 
                                    $details[$i]['parameterId'] == $parameter->id && 
                                    $details[$i]['value'] != $parameter->value) {
                                $details[$i]['value'] = $parameter->value;
                                break;
                            }
                        }
                    } $oversightEntryRep->edit($entry, $details);

                } else {

                    $model['oversight'] = $oversight;
                    $model['entry'] = $entry;
                    $model['parameters'] = $parameters;
                    $model['details'] = $details;

                }

            } catch(RepositoryException $e) {

                $model['status'] = -1;
                $model['message'] = $e->getMessage();

            } finally {

                return $this->json($model);
                
            }

        }

    }

}

?>