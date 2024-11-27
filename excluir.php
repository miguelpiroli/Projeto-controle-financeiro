<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "controle_financeiro";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];

     
    $sql = "DELETE FROM transacoes WHERE id = '$id'";

    
    if ($conn->query($sql) === TRUE) {
        echo "Despesa excluída com sucesso!";
    } else {
        echo "Erro ao excluir despesa: " . $conn->error;
    }
} else {
    echo "ID não especificado.";
}


$conn->close();
?>

<script>
    window.setTimeout(function() {
        window.location.href = "index.php";
    }, 3000);
</script>