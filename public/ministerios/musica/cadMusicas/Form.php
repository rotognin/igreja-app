<?php

namespace View\Ministerios\Musica\Musica;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\MINISTERIOS\MUSICA\DAO\Musicas;
use App\MINISTERIOS\MUSICA\DAO\Categorias;
use Funcoes\Lib\GlobalHelper;

class Form extends GlobalHelper
{
    private Musicas $musicasDAO;
    private Categorias $categoriasDAO;
    private array $aMusica = [];
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
        $this->musicasDAO = new Musicas();
        $this->categoriasDAO = new Categorias();
    }

    private function existeRetorno(): bool
    {
        $existeRetorno = false;

        if ($this->session->check('previous')) {
            $this->aMusica = $this->session->get('previous');
            $existeRetorno = true;
        }

        return $existeRetorno;
    }

    private function checarID()
    {
        $mus_id = $this->request->get('mus_id', 0);

        if ($mus_id > 0) {
            $this->aMusica = $this->musicasDAO->get($mus_id);
            $this->novo = false;

            if (empty($this->aMusica)) {
                $this->session->flash('error', 'Registro não encontrado');
                return $this->response->back();
            }
        }
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Músicas</h1>',
            L::linkbutton('Voltar', 'musicas.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->novo ? 'Nova Música' : 'Editar Música'  . ": {$this->aMusica['mus_id']} - {$this->aMusica['mus_nome']}");
        $this->form->setForm('id="form-musicas" action="?posicao=salvar" method="post"');
    }

    private function montarCampos()
    {
        if (!$this->novo) {
            $this->form->addHidden(FC::hidden('mus_id', $this->aMusica['mus_id']));
        }

        $this->form->addHidden(FC::hidden('novo', ($this->novo) ? 'S' : 'N'));

        $campo_nome = FC::input(
            'Nome',
            'mus_nome',
            $this->aMusica['mus_nome'] ?? '',
            [
                'div_class' => 'col-md-4',
                'class' => 'form-control form-control-sm',
                'autofocus' => 'autofocus'
            ]
        );

        $campo_artista = FC::input(
            'Artista',
            'mus_artista',
            $this->aMusica['mus_artista'] ?? '',
            [
                'div_class' => 'col-md-3',
                'class' => 'form-control form-control-sm'
            ]
        );

        $campo_categoria = FC::select('Categoria', 'mus_categoria_id', $this->categoriasDAO->montarArray(), $this->aMusica['mus_categoria_id'] ?? '', [
            'div_class' => 'col-md-3',
            'class' => 'form-control form-control-sm'
        ]);

        $campo_link = FC::input(
            'Link',
            'mus_link',
            $this->aMusica['mus_link'] ?? '',
            [
                'div_class' => 'col-md-6',
                'class' => 'form-control form-control-sm'
            ]
        );

        $campo_situacao = FC::select('Situação', 'mus_situacao', $this->musicasDAO->getSituacao(), $this->aMusica['mus_situacao'] ?? 'A', [
            'div_class' => 'col-md-1',
            'class' => 'form-control form-control-sm'
        ]);

        $this->form->setFields([
            ['<div class="row">' . $campo_nome . $campo_artista . $campo_categoria . '</div>'],
            ['<div class="row">' . $campo_link . $campo_situacao . '</div>']
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function montarMensagens()
    {
        $this->aMensagens = array(
            'mus_nome' => 'Informe o Nome'
        );
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $(function(){
                    $('#form-musicas').validate({
                        rules: {
                            mus_nome: {
                                required: true
                            }
                        },
                        messages: {
                            mus_nome: {
                                required: '{$this->aMensagens["mus_nome"]}'
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
            ['title' => 'Cadastro de Música']
        );
    }
}
