<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
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

    /**
     * @param ObjectRepository $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ObjectRepository $repository, EntityManagerInterface $entityManager, EntidadeFactory $factory)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }

    public function novo(Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $entidade = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        return  new JsonResponse($entidade);
    }

    public function buscarTodos(): Response
    {
        $entityList = $this->repository->findAll();

        return new JsonResponse($entityList);
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
}