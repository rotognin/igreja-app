<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class CriarTabelaUsuario extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('usuario', ['id' => false, 'primary_key' => ['usu_login']]);
        $table->addColumn('usu_login', Literal::from('varchar(20)'), ['null' => false])
            ->addColumn('usu_senha', Literal::from('varchar(255)'))
            ->addColumn('usu_nome', Literal::from('varchar(50)'))
            ->addColumn('usu_email', Literal::from('varchar(100)'))
            ->addColumn('usu_ramal', Literal::from('varchar(20)'))
            ->addColumn('usu_celular', Literal::from('varchar(20)'))
            ->addColumn('usu_ativo', 'char')
            ->addColumn('usu_celular_whatsapp', 'char')
            ->addColumn('usu_provedor_auth', Literal::from('varchar(20)'), ['default' => 'interno'])
            ->create();

        if ($this->isMigratingUp()) {
            $table->insert([
                [
                    'usu_login' => 'rotog',
                    'usu_senha' => '$2y$10$KHNEBPYcpMup/Ptf6E0f3eqGpRANRQ2R66tLnbSJWdIR/2.GL/zpS',
                    'usu_nome' => 'Rodrigo Tognin',
                    'usu_email' => 'rotog@outlook.com',
                    'usu_celular' => '(19) 99999-9999',
                    'usu_ativo' => 'S',
                    'usu_celular_whatsapp' => 'S',
                    'usu_provedor_auth' => 'interno'
                ]
            ])->save();
        }
    }

    public function down()
    {
        $this->table('usuario')->drop()->save();
    }
}
