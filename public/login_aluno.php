<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login do Aluno</title>
    <link rel="stylesheet" href="login.css" />
  </head>
  <body>
    <div class="login-container">
      <h2>Login do Aluno</h2>
      <form action="/ocorrenciamain/api/processa_login_aluno.php" method="POST">
        <label>Email Institucional:</label>
        <input
          type="email"
          name="login"
          placeholder="Digite seu e-mail"
          required
        />

        <label>Senha:</label>
        <input
          type="password"
          name="senha"
          placeholder="Digite sua senha"
          required
        />

        <button type="submit">Entrar</button>
      </form>
    </div>
  </body>
</html>
