<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TabelaVisitaIntegrantes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('visita_integrantes', ['id' => 'vin_id', 'primary_key' => ['vin_id'], 'signed' => true]);
        $table->addColumn('vin_visita_id', 'integer')
            ->addColumn('vin_membro_id', 'integer')
            ->addColumn('vin_pessoa_id', 'integer')
            ->addColumn('vin_tipo', 'string', ['limit' => 1]) // M - Membro, P - Pessoa
            ->create();

        /* - Para adicionar dados na tabela após criada
            if ($this->isMigratingUp()) {
                $table->insert([['user_id' => 1, 'created' => '2020-01-19 03:14:07']])
                    ->save();
            }
        */
    }
}
