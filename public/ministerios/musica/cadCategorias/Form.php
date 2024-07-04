<?php

namespace View\Ministerios\Musica\Categoria;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\MINISTERIOS\MUSICA\DAO\Categorias;
use Funcoes\Lib\GlobalHelper;

class Form extends GlobalHelper
{
    private Categorias $categoriasDAO;
    private array $aCategoria = [];
    private string $cabecalho;
    private Formulario $form;
    private bool $novo = true;
    private string $script;
    private array $aMensagens;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        if (!$this->existeRetorno()) {
            $this->checarID();
        }

        $this->montarCabecalho();
        $this->inicioForm();
        $this->montarCampos();
        $this->montarMensagens();
        $this->montarScript();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->categoriasDAO = new Categorias();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aCategoria = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $cam_id = $this->request->get('cam_id', 0);

        if ($cam_id > 0) {
            $this->aCategoria = $this->categoriasDAO->get($cam_id);
            $this->novo = false;

            if (empty($this->aCategoria)) {
                $this->session->flash('error', 'Registro não encontrado');
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Categoria de Músicas</h1>',
            L::linkbutton('Voltar', 'categorias.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Nova Categoria' : 'Editar Categoria'  . ": {$this->aCategoria['cam_id']} - {$this->aCategoria['cam_descricao']}");
        $this->form->setForm('id="form-categorias" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('cam_id', $this->aCategoria['cam_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_descricao = FC::input(
            'Descrição',
            'cam_descricao',
            $this->aCategoria['cam_descricao'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $this->form->setFields([
            ['<div class="row">' . $campo_descricao . '</div>']
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'cam_descricao' => 'Informe a Descrição'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $('#form-categorias').validate({
                        rules: {
                            cam_descricao: {
                                required: true
                            }
                        },
                        messages: {
                            cam_descricao: {
                                required: '{$this->aMensagens["cam_descricao"]}'
                            }
                        },
                        invalidHandler: function(form, validator){
                            $('#overlay').remove();
                        }
                    });
                });
            </script>
        HTML;
    }

    private function saidaPagina()
    {
        $this->response->page(
            <<<HTML
                {$this->cabecalho}
                <div class="content">
                    <div class="container-fluid pb-1">
                        {$this->form->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Cadastro de Categoria de Música']
        );
    }
}
