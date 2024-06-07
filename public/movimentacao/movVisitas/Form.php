<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\CADASTRO\DAO\Familias;
use App\MOVIMENTACOES\DAO\Visitas;
use Funcoes\Lib\GlobalHelper;
use Funcoes\Helpers\Format;
use Funcoes\Lib\Traits\TraitFamilia;
use Funcoes\Lib\Traits\TraitPessoas;
use Funcoes\Lib\Traits\TraitVisitaIntegrantes;

class Form extends GlobalHelper
{
    use TraitFamilia, TraitPessoas, TraitVisitaIntegrantes;

    private Visitas $visitasDAO;
    private Familias $familiasDAO;
    private string $cabecalho;
    private array $aVisita;
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
        $this->visitasDAO = new Visitas();
        $this->familiasDAO = new Familias();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aVisita = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $vis_id = $this->request->get('vis_id', 0);

        if ($vis_id > 0) {
            $this->aVisita = $this->visitasDAO->get($vis_id);
            $this->novo = false;

            if (empty($this->aVisita)) {
                $this->session->flash('error', 'Registro não encontrado');
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Visita</h1>',
            L::linkbutton('Voltar', 'visitas.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Nova Visita' : 'Editar Visita'  . ": {$this->aVisita['vis_id']} - {$this->aVisita['vis_titulo']}");
        $this->form->setForm('id="form-visitas" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('vis_id', $this->aVisita['vis_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_titulo = FC::input(
            'Título',
            'vis_titulo',
            $this->aVisita['vis_titulo'] ?? '',
            [
                'div_class' => 'col-md-6',
                'class' => 'form-control form-control-sm',

                'autofocus' => 'autofocus'
            ]
        );

        $campo_descricao = FC::textarea('Descrição', 'vis_descricao', $this->aVisita['vis_descricao'] ?? '', [
            'div_class' => 'col-md-6', 'class' => 'form-control form-control-sm', 'rows' => '3',
        ]);

        $campo_quem = FC::input('Quem vai receber a visita?', 'vis_quem', $this->aVisita['vis_quem'] ?? '', [
            'div_class' => 'col-md-6', 'class' => 'form-control form-control-sm',
        ]);

        $familias = $this->buscarFamilias('Selecione');
        $campo_familias = FC::select2('Família', 'vis_familia_id', $familias, $this->aVisita['vis_familia_id'] ?? '0', [
            'class' => 'form-control form-control-sm', 'div_class' => 'col-md-4'
        ]);

        $campo_data = FC::input('Data', 'vis_data', Format::date($this->aVisita['vis_data'] ?? ''), [
            'div_class' => 'col-md-2', 'class' => 'form-control form-control-sm data-mask'
        ]);
        $campo_hora = FC::input('Hora', 'vis_hora', $this->aVisita['vis_hora'] ?? '', [
            'div_class' => 'col-md-2', 'class' => 'form-control form-control-sm hora-mask'
        ]);

        $campo_local = FC::input('Onde será a visita?', 'vis_local', $this->aVisita['vis_local'] ?? '', [
            'div_class' => 'col-md-6', 'class' => 'form-control form-control-sm',
        ]);

        $pessoas = $this->buscarPessoas();
        $campo_pessoas_visitantes = FC::select2('Pessoas Visitantes', 'vis_pessoas[]', $pessoas, '', [
            'multiple' => 'multiple',
            'id' => 'vis_pessoas_select',
            'div_class' => 'col-md-10'
        ]);

        $campo_observacoes = FC::textarea('Observações', 'vis_observacao', $this->aVisita['vis_observacao'] ?? '', [
            'div_class' => 'col-md-6', 'class' => 'form-control form-control-sm', 'rows' => '3',
        ]);

        $this->form->setFields([
            ['<div class="row">' . $campo_titulo . $campo_quem . $campo_familias . '</div>'],
            ['<div class="row">' . $campo_descricao . '</div>'],
            ['<div class="row">' . $campo_data . $campo_hora . $campo_local . '</div>'],
            ['<div class="row">' . $campo_pessoas_visitantes . '</div>'],
            ['<div class="row">' . $campo_observacoes . '</div>']
        ]);

        $this->form->setActions(L::submit(_('Salvar')));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'vis_titulo'   => _('Informe o Título')
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $('.data-mask').mask('00/00/0000');
                $('.hora-mask').mask('00:00');

                $(function(){
                    $('#form-visitas').validate({
                        rules: {
                            vis_titulo: {
                                required: true
                            }
                        },
                        messages: {
                            vis_titulo: {
                                required: '{$this->aMensagens["vis_titulo"]}'
                            }
                        },
                        invalidHandler: function(form, validator){
                            $('#overlay').remove();
                        }
                    });
                });
            </script>
        HTML;

        if (!$this->novo) {
            $this->montarScriptCarregarDados();
        }
    }

    private function montarScriptCarregarDados()
    {
        $integrantes = $this->obterVisitantes($this->aVisita['vis_id']);

        if (empty($integrantes)) {
            return false;
        }

        $pessoas = '';

        foreach ($integrantes as $integrante) {
            $pessoas .= $integrante['vin_pessoa_id'] . ', ';
        }

        $addScript = "$('#vis_pessoas_select').val([{$pessoas}]).trigger('change');";

        $script = <<<HTML
            <script>
                $(function(){
                    {$addScript}
                });
            </script>
        HTML;

        $this->script .= $script;
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
            ['title' => 'Cadastro de Visita']
        );
    }
}
