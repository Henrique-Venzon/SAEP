<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Gerenciador de Tarefas</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <style>
            *{
                padding: 0;
                margin: 0;
                box-sizing: border-box;
            }
            header{
                height: 10vh;
                display: flex;
                align-items: center;
                justify-content: space-around;
                background-color: #0056b3;
                color: #fff;
            }
            header h1{
                font-size: 36px;
                cursor: default;
            }
            header h3{
                font-size: 24px;
                cursor: pointer;
            }
            header h3:active{
                transform: translateY(-5px);
            }
            .container{
                display: flex;
                justify-content: center;
                align-items: center;
                height: 83vh;
                width: 100%;
            }
            .caixa{
                height: 62vh;
                width: 28vw;
                background-color: #fff;
                box-shadow: 0 0 10px #000;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                border-radius: 40PX;
            }
            .caixa h1{
                margin-top: -1vh;
                margin-bottom: 3vh;
                font-size: 36px;
            }
            .caixa label{
                margin-bottom: 12px;
                margin-top: 12px;
                font-size: 22px;
            }
            .caixa input{
                font-size: 18px;
                width: 60%;
                padding: 5px;
            }
           .caixa button{
                margin-top: 40px;
                width: 190px;
                font-size: 20px;
                padding: 4px 8px;
                background-color: #fff;
                border: 1px solid #000;
                border-radius: 10px;
                cursor: pointer;
           }
           .caixa button:hover{
                background-color: #0056b3;
                color: #fff;
                box-shadow: 0 0 8px #000;
           }
           .caixa button:active{
                transform: translateY(-7px)
           }
           .caixa select{
            font-size: 18px;
           }
           form{
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                width: 100%;
           }
           #mensagem{
            position: absolute;
            margin-top: 500px;
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
        <header>
            <h1>Gerenciamento de Tarefas</h1>
            <h3 id="usuarios">Cadastro de usuários</h3>
            <h3 id="tarefa">Cadastro de tarefas</h3>
            <h3 id="gerenciador">Gerenciar tarefas</h3>
        </header>
        <div class="container">
            <div class="caixa">
                <h1>Cadastro de Tarefas</h1>
                <form id="cadastroTarefa" onsubmit="enviarTarefa(event)">
                    <label for="descricao">Descrição:</label>
                    <input id="descricao" type="text" name="descricao" required>
                    <label for="setor">Setor:</label>
                    <input id="setor" type="text" name="setor" required>
                    <label for="usuario">Usuário:</label>
                    <select id="usuario" required>
                        <option value="">Selecione um usuário</option>
                    </select>
                    <label for="prioridade">Prioridade:</label>
                    <select id="prioridade" name="prioridade" required>
                        <option value="alta">Alta</option>
                        <option value="media">Média</option>
                        <option value="baixa">Baixa</option>
                    </select>
                    <button type="submit">ENVIAR</button>
                </form>
                <p id="mensagem"></p>
            </div>
        </div>
        <footer>
            <h1>@henriquevenzon</h1>
        </footer>
        <script>
        window.onload = function() {
            fetch('carregar_usuarios.php')
                .then(response => response.json())
                .then(data => {
                    const selectUsuario = document.getElementById('usuario');
                    data.forEach(usuario => {
                        const option = document.createElement('option');
                        option.value = usuario.id;
                        option.textContent = usuario.nome;
                        selectUsuario.appendChild(option);
                    });
                })
                .catch(error => console.error('Erro ao carregar usuários:', error));
        };
        </script>
        <script>
        let isSubmitting = false;

        function enviarTarefa(event) {
            event.preventDefault(); 

            if (isSubmitting) return;
            isSubmitting = true;

            const descricao = document.getElementById('descricao').value;
            const setor = document.getElementById('setor').value;
            const usuario_id = document.getElementById('usuario').value;
            const prioridade = document.getElementById('prioridade').value;
            const mensagem = document.getElementById('mensagem');
            const button = document.querySelector("button");

            if (!usuario_id) {
                mensagem.innerText = "Por favor, selecione um usuário.";
                mensagem.style.color = "red";
                isSubmitting = false; 
                return;
            }

            button.disabled = true; 

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "processa_tarefa.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    mensagem.innerText = xhr.responseText;
                    mensagem.style.color = "green";
                } else {
                    mensagem.innerText = "Erro ao cadastrar tarefa.";
                    mensagem.style.color = "red";
                }
                button.disabled = false;
                isSubmitting = false;
            };

            xhr.send("descricao=" + encodeURIComponent(descricao) +
                    "&setor=" + encodeURIComponent(setor) +
                    "&usuario_id=" + encodeURIComponent(usuario_id) +
                    "&prioridade=" + encodeURIComponent(prioridade));
        }

        document.querySelector("form").addEventListener("submit", enviarTarefa);


        </script>

        <script>
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