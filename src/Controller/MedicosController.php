<?php

namespace App\Controller;

use App\Helper\MedicoFactory;
use App\Repository\MedicosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{

    public function __construct(EntityManagerInterface $entityManager, MedicoFactory $medicoFactory, MedicosRepository $medicosRepository)
    {
        parent::__construct($medicosRepository, $entityManager, $medicoFactory);
    }


    /**
     * @Route("/medicos/{id}", methods={"PUT"})
     */

    public function atualizar(int $id, Request $request): Response
    {
        //Pegando o corpo da requisição
        $corpoRequisicao = $request->getContent();

        $medicoEnviado = $this->factory->criarEntidade($corpoRequisicao);

        //Pegando do repositorio o medico pelo seu id
        $medicoExistente = $this->buscaMedico($id);

        //usando if para verificar se o id foi encontrado
        if (is_null($medicoExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        //atualizando os dados
        $medicoExistente
            ->setCrm($medicoEnviado->getCrm())
            ->setNome($medicoEnviado->getNome());

        //Não precisa usar $this->entityManager->persist($medico); pois o doctrine já está gerenciando

        //Agora do enviar
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente);
    }

    /**
     * @Route ("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */

    public function buscaPorEspecialidade(int $especialidadeId): Response
    {
        $medicos = $this->repository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos);
    }

}
