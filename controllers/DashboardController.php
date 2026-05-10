<?php

// Controller do Dashboard. Recebe a requisição da rota '/' e repassa ao Service os dados necessários para exibir o painel geral do sistema.

declare(strict_types=1);

namespace controllers;

use services\AtivoService;
use services\VinculoService;
use dao\AtivoDAO;
use dao\VinculoDAO;

class DashboardController
{
    private readonly AtivoService   $ativoService;
    private readonly VinculoService $vinculoService;

    public function __construct()
    {
        // Cria a conexão PDO e injeta nos DAOs e Services
        $pdo = require __DIR__ . '/../config/database.php';

        $this->ativoService   = new AtivoService(new AtivoDAO($pdo));
        $this->vinculoService = new VinculoService(new VinculoDAO($pdo), new AtivoDAO($pdo));
    }

    // Exibe o painel geral com totais de ativos por tipo e total de vínculos (RF-001, RF-003)
    public function index(): void
    {
        $ativos   = $this->ativoService->listarTodos();
        $vinculos = $this->vinculoService->listarTodos();

        // Agrupa a contagem de ativos por tipo para exibir no dashboard
        $totaisPorTipo = [
            'servidor'          => 0,
            'banco_de_dados'    => 0,
            'dispositivo_rede'  => 0,
            'estacao_trabalho'  => 0,
        ];

        foreach ($ativos as $ativo) {
            $totaisPorTipo[$ativo->tipo]++;
        }

        $totalAtivos   = count($ativos);
        $totalVinculos = count($vinculos);

        require __DIR__ . '/../views/dashboard/index.php'; // Carrega a View passando as variáveis acima
    }
}
