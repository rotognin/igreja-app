<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\CADASTRO\DAO\Membros;
use App\CADASTRO\DAO\Familias;
use Funcoes\Lib\ViewHelper;
use Funcoes\Lib\Constantes;

class Form extends ViewHelper
{
    private Membros $membrosDAO;
    private Familias $familiasDAO;
    private array $aMembro = [];
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
        $this->membrosDAO = new Membros();
        $this->familiasDAO = new Familias();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aMembro = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $mem_id = $this->request->get('mem_id', 0);

        if ($mem_id > 0) {
            $this->aMembro = $this->membrosDAO->get($mem_id);
            $this->novo = false;

            if (empty($this->aMembro)) {
                $this->session->flash('error', _('Registro não encontrado'));
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Membros') . '</h1>',
            L::linkbutton('Voltar', 'membros.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? _('Novo Membro') : _('Editar Membro')  . ": {$this->aMembro['mem_id']} - {$this->aMembro['mem_nome']}");
        $this->form->setForm('id="form-membros" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('mem_id', $this->aMembro['mem_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_nome = FC::input(
            _('Nome'),
            'mem_nome',
            $this->aMembro['mem_nome'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );
        $campo_telefone = FC::input(
            _('Telefone'),
            'mem_telefone',
            $this->aMembro['mem_telefone'] ?? '',
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_email = FC::input(
            _('E-mail'),
            'mem_email',
            $this->aMembro['mem_email'] ?? '',
            [
                'div_class' => 'col-md-4',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_endereco = FC::input(
            _('Endereço'),
            'mem_endereco',
            $this->aMembro['mem_endereco'] ?? '',
            [
                'div_class' => 'col-md-4',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_numero = FC::input(
            _('Número'),
            'mem_numero',
            $this->aMembro['mem_numero'] ?? '',
            [
                'div_class' => 'col-md-1',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_bairro = FC::input(
            _('Bairro'),
            'mem_bairro',
            $this->aMembro['mem_bairro'] ?? '',
            [
                'div_class' => 'col-md-3',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_complemento = FC::input(
            _('Complemento'),
            'mem_complemento',
            $this->aMembro['mem_complemento'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_cep = FC::input(
            _('CEP'),
            'mem_cep',
            $this->aMembro['mem_cep'] ?? '',
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_cidade = FC::input(
            _('Cidade'),
            'mem_cidade',
            $this->aMembro['mem_cidade'] ?? '',
            [
                'div_class' => 'col-md-4',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $arrayEstados = array_merge(['' => 'Selecione'], Constantes::obterEstado());
        $campo_estado = FC::select(
            _('Estado'),
            'mem_estado',
            $arrayEstados,
            $this->aMembro['mem_estado'] ?? '',
            [
                'div_class' => 'col-md-3',
                'class' => 'form-control form-control-sm'
            ]
        );

        $familias = $this->montarArrayFamilias();
        $campo_familia = FC::select(_('Família'), 'mem_familia_id', $familias, $this->aMembro['mem_familia_id'] ?? '0', ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-3']);

        $this->form->setFields([
            ['<div class="row">' . $campo_nome . $campo_telefone . $campo_email . '</div>'],
            ['<div class="row">' . $campo_endereco . $campo_numero . $campo_bairro . '</div>'],
            ['<div class="row">' . $campo_complemento . '</div>'],
            ['<div class="row">' . $campo_cep . $campo_cidade . $campo_estado . '</div>'],
            ['<div class="row">' . $campo_familia . '</div>']
        ]);

        $this->form->setActions(L::submit(_('Salvar')));
    }

    private function montarArrayFamilias(): array
    {
        $array = $this->familiasDAO->getArray();
        $familias = array();

        if ($array) {
            foreach ($array as $familia) {
                $familias[$familia['fam_id']] = $familia['fam_nome'];
            }
        }

        return array_merge(['0' => ''], $familias);
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'mem_nome'     => _('Informe o Nome'),
            'mem_telefone' => _('Informe o Telefone'),
            'mem_email'    => _('E-mail inválido')
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $("#mem_cep").keypress(function(){
                        var cep = $("#mem_cep").val();
                        cep = formatar_cep(cep);
                        $("#mem_cep").val(cep);
                    });

                    $('#form-membros').validate({
                        rules: {
                            mem_nome: {
                                required: true
                            },
                            mem_telefone: {
                                required: true
                            },
                            mem_email: {
                                email: true
                            }
                        },
                        messages: {
                            mem_nome: {
                                required: '{$this->aMensagens["mem_nome"]}'
                            },
                            mem_telefone: {
                                required: '{$this->aMensagens["mem_telefone"]}'
                            },
                            mem_email: {
                                email: '{$this->aMensagens["mem_email"]}'
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
            ['title' => 'Cadastro de Membro']
        );
    }
}
