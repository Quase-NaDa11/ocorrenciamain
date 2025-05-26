<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['nome'])) {
    echo json_encode(['error' => 'Usuário não logado']);
    exit();
}

date_default_timezone_set('America/Fortaleza');
$dataAtual = date('Y-m-d\TH:i'); // formato para input datetime-local

echo json_encode([
    'nomeProfessor' => $_SESSION['nome'],
    'dataAtual' => $dataAtual
]);
?>
