<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
use App\Helper\ExtratorDadosRequest;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected ObjectRepository $repository;
    protected EntityManagerInterface $entityManager;
    protected EntidadeFactory $factory;
    private ExtratorDadosRequest $extratorDadosRequest;

    /**
     * @param ObjectRepository $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $factory,
        ExtratorDadosRequest $extratorDadosRequest)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->extratorDadosRequest = $extratorDadosRequest;
    }

    public function novo(Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $entidade = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        return  new JsonResponse($entidade);
    }

    public function buscarTodos(Request $request): Response
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
        return new JsonResponse($lista);
    }

    public function buscarUm(int $id): Response
    {
        return new JsonResponse($this->repository->find($id));
    }

    public function remove(int $id): Response
    {
        $entidade = $this->repository->find($id);
        $this->entityManager->remove($entidade);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);

    }

    public function atualiza(int $id, Request $request): Response
    {
        //Pegando o corpo da requisição
        $corpoRequisicao = $request->getContent();

        $entidadeEnviada = $this->factory->criarEntidade($corpoRequisicao);

        //Pegando do repositorio o medico pelo seu id
        $entidadeExistente = $this->repository->find($id);

        //usando if para verificar se o id foi encontrado
        if (is_null($entidadeExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        //atualizando os dados
        $this->atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);

        //Agora do enviar
        $this->entityManager->flush();

        return new JsonResponse($entidadeExistente);
    }

    abstract public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);
}