<?php

// Controller da entidade Ativo. Recebe as requisições HTTP das rotas de ativos, chama o AtivoService e carrega a View correspondente. Nunca acessa o DAO diretamente.

declare(strict_types=1);

namespace controllers;

use services\AtivoService;
use dao\AtivoDAO;

class AtivoController
{
    private readonly AtivoService $servico;

    public function __construct()
    {
        // Cria a conexão PDO e injeta no DAO e no Service
        $pdo = require __DIR__ . '/../config/database.php';

        $this->servico = new AtivoService(new AtivoDAO($pdo));
    }

    // Exibe a listagem de todos os ativos com filtros opcionais por tipo e busca textual (GET /ativos)
    public function listar(): void
    {
        $busca  = $_GET['busca']  ?? null;
        $tipo   = $_GET['tipo']   ?? null;
        $status = $_GET['status'] ?? null;

        $ativos = $this->servico->listarTodos($tipo ?: null, $status ?: null, $busca ?: null);

        require __DIR__ . '/../views/ativos/lista.php';
    }

    // Exibe o formulário de cadastro de novo ativo (GET /ativos/novo)
    public function criar(): void
    {
        require __DIR__ . '/../views/ativos/criar.php';
    }

    // Recebe os dados do formulário e cadastra o novo ativo (POST /ativos/salvar)
    public function salvar(): void
    {
        try {
            $this->servico->cadastrar($_POST);
            header('Location: /vincitam/public/ativos?sucesso=cadastro');
        } catch (\InvalidArgumentException $e) {
            // Retorna ao formulário com a mensagem de erro de validação
            $erro = $e->getMessage();
            require __DIR__ . '/../views/ativos/criar.php';
        }
    }

    // Exibe o formulário de edição de um ativo existente (GET /ativos/editar?id=X)
    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        try {
            $ativo = $this->servico->buscarPorId($id);
            require __DIR__ . '/../views/ativos/editar.php';
        } catch (\RuntimeException) {
            header('Location: /vincitam/public/ativos?erro=nao_encontrado');
        }
    }

    // Recebe os dados do formulário e atualiza o ativo (POST /ativos/atualizar)
    public function atualizar(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->servico->atualizar($id, $_POST);
            header('Location: /vincitam/public/ativos?sucesso=edicao');
        } catch (\InvalidArgumentException $e) {
            $erro  = $e->getMessage();
            $ativo = $this->servico->buscarPorId($id);
            require __DIR__ . '/../views/ativos/editar.php';
        }
    }

    // Remove um ativo e redireciona para a listagem (POST /ativos/deletar)
    public function deletar(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        $this->servico->deletar($id);
        header('Location: /vincitam/public/ativos?sucesso=exclusao');
    }
}
