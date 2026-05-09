<?php

// Arquivo de exemplo para configuração da conexão com o banco de dados. Copie este arquivo como database.php e preencha com suas credenciais reais.

declare(strict_types=1);

$host    = '127.0.0.1'; // IP direto evita problemas com socket Unix no Windows
$banco   = 'vincitam';  // Nome do banco criado no MySQL
$usuario = 'root';      // Usuário do MySQL
$senha   = '';          // Senha do MySQL — preencha com sua senha real
$charset = 'utf8mb4';   // Suporta todos os caracteres incluindo acentos e emojis

// DSN (Data Source Name): string que identifica o banco para o PDO
$dsn = "mysql:host={$host};dbname={$banco};charset={$charset}";

// Opções de comportamento do PDO
$opcoes = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros de SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa Prepared Statements reais — protege contra SQL Injection (RNF-002)
];

try {
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}

return $pdo;
