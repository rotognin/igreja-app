<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterarCampoSituacaoVisita extends AbstractMigration
{

    public function up(): void
    {
        $visitas = $this->table('visitas');
        $visitas->changeColumn('vis_status', 'string', ['limit' => 20])
            ->save();
    }

    public function down(): void
    {
        $visitas = $this->table('visitas');
        $visitas->changeColumn('vis_status', 'integer')
            ->save();
    }
}
