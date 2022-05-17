<?php

namespace App\Controller;

use App\Helper\ExtratorDadosRequest;
use App\Helper\MedicoFactory;
use App\Repository\MedicosRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;

class MedicosController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicosRepository $medicosRepository,
        ExtratorDadosRequest $extratorDadosRequest
    )
    {
        parent::__construct($medicosRepository, $entityManager, $medicoFactory, $extratorDadosRequest);
    }

    /**
     * @param int $id
     * @param $entidade
     * @return mixed|object
     */
    public function atualizarEntidadeExistente($id, $entidade)
    {
        $entidadeExistente = $this->repository->find($id);
        if (is_null($entidadeExistente)) {
            throw new \InvalidArgumentException();
        }

        $entidadeExistente
            ->setCrm($entidade->getCrm())
            ->setNome($entidade->getNome());

        return $entidadeExistente;
    }
}
