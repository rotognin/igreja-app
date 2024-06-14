<?php

namespace View\Patrimonio;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Lib\GlobalHelper;
use App\PATRIMONIO\DAO\CategoriaPatrimonio;
use App\PATRIMONIO\DAO\Patrimonio;
use App\SGC\DAO\Usuario;

class Form extends GlobalHelper
{
    private CategoriaPatrimonio $categoriaDAO;
    private Patrimonio $patrimonioDAO;
    private Usuario $usuarioDAO;
    private array $aPatrimonio = [];
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
        $this->patrimonioDAO = new Patrimonio();
        $this->usuarioDAO = new Usuario();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aPatrimonio = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $pat_id = $this->request->get('pat_id', 0);

        if ($pat_id > 0) {
            $this->aPatrimonio = $this->patrimonioDAO->get($pat_id);
            $this->novo = false;

            if (empty($this->aPatrimonio)) {
                $this->session->flash('error', 'Registro não encontrado');
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Patrimônio</h1>',
            L::linkbutton('Voltar', 'patrimonios.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Novo Patrimônio' : 'Editar Patrimônio'  . ": {$this->aPatrimonio['pat_id']}");
        $this->form->setForm('id="form-patrimonio" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('pat_id', $this->aPatrimonio['pat_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_descricao = FC::input(
            'Descrição',
            'pat_descricao',
            $this->aPatrimonio['pat_descricao'] ?? '',
            [
                'div_class' => 'col-md-6',
                'class' => 'form-control form-control-sm',
                'style' => 'text-transform:uppercase',
                'autofocus' => 'autofocus'
            ]
        );

        $aCategoria = $this->categoriaDAO->montarArray();

        $campo_categoria = FC::select('Categoria', 'pat_categoria_id', ['0' => 'Selecione'] + $aCategoria, $this->aPatrimonio['pat_categoria_id'] ?? '', [
            'div_class' => 'col-md-3',
            'class' => 'form-control form-control-sm'
        ]);

        $campo_marca = FC::input('Marca', 'pat_marca', $this->aPatrimonio['pat_marca'] ?? '', [
            'div_class' => 'col-md-4',
            'class' => 'form-control form-control-sm',
            'style' => 'text-transform:uppercase'
        ]);

        $campo_especificacao = FC::textarea('Especificações', 'pat_especificacao', $this->aPatrimonio['pat_especificacao'] ?? '', [
            'div_class' => 'col-md-8',
            'class' => 'form-control form-control-sm',
            'style' => 'text-transform:uppercase',
            'rows' => 3
        ]);

        $campo_tipo_entrada = FC::select('Tipo de Entrada', 'pat_tipo_entrada', ['0' => 'Selecione'] + $this->patrimonioDAO->getTipoEntrada(), $this->aPatrimonio['pat_tipo_entrada'] ?? '0', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm',
        ]);

        $campo_valor_estimado = FC::input('Valor Estimado R$', 'pat_valor_estimado', $this->aPatrimonio['pat_valor_estimado'] ?? '', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm dinheiro-mask'
        ]);

        $campo_data_entrada = FC::input('Data Entrada', 'par_data_hora_entrada', $this->aPatrimonio['pat_data_hora_entrada'] ?? '', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm data-mask'
        ]);

        $aUsuarios = $this->usuarioDAO->montarArray(['0' => 'Selecione']);

        $campo_usu_entrada = FC::select('Usuário Entrada', 'pat_usu_entrada', $aUsuarios, $this->aPatrimonio['pat_usu_entrada'] ?? '0', [
            'div_class' => 'col-md-3',
            'class' => 'form-control form-control-sm'
        ]);

        $campo_usu_responsavel = FC::select('Usuário Responsável', 'pat_usu_responsavel', $aUsuarios, $this->aPatrimonio['pat_usu_responsavel'] ?? '0', [
            'div_class' => 'col-md-3',
            'class' => 'form-control form-control-sm'
        ]);

