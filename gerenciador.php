<?php
session_start();
include_once('include/conexao.php');

if (isset($_POST['salvar'])) {
    $id_tarefa = $_POST['id_tarefa'];
    $descricao = $_POST['descricao'];
    $setor = $_POST['setor'];
    $prioridade = $_POST['prioridade'];

    $sql = "UPDATE tarefa SET descricao = ?, setor = ?, prioridade = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $descricao, $setor, $prioridade, $id_tarefa);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Tarefa atualizada com sucesso.";
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar tarefa.";
    }

    header("Location: gerenciador.php");
    exit;
}

if (isset($_POST['mudar_status'])) {
    $id_tarefa = $_POST['id_tarefa'];
    $direcao = $_POST['direcao'];

    $sql = "SELECT status FROM tarefa WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tarefa);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $status_ordem = ["A fazer", "Fazendo", "Pronto"];
    $status_atual = $row['status'];
    $indice = array_search($status_atual, $status_ordem);

    if ($direcao === 'avancar' && $indice < count($status_ordem) - 1) {
        $indice++;
    } elseif ($direcao === 'retroceder' && $indice > 0) {
        $indice--;
    }
    $novo_status = $status_ordem[$indice];

    $sql = "UPDATE tarefa SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $novo_status, $id_tarefa);
    $stmt->execute();

    header("Location: gerenciador.php");
    exit;
}

if (isset($_POST['excluir'])) {
    $id_tarefa = $_POST['id_tarefa'];
    $sql = "DELETE FROM tarefa WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tarefa);
    $stmt->execute();
    header("Location: gerenciador.php");
    exit;
}

$sql = "SELECT t.id, t.descricao, t.setor, t.prioridade, t.usuario_id, t.status, u.nome AS usuario_nome
        FROM tarefa t
        LEFT JOIN usuario u ON t.usuario_id = u.id";
$result = $conn->query($sql);
$tarefas = ["A fazer" => [], "Fazendo" => [], "Pronto" => []];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tarefas[$row['status']][] = $row;
    }
} else {
    echo "Nenhuma tarefa encontrada.";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gerenciador de Tarefas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        header {
            height: 10vh;
            display: flex;
            align-items: center;
            justify-content: space-around;
            background-color: #0056b3;
            color: #fff;
        }
        header h1 {
            cursor: default;
            font-size: 36px;
        }
        header h3 {
            font-size: 24px;
            cursor: pointer;
        }
        header h3:active{
            transform: translateY(-5px);
            }
        .container {
            display: flex;
            justify-content: space-around;
            height: 83vh;
        }
        .section {
            margin-top: 25px;
            width: 30%;
        }
        .section h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card p {
            margin: 5px 0;
        }
        .buttons {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 10px;
        }
        .buttons button {
            padding: 8px;
            font-size: 18px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit { 
            background-color: #007bff; 
        }
        .delete {
             background-color: #dc3545; 
            }
        .status {
             background-color: #28a745; 
            }
        .retroceder{
             background: orange;
        }
        .editar-form {
            display: none;
            justify-content: space-around;
            flex-wrap: wrap;
            width: 100%;
            margin: 8px;
            font-size: 15px;
        }
        .editar-form input {
            margin: 4px;
            padding: 1px 5px;
            width: 37%;
            font-size: 17px;
        }
        .editar-form select {
            font-size: 15px;
        }
        .editar-form button {
            font-size: 17px;
            background-color: green;
            color: #fff;
            border: none;
            padding: 6px 22px;
        }
        .flex {
            display: flex;
            justify-content: space-around;
        }
        .flex button {
            padding: 8px 12px;
            font-size: 20px;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        button {
            cursor: pointer;
            border-radius: 5px;
        }
        .ajeitar{
            display: flex;
            justify-content: space-around;
            margin: 10px;
            margin-inline: 70px;
        }
        .ajeitar button{
            font-size: 18px;
            background-color: #28a745;
            padding: 8px 8px;
            border: none;
            border-radius: 9px;
            color: #fff;
        }
        .section{
            max-height: 76vh;
            overflow-y: auto;
        }
        footer{
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0056b3;
            color: #fff;
            font-size: 18px;
            height: 7vh;
        }
    </style>
</head>
<body>

<header>
    <h1>Gerenciamento de Tarefas</h1>
    <h3 id="usuarios">Cadastro de usuários</h3>
    <h3 id="tarefa">Cadastro de tarefas</h3>
    <h3 id="gerenciador">Gerenciar tarefas</h3>
</header>

<div class="container">
    <?php foreach ($tarefas as $status => $listaTarefas): ?>
        <div class="section">
            <h2><?= $status ?></h2>
            <?php foreach ($listaTarefas as $tarefa): ?>
                <div class="card">
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($tarefa['descricao']) ?></p>
                    <p><strong>Setor:</strong> <?= htmlspecialchars($tarefa['setor']) ?></p>
                    <p><strong>Prioridade:</strong> <?= htmlspecialchars($tarefa['prioridade']) ?></p>
                    <p><strong>Vinculado ao usuário:</strong> <?= htmlspecialchars($tarefa['usuario_nome']) ?></p>

                    <form action="gerenciador.php" method="POST" class="editar-form" id="form-editar-<?= $tarefa['id'] ?>">
                        <input type="hidden" name="id_tarefa" value="<?= $tarefa['id'] ?>">
                        
                        <input type="text" name="descricao" value="<?= $tarefa['descricao'] ?>" placeholder="Descrição">
                        <input type="text" name="setor" value="<?= $tarefa['setor'] ?>" placeholder="Setor">
                        <select name="prioridade">
                            <option value="Baixo" <?= $tarefa['prioridade'] == 'Baixo' ? 'selected' : '' ?>>Baixo</option>
                            <option value="Médio" <?= $tarefa['prioridade'] == 'Médio' ? 'selected' : '' ?>>Médio</option>
                            <option value="Alta" <?= $tarefa['prioridade'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
                        </select>
                        <button type="submit" name="salvar">Salvar</button>
                    </form>

                    <div class="buttons">
                        <form action="gerenciador.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_tarefa" value="<?= $tarefa['id'] ?>">
                            <input type="hidden" name="direcao" value="retroceder">
                            <button class="retroceder" type="submit" name="mudar_status">Retroceder</button>
                        </form>
                        
                        <form action="gerenciador.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_tarefa" value="<?= $tarefa['id'] ?>">
                            <input type="hidden" name="direcao" value="avancar">
                            <button class="status" type="submit" name="mudar_status">Avançar</button>
                        </form>

                        <form action="gerenciador.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_tarefa" value="<?= $tarefa['id'] ?>">
                            <button class="delete" type="submit" name="excluir">Excluir</button>
                        </form>
                        <button onclick="mostrarFormulario(<?= $tarefa['id'] ?>)" class="edit">Editar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
<footer>
    <h1>@henriquevenzon</h1>
</footer>

<script>
    function mostrarFormulario(id) {
        var form = document.getElementById('form-editar-' + id);
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'flex' : 'none';
    }

    document.getElementById('usuarios').addEventListener('click', function() {
        window.location.href = 'index.php'; 
    });
    document.getElementById('tarefa').addEventListener('click', function() {
        window.location.href = 'tarefa.php'; 
    });
    document.getElementById('gerenciador').addEventListener('click', function() {
        window.location.href = 'gerenciador.php'; 
    });
</script>

</body>
</html>
