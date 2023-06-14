<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\CADASTRO\DAO\Familias;
use Funcoes\Lib\ViewHelper;
use Funcoes\Lib\Constantes;

class Form extends ViewHelper
{
    private Familias $familiasDAO;
    private array $aFamilia = [];
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
        $this->familiasDAO = new Familias();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aFamilia = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $fam_id = $this->request->get('fam_id', 0);

        if ($fam_id > 0) {
            $this->aFamilia = $this->familiasDAO->get($fam_id);
            $this->novo = false;

            if (empty($this->aFamilia)) {
                $this->session->flash('error', _('Registro não encontrado'));
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Famílias') . '</h1>',
            L::linkbutton('Voltar', 'familias.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? _('Nova Família') : _('Editar Família')  . ": {$this->aFamilia['fam_id']} - {$this->aFamilia['fam_nome']}");
        $this->form->setForm('id="form-familias" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('fam_id', $this->aFamilia['fam_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_nome = FC::input(
            _('Nome'),
            'fam_nome',
            $this->aFamilia['fam_nome'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );
        $campo_observacao = FC::textarea(
            _('Observação'),
            'fam_observacao',
            $aFamilia['fam_observacao'] ?? '',
            [
                'class' => 'form-control form-control-sm',
                'style' => 'text-transform:uppercase'
            ]
        );

        $this->form->setFields([
            ['<div class="row">' . $campo_nome . '</div>'],
            [$campo_observacao]
        ]);

        $this->form->setActions(L::submit(_('Salvar')));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'fam_nome' => _('Informe o Nome')
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $('#form-familias').validate({
                        rules: {
                            fam_nome: {
                                required: true
                            }
                        },
                        messages: {
                            fam_nome: {
                                required: '{$this->aMensagens["fam_nome"]}'
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
            ['title' => 'Cadastro de Família']
        );
    }
}
