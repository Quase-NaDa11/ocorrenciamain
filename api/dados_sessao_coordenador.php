<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['coordenacao_id']) || !isset($_SESSION['nome'])) {
    echo json_encode(['erro' => 'Usuário não está logado']);
    exit;
}

echo json_encode([
    'nome' => $_SESSION['nome'],
    'id' => $_SESSION['coordenacao_id']
]);
