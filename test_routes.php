<?php
$pages = [
    'index.php',
    'resenhas.php',
    'sobre.php',
    'login.php',
    'privacidade.php',
    'post.php?id=1',
    'admin/index.php' // Deve redirecionar para login.php devido a require_admin()
];

$baseUrl = 'http://localhost/Sillage/';
echo "Testando rotas do Sillage em {$baseUrl} ...\n\n";

foreach ($pages as $p) {
    $url = $baseUrl . $p;
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true,
            'follow_location' => 0
        ]
    ]);
    $fp = @fopen($url, 'r', false, $ctx);
    if ($fp) {
        $meta = stream_get_meta_data($fp);
        $headers = $meta['wrapper_data'] ?? [];
        $statusLine = $headers[0] ?? 'Desconhecido';
        echo sprintf("%-20s -> %s\n", $p, $statusLine);
        fclose($fp);
    } else {
        echo sprintf("%-20s -> FALHA DE CONEXAO\n", $p);
    }
}
