<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */

    private $entityManager;
    /**
     * @var MedicoFactory
     */
    private MedicoFactory $medicoFactory;

    public function __construct(EntityManagerInterface $entityManager, MedicoFactory $medicoFactory)
    {
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
    }

    /**
     * @Route("/medicos", methods={"POST"})
     */

    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medico = $this->medicoFactory->criarMedico($corpoRequisicao);

        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico);

    }

    /**
     * @Route("/medicos", methods={"GET"})
     */

    public function buscarTodos(): Response
    {
        $repositorioDeMedicos = $this->entityManager->getRepository(Medico::class);
        $listaMedicos = $repositorioDeMedicos->findAll();

        return new JsonResponse($listaMedicos);
    }

    /**
     * @Route("/medicos/{id}", methods={"GET"})
     */


    public function buscarUm(int $id): Response
    {
        $medico = $this->buscaMedico($id);

        $codigoRetorno = is_null($medico) ? Response::HTTP_NO_CONTENT : 200;

        return new JsonResponse($medico, $codigoRetorno);
    }

    /**
     * @Route("/medicos/{id}", methods={"PUT"})
     */

    public function atualizar(int $id, Request $request): Response
    {
        //Pegando o corpo da requisição
        $corpoRequisicao = $request->getContent();
            
        $medicoEnviado = $this->medicoFactory->criarMedico($corpoRequisicao);

        //Pegando do repositorio o medico pelo seu id
        $medicoExistente = $this->buscaMedico($id);

        //usando if para verificar se o id foi encontrado
        if(is_null($medicoExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        //atualizando os dados
        $medicoExistente->crm = $medicoEnviado->crm;
        $medicoExistente->nome = $medicoEnviado->nome;

        //Não precisa usar $this->entityManager->persist($medico); pois o doctrine já está gerenciando

        //Agora do enviar
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente);
    }

    /**
     * @Route("/medicos/{id}", methods={"DELETE"})
     */

    public function remove(int $id): Response
    {
        $medico = $this->buscaMedico($id);
        $this->entityManager->remove($medico);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
    

    public function buscaMedico(int $id): mixed
    {
        $repositorioDeMedicos = $this->entityManager->getRepository(Medico::class);
        $medico = $repositorioDeMedicos->find($id);
        return $medico;
    }


}
