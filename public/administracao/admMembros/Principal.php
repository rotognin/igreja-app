<?php

namespace View\Administracao;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Form;
use Funcoes\Lib\ViewHelper;

class Principal extends ViewHelper
{
    private Form $formBotoes;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->cardBotoes();
        $this->saidaPagina();
    }

    private function cardBotoes()
    {
        $this->formBotoes = new Form();
        $this->formBotoes->setTitle(_('Movimentação de Membros'));
        $this->formBotoes->setForm('action="" method="GET"');

        $botaoPessoaParaMembro = L::linkButton('&nbsp;&nbsp;&nbsp;' . _('Pessoa para Membro'), '?posicao=paraMembro', _('Pessoa para Membro'), 'fas fa-user-circle', 'primary', 'md');
        $botaoMembroParaPessoa = L::linkButton('&nbsp;&nbsp;&nbsp;' . _('Membro para Pessoa'), '?posicao=paraPessoa', _('Membro para Pessoa'), 'fas fa-user', 'primary', 'md');
        $botaoTransferencia = L::linkButton('&nbsp;&nbsp;&nbsp;' . _('Transferência'), '?posicao=transferencia', _('Transferência'), 'fas fa-user-check', 'primary', 'md');
        $botaoExclusao = L::linkButton('&nbsp;&nbsp;&nbsp;' . _('Exclusão'), '?posicao=exclusao', _('Exclusão'), 'fas fa-user-times', 'primary', 'md');

        $this->formBotoes->setFields([
            [$botaoPessoaParaMembro, $botaoMembroParaPessoa, $botaoTransferencia, $botaoExclusao]
        ]);
    }

    private function saidaPagina()
    {
        $this->response->page(
            <<<HTML
                     <div class="content">
                    <div class="container-fluid pb-1">
                        {$this->formBotoes->html()}
                    </div>
                </div>
            HTML,
            ['title' => 'Movimentação de Membros']
        );
    }
}
