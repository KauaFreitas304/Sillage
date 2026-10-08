<?php
$pdo = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', 'root', '');
$hash = password_hash('admin123', PASSWORD_BCRYPT);

foreach (['blog_perfumaria', 'sillage_db'] as $db) {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $db");
    
    // Inserir Administrador padrão do Image 5 (admin@blog.com)
    $stmt1 = $pdo->prepare("INSERT INTO usuarios_adm (nome, email, senha) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE senha = ?");
    $stmt1->execute(['Administrador', 'admin@blog.com', $hash, $hash]);

    // Inserir Administrador do projeto (admin@sillage.com)
    $stmt2 = $pdo->prepare("INSERT INTO usuarios_adm (nome, email, senha) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE senha = ?");
    $stmt2->execute(['Kauã Freitas & Yasmin Cristiny', 'admin@sillage.com', $hash, $hash]);
}

echo "Admins configurados com sucesso em blog_perfumaria e sillage_db!\n";
