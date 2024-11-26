<?php
session_start();
include_once('include/conexao.php');

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao = $_POST['descricao'];
    $setor = $_POST['setor'];
    $usuario_id = $_POST['usuario_id'];
    $prioridade = $_POST['prioridade'];
    $status = "A fazer"; 

    if (!empty($descricao) && !empty($setor) && !empty($usuario_id) && !empty($prioridade)) {
        $conn->begin_transaction();

        try {
            $checkQuery = $conn->prepare("SELECT id FROM tarefa WHERE descricao = ? AND setor = ? AND usuario_id = ?");
            $checkQuery->bind_param("ssi", $descricao, $setor, $usuario_id);
            $checkQuery->execute();
            $checkQuery->store_result();

            if ($checkQuery->num_rows > 0) {
                echo "A tarefa já existe para este usuário.";
            } else {
                $stmt = $conn->prepare("INSERT INTO tarefa (descricao, setor, usuario_id, prioridade, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiss", $descricao, $setor, $usuario_id, $prioridade, $status);

                if ($stmt->execute()) {
                    echo "Tarefa cadastrada com sucesso!";
                } else {
                    echo "Erro ao cadastrar tarefa: " . $stmt->error;
                }
                $stmt->close();
            }
            $checkQuery->close();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Erro ao processar a tarefa: " . $e->getMessage();
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}

$conn->close();
?>
