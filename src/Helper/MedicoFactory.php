<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory implements EntidadeFactory
{
    /**
     * @var EspecialidadeRepository
     */

    private EspecialidadeRepository $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {

        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function criarEntidade(string $json): Medico
    {
        $dadoEmJson = json_decode($json);
        $this->checkAllProperties($dadoEmJson);

        $medico = new Medico();
        $medico
            ->setCrm($dadoEmJson->crm)
            ->setNome($dadoEmJson->nome)
            ->setEspecialidade($this->especialidadeRepository->find($dadoEmJson->especialidadeId));
        return $medico;
    }

    private function checkAllProperties(mixed $dadoEmJson): void
    {
        if (!property_exists($dadoEmJson, 'crm')) {
            throw new EntityFactoryException('Médico precisa de um CRM');
        }
        if(!property_exists($dadoEmJson, 'nome')) {
            throw  new EntityFactoryException('Médico precisa de um nome');
        }
        if(!property_exists($dadoEmJson, 'especialidadeId')){
            throw new EntityFactoryException('Médico precisa de uma especialidade');
        }
    }
}