<?php

namespace View\Ministerios\Musica\Escala;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\MINISTERIOS\MUSICA\DAO\Escalas;
use Funcoes\Lib\GlobalHelper;
use Funcoes\Helpers\Format;

class Form extends GlobalHelper
{
    private Escalas $escalasDAO;
    private array $aEscala = [];
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
        $this->escalasDAO = new Escalas();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aEscala = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $esc_id = $this->request->get('esc_id', 0);

        if ($esc_id > 0) {
            $this->aEscala = $this->escalasDAO->get($esc_id);
            $this->novo = false;

            if (empty($this->aEscala)) {
                $this->session->flash('error', 'Registro não encontrado');
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Escala</h1>',
            L::linkbutton('Voltar', 'escalas.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Nova Escala' : "Editar Escala: {$this->aEscala['esc_id']}");
        $this->form->setForm('id="form-escalas" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('esc_id', $this->aEscala['esc_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_titulo = FC::input(
            'Título',
            'esc_titulo',
            $this->aEscala['esc_titulo'] ?? '',
            [
                'div_class' => 'col-md-6',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $esc_data = '';

        if (!$this->novo) {
            $esc_data = Format::date($this->aEscala['esc_data']);
        }

        $campo_data = FC::input(
            'Data',
            'esc_data',
            $esc_data,
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm data-mask'
            ]
        );

        $campo_hora = FC::input(
            'Hora',
            'esc_hora',
            $this->aEscala['esc_hora'] ?? '',
            [
                'div_class' => 'col-md-1',
                'class' => 'form-control form-control-sm hora-mask'
            ]
        );

        $this->form->setFields([
            ['<div class="row">' . $campo_titulo . '</div>'],
            ['<div class="row">' . $campo_data . $campo_hora . '</div>']
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'esc_titulo' => 'Informe o Título',
            'esc_data' => 'Informe a Data'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $('.data-mask').mask('00/00/0000');
                $('.hora-mask').mask('00:00');

                $(function(){
                    $.validator.addMethod("validarData", function(esc_data){
                        if (esc_data == '' || esc_data == undefined){
                            return false;
                        }

                        if (!dataValida(esc_data)){
                            return false;
                        }

                        return true;

                    }, "Informe uma Data válida");

                    $.validator.addMethod("validarHora", function(esc_hora){
                        if (esc_hora == ''){
                            return true;
                        }

                        if (!horaValida(esc_hora)){
                            return false;
                        }

                        return true;
                    }, "Hora incorreta")

                    $('#form-escalas').validate({
                        rules: {
                            esc_titulo: {
                                required: true
                            },
                            esc_data: "validarData",
                            esc_hora: "validarHora"
                        },
                        messages: {
                            esc_titulo: {
                                required: '{$this->aMensagens["esc_titulo"]}'
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
            ['title' => 'Cadastro de Escala']
        );
    }
}
