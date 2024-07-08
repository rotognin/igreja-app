<?php

namespace View\Ministerios\Musica\Musica;

use Funcoes\Layout\Layout as L;
use App\MINISTERIOS\MUSICA\DAO\MusicaAnexos;
use App\MINISTERIOS\MUSICA\DAO\Musicas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;
use Funcoes\Lib\GlobalHelper;

class Anexos extends GlobalHelper
{
    private string $cabecalho;
    private Form $formAnexo;
    private Table $table;
    private MusicaAnexos $musicaAnexosDAO;
    private Musicas $musicasDAO;
    private string $script;
    private int $mus_id;
    private array $aMusica;

    public function __construct()
    {
        parent::__construct();

        $this->mus_id = $this->request->get('mus_id');
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->carregarRegistro();
        $this->montarCabecalho();
        $this->inicioFormFiltros();
        $this->montarCamposFiltros();
        $this->montarTabela();
        $this->montarScript();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->musicaAnexosDAO = new MusicaAnexos();
        $this->musicasDAO = new Musicas();
    }

    private function carregarRegistro()
    {
        $this->aMusica = $this->musicasDAO->get($this->mus_id);
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Anexos da Mùsica</h1>',
            L::backButton()
        );
    }

    private function inicioFormFiltros()
    {
        $this->formAnexo = new Form();
        $this->formAnexo->setTitle('<i class="fas fa-file-alt"></i> Anexo<br>' . $this->aMusica['mus_id'] . ' - ' . $this->aMusica['mus_nome']);
        $this->formAnexo->setForm('id="form-anexo" action="?posicao=salvarAnexo" method="POST" enctype="multipart/form-data"');
        $this->formAnexo->setCollapsable(false);
        $this->formAnexo->setActions(L::submit('Anexar', 'fas fa-check'));
        $this->formAnexo->addHidden(FC::hidden('mus_id', $this->mus_id));
    }

    private function montarCamposFiltros()
    {
        $campo_descricao = FC::input('Descrição', 'mua_descricao', '', [
            'class' => 'form-control form-control-sm',
            'div_class' => 'col-md-8',
        ]);

        $campo_tipo = FC::select('Tipo', 'mua_tipo', $this->musicaAnexosDAO->getTipo(), 'C', [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm'
        ]);

        $campo_arquivo = FC::input(
            'Arquivo',
            'mua_arquivo',
            '',
            [
                'div_class' => 'col-md-8',
                'type' => 'file',
                'class' => 'form-control-file',
                'id' => 'mua_arquivo'
            ]
        );

        $this->formAnexo->setFields([
            ['<div class="row">' . $campo_descricao . $campo_tipo . '</div>'],
            ['<div class="row">' . $campo_arquivo . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Table('id_anexos');
        $this->table->setFooter(false);
        $this->table->setSize('sm');

        $this->table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Arquivo', 'attrs' => ['class' => 'text-left']],
                ['value' => 'Descrição', 'attrs' => ['class' => 'text-left']],
                ['value' => 'Tipo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ação', 'attrs' => ['class' => 'text-center']]
            ]
        ]);

        $aAnexos = $this->musicaAnexosDAO->getAnexosMusica($this->mus_id);

        if (empty($aAnexos)) {
            $this->table->addRow([
                'cols' => [
                    ['value' => '<i>Nenhum anexo</i>', 'attrs' => ['class' => 'text-center', 'colspan' => '5']]
                ]
            ]);
        } else {
            foreach ($aAnexos as $anx) {
                $botoes = L::buttonGroup([
                    L::button('', "abrir({$anx['mua_id']})", 'Abrir arquivo', 'fas fa-file-alt', 'outline-primary', 'sm'),
                    L::button('', "excluir({$anx['mua_id']})", 'Excluir Anexo', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $this->table->addRow([
                    'cols' => [
                        ['value' => $anx['mua_id'], 'attrs' => ['class' => 'text-center']],
                        ['value' => $anx['mua_arquivo'], 'attrs' => ['class' => 'text-left']],
                        ['value' => $anx['mua_descricao'], 'attrs' => ['class' => 'text-left']],
                        ['value' => $this->musicaAnexosDAO->getTipo($anx['mua_tipo']), 'attrs' => ['class' => 'text-center']],
                        ['value' => $botoes, 'attrs' => ['class' => 'text-center']],
                    ]
                ]);
            }
        }
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function abrir(mua_id){
                    window.location.href = '?posicao=abrir&mua_id=' + mua_id;
                }

                function excluir(mua_id){
                    confirm('Deseja realmente excluir este Anexo?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&mua_id=' + mua_id;
                        }
                    });
                }

                $(function(){
                    $('#form-anexo').validate({
                        rules: {
                            mua_descricao: {
                                required: true
                            }
                        },
                        messages: {
                            mua_descricao: {
                                required: 'Informe uma descrição para o arquivo'
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
                        {$this->formAnexo->html()}
                        {$this->table->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Anexos da Música']
        );
    }
}
