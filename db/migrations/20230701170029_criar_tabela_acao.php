<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class CriarTabelaAcao extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('acao', ['id' => false, 'primary_key' => ['aca_acao']]);
        $table->addColumn('aca_acao', Literal::from('varchar(30)'), ['null' => false])
            ->addColumn('aca_descricao', Literal::from('varchar(100)'))
            ->addColumn('aca_grupo', Literal::from('varchar(50)'))
            ->create();

        if ($this->isMigratingUp()) {
            $table->insert([
                ['aca_acao' => 'acoes_acesso', 'aca_descricao' => 'Acesso a tela de gerenciamento de ações de usuário', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'cadastros_acesso', 'aca_descricao' => 'Acesso aos cadastros do Sistema', 'aca_grupo' => 'sistema'],
                ['aca_acao' => 'empresas_acesso', 'aca_descricao' => 'Acesso a tela de gerenciamento de empresas', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'gerais_acesso', 'aca_descricao' => 'Acesso aos cadastros gerais do sistema', 'aca_grupo' => 'sistema'],
                ['aca_acao' => 'menus_acesso', 'aca_descricao' => 'Acesso a tela de gerenciamento de menus', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'papeis_acesso', 'aca_descricao' => 'Acesso ao cadastro de papéis dos usuários', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'parametros_acesso', 'aca_descricao' => 'Acesso ao cadastro de parâmetros', 'aca_grupo' => 'sistema'],
                ['aca_acao' => 'movimentacoes_acesso', 'aca_descricao' => 'Acesso às movimentações do sistema', 'aca_grupo' => 'Movimentações'],
                ['aca_acao' => 'relatorios_acesso', 'aca_descricao' => 'Acesso aos relatórios do sistema', 'aca_grupo' => 'relatorio'],
                ['aca_acao' => 'menu_gravar', 'aca_descricao' => 'Gravar Menu', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'sgc01_acesso', 'aca_descricao' => 'Acesso ao relatório SGC01', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'usuarios_acesso', 'aca_descricao' => 'Acesso a tela de gerenciamento de usuários', 'aca_grupo' => 'SGC'],
                ['aca_acao' => 'teste_acesso', 'aca_descricao' => 'Acesso a programas em teste', 'aca_grupo' => 'sistema']
            ])->save();
        }
    }

    public function down(): void
    {
        $this->table('acao')->drop()->save();
    }
}
