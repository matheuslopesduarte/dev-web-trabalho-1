<?php
include $_SERVER['DOCUMENT_ROOT'] . '\templates\main.php';
$endereco = $_SERVER['HTTP_HOST'];
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$url = $protocolo . $endereco;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#cadastro').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $url ?>/fx/cadastro-verify.php',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response === "Error: Email") {
                            alert('Email já cadastrado')
                            document.getElementById('loading-screen').style.display = 'none';
                        } else if (response === "Error: Username") {
                            alert('Usuario já cadastrado')
                            document.getElementById('loading-screen').style.display = 'none';
                        }

                        else if (response === "Email envied") {
                            document.getElementById('loading-screen').style.display = 'none';
                            $('#div-code').show();
                            $('#div-cadastro').hide();
                        }
                        else {
                            alert(response);
                            document.getElementById('loading-screen').style.display = 'none';
                        }

                    }
                });
            });

            $('#code-form').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $url ?>/fx/cadastro-check.php',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response === 'email armazened') {
                            alert('Email verificado');
                            window.location.href = "<?php echo $url . '/users/login.php' ?>"
                        } else {
                            alert(response);
                            document.getElementById('loading-screen').style.display = 'none';
                        }
                    }
                });
            });
        });
    </script>
</head>
<style>
    .register-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    .register-container h1 {
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
    }

    .register-form {
        display: flex;
        flex-direction: column;
    }

    .form-group {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .form-group input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .register-btn {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
        align-self: center;
    }

    .register-btn:hover {
        background-color: #0056b3;
    }
</style>

<body>
<div class="register-container" id="div-code" style="display:none;">
    <form id="code-form" class="register-form" >
    <div class="form-group">
        <label for="code">Digite o codigo enviado ao seu email:</label>
        <input type="text" id="code" name="code" required>
    </div>
        <input class="register-btn" type="submit" value="Submit">
    </form>
</div>

    <div class="register-container" id="div-cadastro" >
        <h1>Cadastro</h1>
        <form id="cadastro" class="register-form" onsubmit="showLoadingScreen()">
            <div class="form-group">
                <label for="fullname">Nome Completo:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Digite seu nome completo" required>
            </div>
            <div class="form-group">
                <label for="username">Nome de Usuário:</label>
                <input type="text" id="username" name="username" placeholder="Digite seu nome de usuário" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu email" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            <button class="register-btn" type="submit">Cadastrar</button>
            <a href="<?php $url ?>/users/login.php" style="color: #555555; margin: 10px auto 0 auto;">Login</a>
        </form>
    </div>

</body>

</html>