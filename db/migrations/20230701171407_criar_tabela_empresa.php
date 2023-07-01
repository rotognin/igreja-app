<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class CriarTabelaEmpresa extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('empresa', ['id' => 'emp_id', 'primary_key' => ['emp_id'], 'signed' => true]);
        $table->addColumn('emp_codigo', Literal::from('varchar(20)'))
            ->addColumn('emp_nome', Literal::from('varchar(100)'))
            ->create();

        if ($this->isMigratingUp()) {
            $table->insert([
                ['emp_codigo' => '1', 'emp_nome' => 'Igreja']
            ])->save();
        }
    }

    public function down(): void
    {
        $this->table('empresa')->drop()->save();
    }
}
