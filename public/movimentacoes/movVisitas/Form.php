<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\CADASTRO\DAO\Familias;
use App\MOVIMENTACOES\DAO\Visitas;
use Funcoes\Lib\ViewHelper;
use Funcoes\Lib\Constantes;
use Funcoes\Lib\Traits\TraitFamilia;

class Form extends ViewHelper
{
    use TraitFamilia;

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
                $this->session->flash('error', _('Registro não encontrado'));
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Visitas') . '</h1>',
            L::linkbutton('Voltar', 'visitas.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? _('Nova Visita') : _('Editar Visita')  . ": {$this->aVisita['vis_id']} - {$this->aVisita['vis_titulo']}");
        $this->form->setForm('id="form-visitas" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('vis_id', $this->aVisita['vis_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_titulo = FC::input(
            _('Título'),
            'vis_titulo',
            $this->aVisita['vis_titulo'] ?? '',
            [
                'div_class' => 'col-md-6',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $campo_descricao = FC::textarea('Descrição', 'vis_descricao', $this->aVisita['vis_descricao'] ?? '');

        $familias = $this->buscarFamilias();
        $campo_familias = FC::select2(_('Família'), 'vis_familia_id', $familias, $this->aVisita['vis_familia_id'] ?? '0', ['div_class' => 'col-md-4']);

        $campo_data = FC::date('Data', 'vis_data', $this->aVisita['vis_data'] ?? '');
        $campo_hora = FC::input('Hora', 'vis_hora', $this->aVisita['vis_hora'] ?? '', ['type' => 'time']);

        // Campo para escolher quem vai na visita
        // Membros e Pessoas

        $this->form->setFields([
            ['<div class="row">' . $campo_titulo . $campo_familias . '</div>'],
            [$campo_descricao],
            [$campo_data, $campo_hora],
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
