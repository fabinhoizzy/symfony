<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    private bool $sucesso;
    private $conteudoResposta;
    private int $paginaAtual;
    private int $itensPorPagina;
    private int $statusResposta;

    /**
     * @param bool $sucesso
     * @param $conteudoResposta
     * @param int $statusResposta
     * @param int|null $paginaAtual
     * @param int|null $itensPorPagina
     */
    public function __construct(
        bool $sucesso,
        $conteudoResposta,
        int $statusResposta = 200,
        int $paginaAtual = null,
        int $itensPorPagina = null
        )
    {
        $this->sucesso = $sucesso;
        $this->conteudoResposta = $conteudoResposta;
        $this->paginaAtual = $paginaAtual;
        $this->itensPorPagina = $itensPorPagina;
        $this->statusResposta = $statusResposta;
    }

    public function getResponse(): JsonResponse
    {
        $conteudoResposta = [
            'sucesso' => $this->sucesso,
            'paginaAtual' => $this->paginaAtual,
            'itensPorPagina' => $this->itensPorPagina,
            'conteudoResposta' => $this->conteudoResposta
        ];
        if (is_null($this->paginaAtual)) {
            unset($conteudoResposta['paginaAtual']);
            unset($conteudoResposta['itensPorPagina']);
        }

        return new JsonResponse($conteudoResposta, $this->statusResposta);
    }
}