        $campo_quantidade = FC::input('Quantidade', 'pat_quantidade', $this->aPatrimonio['pat_quantidade'] ?? '', [
            'div_class' => 'col-md-1',
            'class' => 'form-control form-control-sm quantidade-mask'
        ]);

        $campo_consevacao = FC::select('<nobr>Estado de Conservação</nobr>', 'pat_conservacao', $this->patrimonioDAO->getConservacao(), $this->aPatrimonio['pat_conservacao'] ?? '0', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm'
        ]);

        $campo_observacoes = FC::textarea('Observações', 'pat_observacoes', $this->aPatrimonio['pat_observacoes'] ?? '', [
            'div_class' => 'col-md-8',
            'class' => 'form-control form-control-sm',
            'style' => 'text-transform:uppercase',
            'rows' => 3
        ]);

        $campo_nf_serie = FC::input('NF Série', 'par_nf_serie', $this->aPatrimonio['pat_nf_serie'] ?? '', [
            'div_class' => 'col-md-1',
            'class' => 'form-control form-control-sm',
            'style' => 'text-transform:uppercase',
            'maxlength' => '4'
        ]);

        $campo_nf_numero = FC::input('NF Número', 'par_nf_numero', $this->aPatrimonio['pat_nf_numero'] ?? '', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm',
            'type' => 'number'
        ]);

        $campo_nf_valor = FC::input('NF Valor R$', 'pat_nf_valor', $this->aPatrimonio['pat_nf_valor'] ?? '', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm dinheiro-mask'
        ]);

        $campo_nf_data = FC::input('NF Data', 'par_nf_data', $this->aPatrimonio['pat_nf_data'] ?? '', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm data-mask'
        ]);

        $campo_nf_chave = FC::input('NF Chave', 'par_nf_chave', $this->aPatrimonio['pat_nf_chave'] ?? '', [
            'div_class' => 'col-md-4',
            'class' => 'form-control form-control-sm',
            'maxlength' => '44'
        ]);

        $this->form->setFields([
            ['<div class="row">' . $campo_descricao . $campo_marca . '</div>'],
            ['<div class="row">' . $campo_especificacao . '</div>'],
            ['<div class="row">' . $campo_categoria . $campo_tipo_entrada . $campo_valor_estimado . $campo_data_entrada . '</div>'],
            ['<div class="row">' . $campo_quantidade . $campo_consevacao . $campo_usu_entrada . $campo_usu_responsavel . '</div>'],
            ['<div class="row">' . $campo_observacoes . '</div>'],
            ['<br>'],
            ['<div class="row" style="border:1px solid black;">' . $campo_nf_serie . $campo_nf_numero . $campo_nf_data . $campo_nf_valor . $campo_nf_chave . '</div>'],
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'pat_descricao' => 'Informe a Descrição'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $('.data-mask').mask('00/00/0000');
                $('.dinheiro-mask').mask('#.##0,00', {reverse: true});
                $('.quantidade-mask').mask('#,00', {reverse: true});

                $(function(){
                    $.validator.addMethod("verificarCategoria", function(cat){
                        if (cat == 0 || cat == '' || cat == undefined){
                            return false;
                        }

                        return true;
                    }, "Informe a Categoria");

                    $.validator.addMethod("verificarTipoEntrada", function(tip){
                        if (tip == 0 || tip == '' || tip == undefined){
                            return false;
                        }

                        return true;
                    }, "Informe o Tipo de Entrada");

                    $('#form-patrimonio').validate({
                        rules: {
                            pat_descricao: {
                                required: true
                            },
                            pat_categoria_id: "verificarCategoria",
                            pat_tipo_entrada: "verificarTipoEntrada"
                        },
                        messages: {
                            pat_descricao: {
                                required: '{$this->aMensagens["pat_descricao"]}'
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
            ['title' => 'Cadastro de Patrimonio']
        );
    }
}
