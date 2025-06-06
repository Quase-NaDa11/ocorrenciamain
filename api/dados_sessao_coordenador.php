<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['coordenador_id']) || !isset($_SESSION['nome'])) {
    echo json_encode(['erro' => 'Usuário não está logado']);
    exit;
}

echo json_encode([
    'nome' => $_SESSION['nome'],
    'id' => $_SESSION['coordenador_id']
]);
