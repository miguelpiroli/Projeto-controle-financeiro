<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "controle_financeiro";


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


$mes_selecionado = isset($_GET["mes"]) ? $_GET["mes"] : date("Y-m");
$filtro_sql = "WHERE DATE_FORMAT(data, '%Y-%m') = '$mes_selecionado'";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="resumo.php">VER SEU REMUSO MENSAL</a>
  </nav>
</div>



<div class="container my-5">
        <h1 class="text-center mb-4">Controle Financeiro</h1>

        
        <form method="get" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <input type="month" class="form-control" name="mes" value="<?php echo $mes_selecionado; ?>">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </form>

        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5>Entradas</h5>
                    <h3 class="text-success">
                        <?php
                        $sql = "SELECT SUM(valor) as total FROM transacoes $filtro_sql AND tipo = 'entrada'";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo "R$ " . number_format($row["total"] ?? 0, 2, ",", ".");
                        ?>
                    </h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5>Saídas</h5>
                    <h3 class="text-danger">
                        <?php
                        $sql = "SELECT SUM(valor) as total FROM transacoes $filtro_sql AND tipo = 'saida'";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo "R$ " . number_format($row["total"] ?? 0, 2, ",", ".");
                        ?>
                    </h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5>Total</h5>
                    <h3>
                        <?php
                        $sql = "SELECT SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE -valor END) as total FROM transacoes $filtro_sql";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo "R$ " . number_format($row["total"] ?? 0, 2, ",", ".");
                        ?>
                    </h3>
                </div>
            </div>
        </div>

        
        <form action="" method="post">
            <input type="hidden" name="mes" value="<?php echo $mes_selecionado; ?>"> 
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="text" class="form-control" placeholder="Descrição" id="descricao" name="descricao" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" class="form-control" placeholder="Valor" id="valor" name="valor" required>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo" id="entrada" value="entrada" required>
                        <label class="form-check-label" for="entrada">Entrada</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo" id="saida" value="saida" required>
                        <label class="form-check-label" for="saida">Saída</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success w-100">Adicionar</button>
                </div>
            </div>
        </form>

        <?php
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $descricao = $_POST["descricao"];
            $valor = $_POST["valor"];
            $tipo = $_POST["tipo"];
            $data = $mes_selecionado . '-01'; 

            $sql = "INSERT INTO transacoes (descricao, valor, tipo, data) VALUES ('$descricao', '$valor', '$tipo', '$data')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success mt-4'>Transação adicionada com sucesso!</div>";
            } else {
                echo "<div class='alert alert-danger mt-4'>Erro ao adicionar transação: " . $conn->error . "</div>";
            }

            
            header("Location: index.php?mes=$mes_selecionado");
            exit();
        }
        ?>

      
        <table class="table table-bordered mt-4">
            <thead class="table-light">
                <tr>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM transacoes $filtro_sql";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["descricao"] . "</td>";
                        echo "<td>R$ " . number_format($row["valor"], 2, ",", ".") . "</td>";
                        echo "<td>" . ucfirst($row["tipo"]) . "</td>";
                        echo "<td>" . date("d/m/Y", strtotime($row["data"])) . "</td>";
                        echo "<td>
                               
                                <a class='btn btn-danger' href='excluir.php?id=" . $row["id"] . "&mes=$mes_selecionado'>Excluir</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhuma transação encontrada para o mês selecionado.</td></tr>";
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
?>
