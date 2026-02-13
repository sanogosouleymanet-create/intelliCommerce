<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Ia_alerte;
use App\Models\Administrateur;
use App\Models\Vendeur;
use App\Services\IAService;

class IaAlerteTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_alert_for_admin()
    {
        // Créer un administrateur fictif
        $admin = Administrateur::factory()->create();

        $service = new IAService();
        $service->CrerAlerte('administrateur', $admin->idAdmi, 'Test', 'critique', 'Description test');

        $this->assertDatabaseHas('ia_alertes', [
            'destinataire_type' => 'administrateur',
            'destinataire_id' => $admin->idAdmi,
            'TypeAlerte' => 'Test',
            'NiveauGravité' => 'critique',
            'Description' => 'Description test',
        ]);

        $alerte = Ia_alerte::first();
        $this->assertInstanceOf(Administrateur::class, $alerte->destinataire);
        $this->assertEquals($admin->idAdmi, $alerte->destinataire->idAdmi);
    }

    public function test_create_alert_for_vendeur()
    {
        // Créer un vendeur fictif
        $vendeur = Vendeur::factory()->create();

        $service = new IAService();
        $service->CrerAlerte('vendeur', $vendeur->idVendeur, 'Test', 'moyen', 'Description test vendeur');

        $this->assertDatabaseHas('ia_alertes', [
            'destinataire_type' => 'vendeur',
            'destinataire_id' => $vendeur->idVendeur,
            'TypeAlerte' => 'Test',
            'NiveauGravité' => 'moyen',
            'Description' => 'Description test vendeur',
        ]);

        $alerte = Ia_alerte::where('destinataire_type', 'vendeur')->first();
        $this->assertInstanceOf(Vendeur::class, $alerte->destinataire);
        $this->assertEquals($vendeur->idVendeur, $alerte->destinataire->idVendeur);
    }
}
