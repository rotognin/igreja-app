<?php

namespace View\Ministerios\Musica\Grupo;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\MINISTERIOS\MUSICA\DAO\Grupos;
use Funcoes\Lib\GlobalHelper;

class Form extends GlobalHelper
{
    private Grupos $gruposDAO;
    private array $aGrupo = [];
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
        $this->gruposDAO = new Grupos();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aGrupo = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $gru_id = $this->request->get('gru_id', 0);

        if ($gru_id > 0) {
            $this->aGrupo = $this->gruposDAO->get($gru_id);
            $this->novo = false;

            if (empty($this->aGrupo)) {
                $this->session->flash('error', 'Registro não encontrado');
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Grupo</h1>',
            L::linkbutton('Voltar', 'grupos.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Novo Grupo' : 'Editar Grupo'  . ": {$this->aGrupo['gru_id']} - {$this->aGrupo['gru_nome']}");
        $this->form->setForm('id="form-grupos" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('gru_id', $this->aGrupo['gru_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_nome = FC::input(
            'Nome',
            'gru_nome',
            $this->aGrupo['gru_nome'] ?? '',
            [
                'div_class' => 'col-md-5',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $campo_sigla = FC::input(
            'Sigla',
            'gru_sigla',
            $this->aGrupo['gru_sigla'] ?? '',
            [
                'div_class' => 'col-md-1',
                'class' => 'form-control form-control-sm'
            ]
        );

        $campo_observacoes = FC::textarea(
            'Observações',
            'gru_observacoes',
            $this->aGrupo['gru_observacoes'] ?? '',
            [
                'div_class' => 'col-md-8',
                'class' => 'form-control form-control-sm',
                'rows' => '3'
            ]
        );

        $campo_situacao = FC::select('Situação', 'gru_situacao', $this->gruposDAO->getSituacao(), $this->aGrupo['gru_situacao'] ?? 'A', [
            'div_class' => 'col-md-1',
            'class' => 'form-control form-control-sm'
        ]);

        $this->form->setFields([
            ['<div class="row">' . $campo_nome . $campo_sigla . '</div>'],
            ['<div class="row">' . $campo_observacoes . '</div>'],
            ['<div class="row">' . $campo_situacao . '</div>']
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'gru_nome' => 'Informe o Nome'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $('#form-grupos').validate({
                        rules: {
                            gru_nome: {
                                required: true
                            }
                        },
                        messages: {
                            gru_nome: {
                                required: '{$this->aMensagens["gru_nome"]}'
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
            ['title' => 'Cadastro de Grupo']
        );
    }
}
