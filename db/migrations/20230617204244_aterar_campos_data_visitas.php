<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AterarCamposDataVisitas extends AbstractMigration
{
    public function up(): void
    {
        $vis  = $this->table('visitas');
        $vis->changeColumn('vis_data_inc', 'datetime')
            ->changeColumn('vis_data_alt', 'datetime', ['null' => true])
            ->changeColumn('vis_data_exc', 'datetime', ['null' => true])
            ->save();
    }

    public function down(): void
    {
        $vis  = $this->table('visitas');
        $vis->changeColumn('vis_data_inc', 'date')
            ->changeColumn('vis_data_alt', 'date', ['null' => true])
            ->changeColumn('vis_data_exc', 'date', ['null' => true])
            ->save();
    }
}
