<?php

namespace Funcoes\Lib;

class Response
{
    private array $javascript = [];
    private array $css = [];

    protected function beginHTML($params = [])
    {
        global $config;

        $title = $params['title'] ? "{$params['title']} :: " : '';
        $title .= $config->get('module.title') ? "{$config->get('module.title')} - " : '';
        $title .= $config->get('app.title');

        $css = $this->css ? implode(PHP_EOL, $this->css) : '';
        $javascript = $this->javascript ? implode(PHP_EOL, $this->javascript) : '';

        $toasts = \Funcoes\Layout\Toasts::json();

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="pt">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
            <title>$title</title>
            $css
            $javascript
            <script>
                toasts = $toasts;
            </script>
        </head>
        HTML;
    }

    protected function endHTML($params = [])
    {
        echo <<<HTML
        </body>
        </html>
        HTML;
    }

    public function defaultJS()
    {
        $this->javascript[0] = '<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js"></script>';
        $this->javascript[1] = "<script src=\"https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js\"></script>";
        $this->javascript[2] = '<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>';
        $this->javascript[10] = '<script src="https://kit.fontawesome.com/3f05d42f08.js" crossorigin="anonymous"></script>';
        $this->javascript[20] = '<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        $this->javascript[30] = '<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>';
        $this->javascript[80] = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>';
        $this->javascript[100] = '<script src="/assets/js/main.js"></script>';
    }

    public function defaultCSS()
    {
        $this->css[10] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icheck-bootstrap@3.0.1/icheck-bootstrap.min.css">';
        $this->css[20] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">';
        $this->css[110] = '<link rel="stylesheet" href="/assets/css/compact.css">';
    }

    public function loginPage($content, $params = [])
    {
        $this->defaultCSS();
        $this->defaultJS();

        $this->beginHTML($params);

        echo <<<HTML
        <body class="login-page">
            $content
        HTML;

        $this->endHTML($params);
    }

    public function page($content, $params)
    {
        global $config;

        $this->defaultCSS();
        $this->defaultJS();

        $this->css[30] = '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/fc-4.1.0/fh-3.2.4/r-2.3.0/sl-1.4.0/datatables.min.css"/>';
        $this->css[31] = '<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />';
        $this->css[32] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">';
        $this->css[33] = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';
        $this->css[34] = '<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />';
        $this->css[100] = '<link rel="stylesheet" type="text/css" href="/assets/css/main.css">';

        $this->javascript[31] = '<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/fc-4.1.0/fh-3.2.4/r-2.3.0/sl-1.4.0/datatables.min.js"></script>';
        $this->javascript[32] = '<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>';
        $this->javascript[33] = '<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment-with-locales.min.js"></script>';
        $this->javascript[34] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
        $this->javascript[35] = '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>';

        $this->beginHTML($params);

        $header_links = "";
        foreach ($config->get('module.header_links', []) as $link) {
            $header_links .= <<<HTML
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{$link['href']}" class="nav-link">
                    <i class="{$link['icon']}"></i> {$link['title']}
                </a>
            </li>
            HTML;
        }

        $year = date('Y');

        // Usuário logado
        global $activeUser;

        $establishmentSelector = \Funcoes\Layout\EstablishmentSelector::render();
        $notificationWidget = \Funcoes\Layout\NotificationWidget::render();
        $navMenu = \Funcoes\Layout\NavMenu::render();
        echo <<<HTML
        <body class="layout-navbar-fixed sidebar-mini layout-fixed d3v-compact">
            <div class="wrapper">
                <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                        </li>
                        $header_links
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        $establishmentSelector
                        $notificationWidget
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                <i class="far fa-user-circle"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-item">
                                    <small class="text-muted">
                                        Usuário logado
                                    </small><br>
                                    {$activeUser->getUsername()}
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/sgc/logout.php" role="button">
                                    <i class="fas fa-sign-out-alt"></i> Encerrar sessão
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
                    <a class="brand-link text-center" href="/" style="background-color: white; padding:0px; border-bottom: solid 3px #28A745">
                        <img class="rounded mx-auto d-block" src="/assets/img/igreja.png" alt="Logo Interno">
                    </a>
                    <div class="sidebar">
                        <div class="form-inline mt-2">
                            <!--div class="input-group" data-widget="sidebar-search">
                                <input class="form-control form-control-sidebar" type="search" placeholder="Pesquisar programas" aria-label="Pesquisar programas">
                                <div class="input-group-append">
                                    <button class="btn btn-sidebar">
                                        <i class="fas fa-search fa-fw"></i>
                                    </button>
                                </div>
                            </div-->
                        </div>
                        $navMenu
                    </div>
                </aside>
                <div class="content-wrapper">
                    $content
                </div>
                <footer class="main-footer">
                    <strong>Copyright &copy; $year {$config->get('app.title')}. Todos os direitos reservados.</strong>
                    <div class="float-right d-none d-sm-block">
                        <strong>Desenvolvido por: <a href="https://rodrigotognin.com.br" target="_blank">Tognin Sistemas</a>.</strong>
                    </div>
                </footer>
            </div>
        HTML;
        $this->endHTML($params);
    }

    public function checkAction($action, $msg = "Você não tem direito de executar esta ação")
    {
        global $activeUser;
        global $session;

        if (!$activeUser->checkAction($action)) {
            $session->flash('warning', $msg);
            $this->back();
        }
    }

    public function back(int $steps = -1)
    {
        echo "<script>window.history.go($steps);</script>";
        exit;
    }

    public function redirect($url, $httpStatusCode = 302)
    {
        header("Location: $url", true, $httpStatusCode);
        exit;
    }

    public function replace($url)
    {
        echo "<script>window.location.replace('$url');</script>";
        exit;
    }

    public function json($httpStatusCode, $record)
    {
        header('Content-type: application/json');
        http_response_code($httpStatusCode);
        echo json_encode($record);
        exit;
    }
}
