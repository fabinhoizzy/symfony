<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\ExtratorDadosRequest;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class EspecialidadeController extends BaseController
{

    private CacheItemPoolInterface $cache;

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory,
        ExtratorDadosRequest $extratorDadosRequest,
        CacheItemPoolInterface $cache
    )
    {
        parent::__construct($repository, $entityManager, $factory, $extratorDadosRequest, $cache);
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

    public function cachePrefix(): string
    {
        return 'especialidade_';
    }
}
