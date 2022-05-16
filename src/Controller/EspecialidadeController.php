<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadeController extends BaseController
{

    public function __construct(EntityManagerInterface $entityManager, EspecialidadeRepository $repository, EspecialidadeFactory $factory)
    {
        parent::__construct($repository, $entityManager, $factory);
    }

    /**
     * @Route("/especialidades/{id}", methods={"PUT"})
     */

    public function atualiza(int $id, Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $dadosEmJson = json_decode($dadosRequest);

        $especialidade = $this->repository->find($id);
        $especialidade
            ->setDescricao($dadosEmJson->descricao);
        $this->entityManager->flush();

        return new JsonResponse($especialidade);
    }


}
