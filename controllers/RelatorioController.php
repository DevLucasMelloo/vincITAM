<?php

// Controller de Relatórios. Exibe a listagem de ativos com filtros para geração de relatórios e exportação (RF-007).

declare(strict_types=1);

namespace controllers;

use services\AtivoService;
use services\VinculoService;
use dao\AtivoDAO;
use dao\VinculoDAO;

class RelatorioController
{
    private readonly AtivoService   $servico;
    private readonly VinculoService $vinculoServico;

    public function __construct()
    {
        $pdo                  = require __DIR__ . '/../config/database.php';
        $this->servico        = new AtivoService(new AtivoDAO($pdo));
        $this->vinculoServico = new VinculoService(new VinculoDAO($pdo), new AtivoDAO($pdo));
    }

    // Gera e faz download do inventário de ativos em formato CSV com vínculos (RF-007)
    public function exportarCsv(): void
    {
        $tipo   = $_GET['tipo']   ?? null;
        $status = $_GET['status'] ?? null;
        $busca  = $_GET['busca']  ?? null;

        $ativos          = $this->servico->listarTodos($tipo ?: null, $status ?: null, $busca ?: null);
        $vinculosPorAtivo = $this->vinculoServico->listarIndexadosPorPai();

        // Define os headers HTTP para forçar o download do arquivo
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="vincitam-inventario-' . date('Y-m-d') . '.csv"');

        $saida = fopen('php://output', 'w');

        // Escreve o BOM UTF-8 para o Excel reconhecer acentos corretamente
        fputs($saida, "\xEF\xBB\xBF");

        // Cabeçalho do CSV — inclui coluna de vínculos/localizações conforme RF-007
        fputcsv($saida, ['ID', 'Nome', 'Tipo', 'Número de Série', 'Data de Aquisição', 'Status', 'Dependentes (Vínculos)'], ';');

        foreach ($ativos as $ativo) {
            // Concatena todos os vínculos do ativo em uma única célula separados por " | "
            $dependentes = isset($vinculosPorAtivo[$ativo->id])
                ? implode(' | ', $vinculosPorAtivo[$ativo->id])
                : '';

            fputcsv($saida, [
                $ativo->id,
                $ativo->nome,
                $ativo->tipo,
                $ativo->numeroSerie   ?? '',
                $ativo->dataAquisicao ?? '',
                $ativo->status,
                $dependentes,
            ], ';');
        }

        fclose($saida);
        exit;
    }

    // Exibe a página de relatórios com filtros opcionais por tipo e status via GET (RF-007)
    public function index(): void
    {
        $tipo   = $_GET['tipo']   ?? null;
        $status = $_GET['status'] ?? null;

        $ativos = $this->servico->listarTodos($tipo ?: null, $status ?: null);

        require __DIR__ . '/../views/relatorios/index.php';
    }
}
