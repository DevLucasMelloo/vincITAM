<?php

// Mapeamento de rotas do sistema. Define quais URLs respondem a quais métodos dos Controllers, separando GET (leitura) de POST (escrita).

declare(strict_types=1);

return [
    'GET' => [
        '/'                  => 'DashboardController@index',  // Painel geral do sistema
        '/ativos'            => 'AtivoController@listar',     // Listagem de todos os ativos
        '/ativos/novo'       => 'AtivoController@criar',      // Formulário de cadastro de ativo
        '/ativos/editar'     => 'AtivoController@editar',     // Formulário de edição de ativo (?id=X)
        '/vinculos'          => 'VinculoController@index',    // Listagem de todos os vínculos
        '/vinculos/novo'     => 'VinculoController@criar',    // Formulário de criação de vínculo
        '/vinculos/topologia'=> 'VinculoController@topologia',// Topologia de dependências de um ativo (?id=X)
    ],
    'POST' => [
        '/ativos/salvar'     => 'AtivoController@salvar',     // Cadastra novo ativo
        '/ativos/atualizar'  => 'AtivoController@atualizar',  // Atualiza ativo existente
        '/ativos/deletar'    => 'AtivoController@deletar',    // Remove ativo
        '/vinculos/gerar'    => 'VinculoController@vincular', // Cria vínculo entre dois ativos
        '/vinculos/deletar'  => 'VinculoController@deletar',  // Remove vínculo
    ],
];
