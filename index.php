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
                cursor: default;
                font-size: 36px;
            }
            header h3{
                font-size: 24px;
                cursor: pointer;
                transition: 0.3s;
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
                height: 55vh;
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
                margin-bottom: 3vh;
                margin-top: 3vh;
                font-size: 22px;
            }
            .caixa input{
                font-size: 20px;
                width: 60%;
                padding: 5px;
                transition: 0.7s;
            }
            .caixa input:focus{
                transform: scale(1.05);
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
                transition: 0.2s;
           }
           .caixa button:hover{
                background-color: #0056b3;
                color: #fff;
                box-shadow: 0 0 8px #000;
           }
           .caixa button:active{
                transform: translateY(-7px)
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
            margin-top: 480px;
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
                <h1>Cadastro de Usuário</h1>
                <form id="cadastroForm" onsubmit="enviarFormulario(event)">
                <label for="nome">Nome:</label>
                <input id="nome" type="text" name="nome">
                <label for="email">Email:</label>
                <input id="email" type="email" name="email">
                <button>ENVIAR</button>
                </form>
                <p id="mensagem"></p>
            </div>
        </div>
        <footer>
            <h1>@henriquevenzon</h1>
        </footer>
        <script>
                function enviarFormulario(event) {
                    event.preventDefault(); 
                    
                    const nome = document.getElementById('nome').value;
                    const email = document.getElementById('email').value;
                    const mensagem = document.getElementById('mensagem');

                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "processa_cadastro.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            mensagem.innerText = xhr.responseText;
                            mensagem.style.color = "green"; 
                        } else {
                            mensagem.innerText = "Erro ao cadastrar usuário.";
                            mensagem.style.color = "red"; 
                        }
                    };

                    xhr.send("nome=" + encodeURIComponent(nome) + "&email=" + encodeURIComponent(email));
                }
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