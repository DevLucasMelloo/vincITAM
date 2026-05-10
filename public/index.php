<?php

// Ponto de entrada único do sistema (Front Controller). Todas as requisições passam por aqui — o .htaccess garante isso. Inicializa o autoload, captura a URL e despacha para o Controller correto.

declare(strict_types=1);

// Exibe erros durante o desenvolvimento — remover em produção
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Autoload das classes por namespace — mapeia namespace\Classe para o arquivo correspondente na estrutura de pastas
spl_autoload_register(function (string $classe): void {
    // Converte namespace\Classe em caminho de arquivo ex: controllers\AtivoController → controllers/AtivoController.php
    $arquivo = __DIR__ . '/../' . str_replace('\\', '/', $classe) . '.php';

    if (file_exists($arquivo)) {
        require $arquivo;
    }
});

// Carrega a tabela de rotas definida em routes/web.php
$rotas = require __DIR__ . '/../routes/web.php';

// Captura o método HTTP (GET ou POST) e a URL da requisição
$metodo = $_SERVER['REQUEST_METHOD'];
$url    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove o prefixo '/vincitam' da URL para funcionar no subdiretório do XAMPP
$url = str_replace('/vincitam', '', $url) ?: '/';

// Verifica se a rota existe na tabela de rotas
if (isset($rotas[$metodo][$url])) {
    // Separa 'NomeController@nomeMetodo' em duas partes
    [$nomeController, $nomeMetodo] = explode('@', $rotas[$metodo][$url]);

    $classeController = "controllers\\{$nomeController}";

    $controller = new $classeController();
    $controller->$nomeMetodo();
} else {
    // Rota não encontrada — retorna 404
    http_response_code(404);
    require __DIR__ . '/../views/404.php';
}
