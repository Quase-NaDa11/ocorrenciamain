<?php
include '../api/conexao.php';

// Consulta 1: Total de ocorrências por turma
$sqlTurma = "SELECT turma, COUNT(*) as total FROM Ocorrencia GROUP BY turma";
$resultTurma = $conn->query($sqlTurma);

$turmas = [];
$totaisTurma = [];
while ($row = $resultTurma->fetch_assoc()) {
    $turmas[] = $row['turma'];
    $totaisTurma[] = $row['total'];
}

// Consulta 2: Ocorrências mais comuns (por situação)
$sqlSituacao = "SELECT situacao, COUNT(*) as total FROM Ocorrencia GROUP BY situacao ORDER BY total DESC LIMIT 5";
$resultSituacao = $conn->query($sqlSituacao);

$situacoes = [];
$totaisSituacao = [];
$totalGeralSituacoes = 0;

while ($row = $resultSituacao->fetch_assoc()) {
    $situacoes[] = $row['situacao'];
    $totaisSituacao[] = $row['total'];
    $totalGeralSituacoes += $row['total'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Gráficos de Ocorrências</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #048a23;
      color: #333;
      padding-bottom: 40px;
    }

    header {
      width: 100%;
      background-color: #01923d;
      padding: 10px 0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      padding: 0 10px;
      flex-wrap: nowrap;
    }

    .logo-container img {
      max-width: 100%;
      height: auto;
      display: block;
      flex-shrink: 0;
    }

    .texto {
      color: white;
      font-size: 14px;
      max-width: 400px;
      text-align: center;
      flex-shrink: 1;
    }

    @media (max-width: 480px) {
      .logo-container {
        flex-wrap: wrap;
        flex-direction: column;
        gap: 10px;
      }

      .logo-container img:nth-child(1),
      .logo-container .texto {
        display: none !important;
      }

      .logo-container img:nth-child(3) {
        width: 80px !important;
        height: auto !important;
      }
    }

    .grafico-container {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      max-width: 700px;
      margin: 30px auto;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 20px;
    }

    canvas {
      max-width: 100%;
      height: auto;
    }
    @media (max-width: 480px) {
  .logo-container img:nth-child(1),  /* Primeira imagem (Brasão do Ceará) */
  .logo-container .texto {          /* Texto no meio */
    display: none !important;
  }

  .logo-container img:nth-child(3) {  /* Logo da escola */
    width: 80px !important;
    height: auto !important;
  }

  .logo-container {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }
}

  </style>
</head>
<body>

  <header>
    <div class="logo-container">
      <img src="/ocorrenciamain/img/brasao-do-ceara.png" alt="Brasão do Ceará" width="70px" />
      <p class="texto">
        GOVERNO DO ESTADO DO CEARÁ <br>
        19ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO <br>
        ESCOLA ESTADUAL DE EDUCAÇÃO PROFISSIONAL PAULO BARBOSA LEITE
      </p>
      <img src="/ocorrenciamain/img/escola-removebg-preview.png" alt="Logo da Escola" width="100px" />
    </div>
  </header>

  <div class="grafico-container">
    <h2>📊 Ocorrências por Turma</h2>
    <canvas id="graficoTurmas"></canvas>
  </div>

  <div class="grafico-container">
    <h2>🔥 Ocorrências Mais Frequentes (em %)</h2>
    <canvas id="graficoSituacoes"></canvas>
  </div>

  <script>
    const turmas = <?= json_encode($turmas) ?>;
    const totaisTurma = <?= json_encode($totaisTurma) ?>;

    const situacoes = <?= json_encode($situacoes) ?>;
    const totaisSituacao = <?= json_encode($totaisSituacao) ?>;
    const totalGeral = <?= $totalGeralSituacoes ?>;

    // Gráfico de barras - Ocorrências por turma
    new Chart(document.getElementById('graficoTurmas'), {
      type: 'bar',
      data: {
        labels: turmas,
        datasets: [{
          label: 'Total de Ocorrências',
          data: totaisTurma,
          backgroundColor: '#4e73df'
        }]
      },
      options: {
        plugins: {
          title: {
            display: true,
            text: 'Total de Ocorrências por Turma',
            font: { size: 18 }
          },
          legend: { display: false }
        },
        responsive: true
      }
    });

    // Gráfico de pizza - Situações com porcentagens
    new Chart(document.getElementById('graficoSituacoes'), {
      type: 'pie',
      data: {
        labels: situacoes,
        datasets: [{
          data: totaisSituacao,
          backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#00C49F', '#FF9F40']
        }]
      },
      options: {
        plugins: {
          title: {
            display: true,
            text: '5 Ocorrências Mais Comuns',
            font: { size: 18 }
          },
          datalabels: {
            formatter: (value, context) => {
              const porcentagem = ((value / totalGeral) * 100).toFixed(1);
              return porcentagem + '%';
            },
            color: '#fff',
            font: {
              weight: 'bold',
              size: 14
            }
          }
        }
      },
      plugins: [ChartDataLabels]
    });
  </script>
</body>
</html>
