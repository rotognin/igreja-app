<?php

namespace View\Patrimonio;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Lib\GlobalHelper;
use App\PATRIMONIO\DAO\CategoriaPatrimonio;

class Form extends GlobalHelper
{
    private CategoriaPatrimonio $categoriaDAO;
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
        $this->categoriaDAO = new CategoriaPatrimonio();
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
        $cpa_id = $this->request->get('cpa_id', 0);

        if ($cpa_id > 0) {
            $this->aCategoria = $this->categoriaDAO->get($cpa_id);
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
            '<h1 class="m-0 text-dark">Cadastro de Categoria</h1>',
            L::linkbutton('Voltar', 'categorias.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Nova Categoria' : 'Editar Categoria'  . ": {$this->aCategoria['cpa_id']}");
        $this->form->setForm('id="form-categorias" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('cpa_id', $this->aCategoria['cpa_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_titulo = FC::input(
            'Título',
            'cpa_titulo',
            $this->aCategoria['cpa_titulo'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $campo_descricao = FC::input(
            'Descrição',
            'cpa_descricao',
            $this->aCategoria['cpa_descricao'] ?? '',
            [
                'div_class' => 'col-md-10',
                'class' => 'form-control form-control-sm',
                'style' => 'text-transform:uppercase'
            ]
        );

        $campo_ativo = FC::select('Ativo', 'cpa_ativo', ['S' => 'Sim', 'N' => 'Não'], $this->aCategoria['cpa_ativo'] ?? 'S', [
            'div_class' => 'col-md-1',
            'class' => 'form-control form-control-sm'
        ]);

        $this->form->setFields([
            ['<div class="row">' . $campo_titulo . '</div>'],
            ['<div class="row">' . $campo_descricao . '</div>'],
            ['<div class="row">' . $campo_ativo . '</div>']
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'cpa_titulo'    => 'Informe o Título',
            'cpa_descricao' => 'Informe a Descrição'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $('#form-categorias').validate({
                        rules: {
                            cpa_titulo: {
                                required: true
                            },
                            cpa_descricao: {
                                required: true
                            }
                        },
                        messages: {
                            cpa_titulo: {
                                required: '{$this->aMensagens["cpa_titulo"]}'
                            },
                            cpa_descricao: {
                                required: '{$this->aMensagens["cpa_descricao"]}'
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
            ['title' => 'Cadastro de Categoria']
        );
    }
}
