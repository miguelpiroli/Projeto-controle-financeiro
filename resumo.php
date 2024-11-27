


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<body>

<?php
require 'conexao.php';

$sql = "SELECT 
            DATE_FORMAT(data, '%Y-%m') as mes,
            SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END) as total_entradas,
            SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END) as total_saidas,
            SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE -valor END) as saldo
        FROM transacoes
        GROUP BY mes
        ORDER BY mes DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo Mensal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    

<div class="container my-5">
        <h1 class="text-center mb-4">Resumo Mensal</h1>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Mês</th>
                    <th>Total Entradas</th>
                    <th>Total Saídas</th>
                    <th>Saldo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $mes = $row["mes"];
                        echo "<tr>";
                        echo "<td>" . date("m/Y", strtotime($mes . "-01")) . "</td>";
                        echo "<td class='text-success'>R$ " . number_format($row["total_entradas"], 2, ",", ".") . "</td>";
                        echo "<td class='text-danger'>R$ " . number_format($row["total_saidas"], 2, ",", ".") . "</td>";
                        $saldo_class = $row["saldo"] >= 0 ? "text-success" : "text-danger";
                        echo "<td class='$saldo_class'>R$ " . number_format($row["saldo"], 2, ",", ".") . "</td>";
                        echo "<td>
                                <a class='btn btn-primary' href='index.php?mes=$mes'>VOLTAR</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Nenhum dado disponível.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();




