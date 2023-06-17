<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterarCampoTipoVisitaIntegrantes extends AbstractMigration
{
    public function up(): void
    {
        $vin = $this->table('visita_integrantes');
        $vin->changeColumn('vin_tipo', 'string', ['limit' => 20])->save();
    }

    public function down(): void
    {
        $vin = $this->table('visita_integrantes');
        $vin->changeColumn('vin_tipo', 'string', ['limit' => 1])->save();
    }
}
