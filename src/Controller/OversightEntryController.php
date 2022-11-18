<?php

namespace App\Controller;

use App\Entity\OversightEntry;
use App\Repository\OversightRepository;
use App\Repository\ParameterRepository;
use App\Repository\RepositoryException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/oversight-entry/add/{oversightId}', name: 'oversight_entry_add')]
    public function add(int $oversightId, 
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
                $entry = new OversightEntry(
                    $oversightId,
                    $requestStack->getCurrentRequest()->request->get('date'),
                    1
                ); $entry->validate();

                // INITIER UNE TRANSACTION ICI

                foreach($model['parameters'] as $parameter) {
                    $requestStack->getCurrentRequest()->request->get('parameter-'. $parameter['id']);
                }
            } catch(RepositoryException $e) {
                $model['status'] = -1;
                $model['message'] = $e->getMessage();
            } finally {
                return $this->render('oversight-entry/add.html.twig', $model);
            }
        }

    }

}

?>