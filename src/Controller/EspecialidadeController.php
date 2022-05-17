<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\ExtratorDadosRequest;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;

class EspecialidadeController extends BaseController
{

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory,
        ExtratorDadosRequest $extratorDadosRequest
    )
    {
        parent::__construct($repository, $entityManager, $factory, $extratorDadosRequest);
    }

    public function atualizarEntidadeExistente(int $id, $entidade)
    {
        /**
         * @var Especialidade $entidadeExistente
         */

        $entidadeExistente = $this->repository->find($id);
        if (is_null($entidadeExistente)) {
            throw new \InvalidArgumentException();
        }

        $entidadeExistente
            ->setDescricao($entidade->getDescricao());
        return $entidadeExistente;
    }
}
