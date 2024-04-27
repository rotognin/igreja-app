<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class CriarTabelaPrograma extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('programa', ['id' => 'prg_codigo', 'primary_key' => ['prg_codigo'], 'signed' => true]);
        $table->addColumn('prg_sequencia', 'integer')
            ->addColumn('prg_descricao', Literal::from('varchar(100)'))
            ->addColumn('prg_url', Literal::from('varchar(100)'))
            ->addColumn('prg_icone', Literal::from('varchar(100)'))
            ->addColumn('prg_codigo_pai', 'integer')
            ->addColumn('prg_lft', 'integer')
            ->addColumn('prg_rgt', 'integer')
            ->addColumn('prg_nivel', 'integer')
            ->addColumn('prg_ativo', 'char')
            ->addColumn('prg_acao', Literal::from('varchar(30)'))
            ->create();

        // Criar as informações da tabela para serem inseridas na migration
        if ($this->isMigratingUp()) {
            $table->insert([
                [ // 1
                    'prg_sequencia' => 10,
                    'prg_descricao' => 'Sistema',
                    'prg_icone' => 'fas fa-user-shield',
                    'prg_codigo_pai' => 0,
                    'prg_lft' => 13,
                    'prg_rgt' => 30,
                    'prg_nivel' => 0,
                    'prg_ativo' => 'S'
                ],
                [ // 2
                    'prg_sequencia' => 10,
                    'prg_descricao' => 'Usuários',
                    'prg_url' => '/sgc/cadUsuario.php',
                    'prg_icone' => 'fas fa-users',
                    'prg_codigo_pai' => 4,
                    'prg_lft' => 19,
                    'prg_rgt' => 20,
                    'prg_nivel' => 2,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'usuarios_acesso'
                ],
                [ // 3
                    'prg_sequencia' => 20,
                    'prg_descricao' => 'Ações de usuário',
                    'prg_url' => '/sgc/cadAcao.php',
                    'prg_icone' => 'fas fa-shield-halved',
                    'prg_codigo_pai' => 4,
                    'prg_lft' => 21,
                    'prg_rgt' => 22,
                    'prg_nivel' => 2,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'acoes_acesso'
                ],
                [ // 4
                    'prg_sequencia' => 10,
                    'prg_descricao' => 'Cadastros',
                    'prg_url' => '',
                    'prg_icone' => 'fas fa-box-archive',
                    'prg_codigo_pai' => 1,
                    'prg_lft' => 16,
                    'prg_rgt' => 25,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => ''
                ],
                [ // 5
                    'prg_sequencia' => 0,
                    'prg_descricao' => 'Empresas',
                    'prg_url' => '/sgc/cadEmpresa.php',
                    'prg_icone' => 'far fa-building',
                    'prg_codigo_pai' => 4,
                    'prg_lft' => 17,
                    'prg_rgt' => 18,
                    'prg_nivel' => 2,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'empresas_acesso'
                ],
                [ // 6
                    'prg_sequencia' => 0,
                    'prg_descricao' => 'Menus',
                    'prg_url' => '/sgc/cadMenu.php',
                    'prg_icone' => 'fas fa-bars',
                    'prg_codigo_pai' => 1,
                    'prg_lft' => 14,
                    'prg_rgt' => 15,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'menus_acesso'
                ],
                [ // 7
                    'prg_sequencia' => 20,
                    'prg_descricao' => 'Relatórios',
                    'prg_url' => '',
                    'prg_icone' => 'far fa-file-lines',
                    'prg_codigo_pai' => 1,
                    'prg_lft' => 26,
                    'prg_rgt' => 29,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => ''
                ],
                [ // 8
                    'prg_sequencia' => 10,
                    'prg_descricao' => 'Acesso à programas',
                    'prg_url' => '/sgc/relLogPrograma.php',
                    'prg_icone' => 'fas fa-user-clock',
                    'prg_codigo_pai' => 7,
                    'prg_lft' => 27,
                    'prg_rgt' => 28,
                    'prg_nivel' => 2,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'sgc01_acesso'
                ],
                [ // 9
                    'prg_sequencia' => 1,
                    'prg_descricao' => 'Cadastros',
                    'prg_url' => '',
                    'prg_icone' => 'fa fa-book',
                    'prg_codigo_pai' => 0,
                    'prg_lft' => 1,
                    'prg_rgt' => 8,
                    'prg_nivel' => 0,
                    'prg_ativo' => 'S',
                    'aca_acao' => ''
                ],
                [ // 10
                    'prg_sequencia' => 5,
                    'prg_descricao' => 'Pessoas',
                    'prg_url' => '/cadastro/pessoas.php',
                    'prg_icone' => 'fas fa-user',
                    'prg_codigo_pai' => 9,
                    'prg_lft' => 4,
                    'prg_rgt' => 5,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'cadastros_acesso'
                ],
                [ // 11
                    'prg_sequencia' => 10,
                    'prg_descricao' => 'Famílias',
                    'prg_url' => '/cadastro/familias.php',
                    'prg_icone' => 'fas fa-users',
                    'prg_codigo_pai' => 9,
                    'prg_lft' => 6,
                    'prg_rgt' => 7,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'cadastros_acesso'
                ],
                [ // 12
                    'prg_sequencia' => 5,
                    'prg_descricao' => 'Movimentações',
                    'prg_url' => '',
                    'prg_icone' => 'fas fa-random',
                    'prg_codigo_pai' => 0,
                    'prg_lft' => 9,
                    'prg_rgt' => 12,
                    'prg_nivel' => 0,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'movimentacoes_acesso'
                ],
                [ // 13
                    'prg_sequencia' => 1,
                    'prg_descricao' => 'Visitas',
                    'prg_url' => '/movimentacoes/visitas.php',
                    'prg_icone' => 'fas fa-store-alt',
                    'prg_codigo_pai' => 12,
                    'prg_lft' => 10,
                    'prg_rgt' => 11,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'movimentacoes_acesso'
                ],
                [ // 14
                    'prg_sequencia' => 21,
                    'prg_descricao' => 'Papéis de usuário',
                    'prg_url' => '/sgc/cadPapel.php',
                    'prg_icone' => 'fas fa-tools',
                    'prg_codigo_pai' => 4,
                    'prg_lft' => 23,
                    'prg_rgt' => 24,
                    'prg_nivel' => 2,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'papeis_acesso'
                ],
                [ // 15
                    'prg_sequencia' => 1,
                    'prg_descricao' => 'Membros',
                    'prg_url' => '/cadastro/membros.php',
                    'prg_icone' => 'fas fa-church',
                    'prg_codigo_pai' => 9,
                    'prg_lft' => 2,
                    'prg_rgt' => 3,
                    'prg_nivel' => 1,
                    'prg_ativo' => 'S',
                    'aca_acao' => 'cadastros_acesso'
                ]
            ])->save();
        }
    }

    public function down()
    {
        $this->table('programa')->drop()->save();
    }
}
