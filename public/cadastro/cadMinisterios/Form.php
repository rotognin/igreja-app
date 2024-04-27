<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Lib\GlobalHelper;
use App\CADASTRO\DAO\Ministerios;

class Form extends GlobalHelper
{
    private Ministerios $ministeriosDAO;
    private array $aMinisterio = [];
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
        $this->ministeriosDAO = new Ministerios();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aMinisterio = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $min_id = $this->request->get('min_id', 0);

        if ($min_id > 0) {
            $this->aMinisterio = $this->ministeriosDAO->get($min_id);
            $this->novo = false;

            if (empty($this->aMinisterio)) {
                $this->session->flash('error', _('Registro não encontrado'));
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Ministérios</h1>',
            L::linkbutton('Voltar', 'ministerios.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? _('Novo Ministério') : _('Editar Ministério')  . ": {$this->aMinisterio['min_id']} - {$this->aMinisterio['min_nome']}");
        $this->form->setForm('id="form-ministerios" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('min_id', $this->aMinisterio['min_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_nome = FC::input(
            'Nome',
            'min_nome',
            $this->aMinisterio['min_nome'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $campo_sigla = FC::input(
            'Sigla',
            'min_sigla',
            $this->aMinisterio['min_sigla'] ?? '',
            [
                'div_class' => 'col-md-1',
                'class' => 'form-control form-control-sm',
                'maxlength' => '3',
                'style' => 'text-transform:uppercase'
            ]
        );

        $campo_ativo = FC::select('Ativo', 'min_ativo', ['S' => 'Sim', 'N' => 'Não'], $this->aMinisterio['min_ativo'] ?? 'S', [
            'div_class' => 'col-md-1',
            'class' => 'form-control form-control-sm'
        ]);

        $this->form->setFields([
            ['<div class="row">' . $campo_nome . $campo_sigla . $campo_ativo . '</div>']
        ]);

        $this->form->setActions(L::submit(_('Salvar')));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'min_nome'     => 'Informe o Nome do Ministério',
            'min_sigla' => 'Informe uma Sigla para o Ministério'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $('#form-ministerios').validate({
                        rules: {
                            min_nome: {
                                required: true
                            },
                            min_sigla: {
                                required: true
                            }
                        },
                        messages: {
                            min_nome: {
                                required: '{$this->aMensagens["min_nome"]}'
                            },
                            min_sigla: {
                                required: '{$this->aMensagens["min_sigla"]}'
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
            ['title' => 'Cadastro de Ministério']
        );
    }
}
