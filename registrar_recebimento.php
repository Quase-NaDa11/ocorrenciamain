<?php
include 'conexao.php';

if (isset($_POST['ocorrencia_id'], $_POST['data_recebimento'], $_POST['responsavel'])) {
    $ocorrencia_id = $_POST['ocorrencia_id'];
    $data_recebimento = $_POST['data_recebimento'];
    $responsavel = $_POST['responsavel'];

    // Registrar o recebimento na tabela Coordenacao
    $stmt = $conn->prepare("INSERT INTO Coordenacao (ocorrencia_id, data_recebimento, responsavel_recebimento) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $ocorrencia_id, $data_recebimento, $responsavel);

    if ($stmt->execute()) {
        // Atualizar o status da ocorrência para "Concluído"
        $update_stmt = $conn->prepare("UPDATE Ocorrencia SET status = 'Concluído' WHERE id = ?");
        $update_stmt->bind_param("i", $ocorrencia_id);
        $update_stmt->execute();

        header("Location: coordenacao.php"); // Redireciona para a página principal
        exit();
    } else {
        echo "Erro ao registrar o recebimento: " . $conn->error;
    }
} else {
    echo "Erro: Todos os campos são obrigatórios.";
}
?>
