<?php

// Controller da entidade Vinculo. Recebe as requisições HTTP das rotas de vínculos, chama o VinculoService e carrega a View correspondente. Nunca acessa o DAO diretamente.

declare(strict_types=1);

namespace controllers;

use services\VinculoService;
use services\AtivoService;
use dao\VinculoDAO;
use dao\AtivoDAO;

class VinculoController
{
    private readonly VinculoService $vinculoServico;
    private readonly AtivoService   $ativoServico;

    public function __construct()
    {
        // Cria a conexão PDO e injeta nos DAOs e Services
        $pdo = require __DIR__ . '/../config/database.php';

        $ativoDAO             = new AtivoDAO($pdo);
        $this->vinculoServico = new VinculoService(new VinculoDAO($pdo), $ativoDAO);
        $this->ativoServico   = new AtivoService($ativoDAO);
    }

    // Exibe a listagem de todos os vínculos (GET /vinculos)
    public function index(): void
    {
        $vinculos = $this->vinculoServico->listarTodos();
        $ativos   = $this->ativoServico->listarTodos(); // Necessário para popular os dropdowns do formulário

        require __DIR__ . '/../views/vinculos/index.php';
    }

    // Exibe o formulário de criação de vínculo (GET /vinculos/novo)
    public function criar(): void
    {
        $ativos = $this->ativoServico->listarTodos(); // Lista de ativos para os dropdowns pai e filho

        require __DIR__ . '/../views/vinculos/criar.php';
    }

    // Recebe os dados do formulário e cria o vínculo entre dois ativos (POST /vinculos/gerar)
    public function vincular(): void
    {
        try {
            $this->vinculoServico->vincular($_POST);
            header('Location: /vincitam/vinculos?sucesso=vinculo');
        } catch (\InvalidArgumentException $e) {
            // Retorna ao formulário com a mensagem de erro de validação (hierarquia invertida, duplicidade, etc.)
            $erro   = $e->getMessage();
            $ativos = $this->ativoServico->listarTodos();
            require __DIR__ . '/../views/vinculos/criar.php';
        }
    }

    // Exibe a topologia de um ativo — lista todos os ativos dependentes dele (RF-004)
    public function topologia(): void
    {
        $id       = (int) ($_GET['id'] ?? 0);
        $ativo    = $this->ativoServico->buscarPorId($id);
        $vinculos = $this->vinculoServico->obterTopologia($id);

        require __DIR__ . '/../views/vinculos/index.php';
    }

    // Remove um vínculo e redireciona para a listagem (POST /vinculos/deletar)
    public function deletar(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        $this->vinculoServico->deletar($id);
        header('Location: /vincitam/vinculos?sucesso=exclusao');
    }
}
