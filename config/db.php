<?php
/**
 * Sillage - Conexão Segura com Banco de Dados MySQL (PDO)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'blog_perfumaria');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Tenta criar o banco se não existir
            try {
                $rootDsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $innerException) {
                die("Erro crítico de conexão com o banco de dados Sillage: " . htmlspecialchars($innerException->getMessage()));
            }
        }
    }
    return $pdo;
}

/**
 * Garante que as tabelas essenciais existam
 */
function initializeDatabase(): void {
    $pdo = getDBConnection();

    // Tabela: usuarios_adm
    $sqlUsuarios = "CREATE TABLE IF NOT EXISTS usuarios_adm (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        tipo ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
        data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sqlUsuarios);

    // Migração transparente: adiciona 'tipo' se a tabela já existir sem a coluna
    try {
        $checkCol = $pdo->query("SHOW COLUMNS FROM usuarios_adm LIKE 'tipo'")->fetch();
        if (!$checkCol) {
            $pdo->exec("ALTER TABLE usuarios_adm ADD COLUMN tipo ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario' AFTER senha");
            $pdo->exec("UPDATE usuarios_adm SET tipo = 'admin' WHERE tipo IS NULL OR tipo = '' OR tipo = 'usuario'");
        }
    } catch (Exception $e) {
        // Silencia erro caso já esteja migrado
    }

    // Tabela: posts
    $sqlPosts = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        imagem VARCHAR(255) DEFAULT NULL,
        conteudo LONGTEXT NOT NULL,
        referencias TEXT DEFAULT NULL,
        categoria ENUM('noticias', 'resenhas', 'curiosidades') NOT NULL,
        data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        autor_id INT NOT NULL,
        nome_perfume VARCHAR(255) DEFAULT NULL,
        concentracao VARCHAR(100) DEFAULT NULL,
        familia_olfativa VARCHAR(100) DEFAULT NULL,
        notas_topo VARCHAR(255) DEFAULT NULL,
        notas_corpo VARCHAR(255) DEFAULT NULL,
        notas_fundo VARCHAR(255) DEFAULT NULL,
        clima VARCHAR(255) DEFAULT NULL,
        INDEX idx_categoria (categoria),
        CONSTRAINT fk_posts_autor FOREIGN KEY (autor_id) REFERENCES usuarios_adm(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sqlPosts);
}
