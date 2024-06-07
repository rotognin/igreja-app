<?php

require_once("headerLogin.php");
$user = $authManager->get('default');
if ($user) {
    $response->redirect("/dashboard");
}

$posicao = $request->get('posicao', 'form');

if ($posicao == 'form') {
    global $config;
    echo $response->loginPage(
        <<<HTML
        <div class="login-box">
            <div class="card card-outline card-success">
                <div class="card-header text-center">
                    <b class="h1">
                        <p>{$config->get('app.title', 'Igreja Batista')}</p>
                        <img class="brand-image" src="/assets/img/logo-login.png" alt="Logo">
                    </b>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Faça login para iniciar sua sessão</p>
                    <form id="login-form" action="?posicao=login" method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Usuário" name="usuario" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text"> 
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" placeholder="Senha" name="senha">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 d-flex align-items-center">
                                <!--a href="forgot-password.html">Esqueci minha senha</a-->
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-success btn-block">Entrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            $(function() {
                $('#login-form').validate({
                    rules: {
                        usuario: {
                            required: true,
                        },
                        senha: {
                            required: true,
                        }
                    },
                    messages: {
                        usuario: {
                            required: "Informe o usuário"
                        },
                        senha: {
                            required: "Informe a senha"
                        }
                    },
                    invalidHandler: function(form, validator){
                        $('#overlay').remove();
                    }
                });
            });
        </script>
        HTML,
        ['title' => 'Login']
    );
} elseif ($posicao == 'login') {
    $dao = new App\SGC\DAO\Usuario();
    $empresaDAO = new App\SGC\DAO\Empresa();

    $usuario = App\SGC\Usuario::auth($request->post('usuario'), $request->post('senha'));

    if (!$usuario) {
        $session->flash('error', 'Usuário ou senha inválidos');
        return $response->back();
    }

    if ($usuario['usu_ativo'] != 'S') {
        $session->flash('error', "Usuário inativo");
        return $response->back();
    }

    xdebug_break();

    $session->set('credentials', array(
        'default' => $usuario['usu_login'],
        'name'    => $usuario['usu_nome']
    ));

    $session->set('establishment', $empresaDAO->get($usuario['emp_codigo']));
    $response->redirect('/dashboard');
}
