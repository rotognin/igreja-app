<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\CADASTRO\DAO\Pessoas;
use App\CADASTRO\DAO\Familias;
use Funcoes\Lib\ViewHelper;
use Funcoes\Lib\Constantes;
use Funcoes\Lib\Traits\TraitFamilia;

class Form extends ViewHelper
{
    use TraitFamilia;

    private Pessoas $pessoasDAO;
    private Familias $familiasDAO;
    private array $aPessoa = [];
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
        $this->pessoasDAO = new Pessoas();
        $this->familiasDAO = new Familias();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aPessoa = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $pes_id = $this->request->get('pes_id', 0);

        if ($pes_id > 0) {
            $this->aPessoa = $this->pessoasDAO->get($pes_id);
            $this->novo = false;

            if (empty($this->aPessoa)) {
                $this->session->flash('error', _('Registro não encontrado'));
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Pessoas') . '</h1>',
            L::linkbutton('Voltar', 'pessoas.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? _('Nova Pessoa') : _('Editar Pessoa')  . ": {$this->aPessoa['pes_id']} - {$this->aPessoa['pes_nome']}");
        $this->form->setForm('id="form-pessoas" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('pes_id', $this->aPessoa['pes_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_nome = FC::input(
            _('Nome'),
            'pes_nome',
            $this->aPessoa['pes_nome'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );
        $campo_telefone = FC::input(
            _('Telefone'),
            'pes_telefone',
            $this->aPessoa['pes_telefone'] ?? '',
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_email = FC::input(
            _('E-mail'),
            'pes_email',
            $this->aPessoa['pes_email'] ?? '',
            [
                'div_class' => 'col-md-4',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_endereco = FC::input(
            _('Endereço'),
            'pes_endereco',
            $this->aPessoa['pes_endereco'] ?? '',
            [
                'div_class' => 'col-md-4',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_numero = FC::input(
            _('Número'),
            'pes_numero',
            $this->aPessoa['pes_numero'] ?? '',
            [
                'div_class' => 'col-md-1',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_bairro = FC::input(
            _('Bairro'),
            'pes_bairro',
            $this->aPessoa['pes_bairro'] ?? '',
            [
                'div_class' => 'col-md-3',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_complemento = FC::input(
            _('Complemento'),
            'pes_complemento',
            $this->aPessoa['pes_complemento'] ?? '',
            [
                'div_class' => 'col-md-6',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_cep = FC::input(
            _('CEP'),
            'pes_cep',
            $this->aPessoa['pes_cep'] ?? '',
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm'
            ]
        );
        $campo_cidade = FC::input(
            _('Cidade'),
            'pes_cidade',
            $this->aPessoa['pes_cidade'] ?? '',
            [
                'div_class' => 'col-md-4',
                'style' => 'text-transform:uppercase',
                'class' => 'form-control form-control-sm'
            ]
        );
        $arrayEstados = array_merge(['' => 'Selecione'], Constantes::obterEstado());
        $campo_estado = FC::select(
            _('Estado'),
            'pes_estado',
            $arrayEstados,
            $this->aPessoa['pes_estado'] ?? '',
            [
                'div_class' => 'col-md-3',
                'class' => 'form-control form-control-sm'
            ]
        );

        $familias = $this->buscarFamilias();
        $campo_familia = FC::select(_('Família'), 'pes_familia_id', $familias, $this->aPessoa['pes_familia_id'] ?? '0', ['class' => 'form-control form-control-sm', 'div_class' => 'col-md-3']);

        $this->form->setFields([
            ['<div class="row">' . $campo_nome . $campo_telefone . $campo_email . '</div>'],
            ['<div class="row">' . $campo_endereco . $campo_numero . $campo_bairro . '</div>'],
            ['<div class="row">' . $campo_complemento . '</div>'],
            ['<div class="row">' . $campo_cep . $campo_cidade . $campo_estado . '</div>'],
            ['<div class="row">' . $campo_familia . '</div>']
        ]);

        $this->form->setActions(L::submit(_('Salvar')));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'pes_nome'     => _('Informe o Nome'),
            'pes_telefone' => _('Informe o Telefone'),
            'pes_email'    => _('E-mail inválido')
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $("#pes_cep").keypress(function(){
                        var cep = $("#pes_cep").val();
                        cep = formatar_cep(cep);
                        $("#pes_cep").val(cep);
                    });

                    $('#form-pessoas').validate({
                        rules: {
                            pes_nome: {
                                required: true
                            },
                            pes_telefone: {
                                required: true
                            },
                            pes_email: {
                                email: true
                            }
                        },
                        messages: {
                            pes_nome: {
                                required: '{$this->aMensagens["pes_nome"]}'
                            },
                            pes_telefone: {
                                required: '{$this->aMensagens["pes_telefone"]}'
                            },
                            pes_email: {
                                email: '{$this->aMensagens["pes_email"]}'
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
            ['title' => 'Cadastro de Pessoa']
        );
    }
}
