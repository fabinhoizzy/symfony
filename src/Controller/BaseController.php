<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
use App\Helper\ExtratorDadosRequest;
use App\Helper\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected ObjectRepository $repository;
    protected EntityManagerInterface $entityManager;
    protected EntidadeFactory $factory;
    protected ExtratorDadosRequest $extratorDadosRequest;
    private CacheItemPoolInterface $cache;
    private LoggerInterface $logger;

    /**
     * @param ObjectRepository $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $entityFactory,
        ExtratorDadosRequest $extratorDadosRequest,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->entityFactory = $entityFactory;
        $this->extratorDadosRequest = $extratorDadosRequest;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function novo(Request $request, ManagerRegistry $doctrine): Response
    {
        $entity = $this->entityFactory->criarEntidade($request->getContent());
        $entityManager = $doctrine->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $entity->getId());
        $cacheItem->set($entity);
        $this->cache->save($cacheItem);

        $this->logger
            ->notice('Novo registro de {entidade} adicionado com o id: {id}. ',
            [
                'entidade' => get_class($entity),
                'id' => $entity->getId(),
            ]
        );

        return  new JsonResponse($entity);
    }

    public function buscarTodos(Request $request)
    {
        $filtro = $this->extratorDadosRequest->buscaDadosFiltro($request);
        $informacoesDeOrdenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);

        [$paginaAtual, $itensPorPagina] = $this->extratorDadosRequest->buscaDadosPaginacao($request);

        $lista = $this->repository->findBy(
            $filtro,
            $informacoesDeOrdenacao,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );

        $fabricaResposta = new ResponseFactory(
            true,
            $lista,
            Response::HTTP_OK,
            $paginaAtual,
            $itensPorPagina
        );

        return $fabricaResposta->getResponse();
    }

    public function buscarUm(int $id): Response
    {
        $entity = $this->cache->hasItem($this->cachePrefix() . $id)
            ? $this->cache->getItem($this->cachePrefix() . $id)->get()
            : $entity = $this->repository->find($id);
        $statusResposta = is_null($entity) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        $fabricaResposta = new ResponseFactory(
            true,
            $entity,
            $statusResposta
        );

        return $fabricaResposta->getResponse();
    }

    public function remove(int $id): Response
    {
        $entidade = $this->repository->find($id);
        $this->entityManager->remove($entidade);
        $this->entityManager->flush();

        $this->cache->deleteItem($this->cachePrefix() . $id);

        return new Response('', Response::HTTP_NO_CONTENT);

    }

    public function atualiza(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);
        try {
            $entidadeExistente = $this->atualizarEntidadeExistente($id, $entidade);
            $this->entityManager->flush();

            $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
            $cacheItem->set($entidadeExistente);
            $this->cache->save($cacheItem);

            $fabrica = new ResponseFactory(
                true,
                $entidadeExistente,
                Response::HTTP_OK
            );
            return $fabrica->getResponse();
        } catch (\InvalidArgumentException $ex) {
            $fabrica = new ResponseFactory(
                false,
                'Recurso não encontrado',
                Response::HTTP_NOT_FOUND
            );
            return $fabrica->getResponse();
        }
    }
    abstract public function atualizarEntidadeExistente(int $id, $entidade);
    abstract public function cachePrefix(): string;
}