<?php
include '../api/conexao.php';

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

$sql = "SELECT 
            Ocorrencia.estudante, 
            Ocorrencia.turma, 
            Ocorrencia.situacao, 
            Ocorrencia.data,
            Professor.nome AS professor,
            Coordenador.nome AS coordenador
        FROM Ocorrencia
        LEFT JOIN Professor ON Ocorrencia.professor_id = Professor.id
        LEFT JOIN Coordenador ON Ocorrencia.coordenador_id = Coordenador.id
        WHERE MONTH(data) = ? AND YEAR(data) = ?
        ORDER BY data DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $mes, $ano);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório Mensal</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      padding: 20px;
    }

    header {
      background-color: #01923d;
      color: white;
      text-align: center;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h1 {
      margin-bottom: 5px;
    }

    .filtros {
      text-align: center;
      margin-bottom: 20px;
    }

    .filtros form {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      align-items: center;
    }

    select, input[type="number"], button {
      padding: 8px 12px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      background-color: #01923d;
      color: white;
      border: none;
      cursor: pointer;
    }

    button:hover {
      background-color: #017832;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px 8px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #01923d;
      color: white;
    }

    tbody tr:hover {
      background-color: #f7f7f7;
    }

    .botoes {
      text-align: center;
      margin-top: 25px;
    }

    .botoes button {
      margin: 5px;
      padding: 10px 20px;
      font-size: 16px;
      background-color: #01923d;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    .botoes button:hover {
      background-color: #017832;
    }

    @media print {
      .filtros, .botoes {
        display: none;
      }

      header {
        background-color: #000;
        color: white;
        -webkit-print-color-adjust: exact;
      }
    }

    @media (max-width: 768px) {
      table {
        font-size: 13px;
      }

      .filtros form {
        flex-direction: column;
        gap: 8px;
      }

      .botoes button {
        width: 80%;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Relatório Mensal de Ocorrências</h1>
    <p>Período: <?= $mes ?>/<?= $ano ?></p>
  </header>

  <div class="filtros">
    <form method="GET">
      <label for="mes">Mês:</label>
      <select name="mes" id="mes" required>
        <?php for ($i = 1; $i <= 12; $i++): 
          $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
          <option value="<?= $val ?>" <?= ($val == $mes ? 'selected' : '') ?>><?= $val ?></option>
        <?php endfor; ?>
      </select>

      <label for="ano">Ano:</label>
      <input type="number" name="ano" id="ano" value="<?= $ano ?>" required min="2000" max="<?= date('Y') ?>" />

      <button type="submit">🔍 Filtrar</button>
    </form>
  </div>

  <table>
    <thead>
      <tr>
        <th>Estudante</th>
        <th>Turma</th>
        <th>Problema</th>
        <th>Data</th>
        <th>Professor</th>
        <th>Coordenador</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['estudante']) ?></td>
            <td><?= htmlspecialchars($row['turma']) ?></td>
            <td><?= htmlspecialchars($row['situacao']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['data'])) ?></td>
            <td><?= htmlspecialchars($row['professor'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['coordenador'] ?? '-') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6">Nenhuma ocorrência encontrada neste período.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="botoes">
    <button onclick="window.print()">🖨️ Imprimir</button>
    <button onclick="exportarPDF()">📄 Exportar em PDF</button>
  </div>

  <script>
    function exportarPDF() {
      window.print(); // Alternativa simples: usar "Salvar como PDF" na impressão
    }
  </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
