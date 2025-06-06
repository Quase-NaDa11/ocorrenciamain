<?php
session_start();
include 'conexao.php';

$email = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha email e senha.";
    } else {
        $query = "SELECT id, nome, senha FROM Coordenador WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($senha, $user['senha'])) {
                $_SESSION['coordenador_id'] = $user['id'];
                $_SESSION['nome'] = $user['nome'];

                $stmt->close();
                $conn->close();

                header("Location: /ocorrenciamain/public/geral.html");
                exit();
            } else {
                $erro = "Email ou senha inválidos!";
            }
        } else {
            $erro = "Email ou senha inválidos!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login da Coordenação</title>
    <link rel="stylesheet" href="/ocorrenciamain/public/login.css" />
</head>
<body>
    <div class="login-container">
        <h2>Login da Coordenação</h2>
        <?php if (!empty($erro)) : ?>
            <p style="color: red; text-align: center; margin-bottom: 15px;">
                <?= htmlspecialchars($erro) ?>
            </p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Digite seu e-mail" value="<?= htmlspecialchars($email) ?>" required />

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" placeholder="Digite sua senha" required />

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
