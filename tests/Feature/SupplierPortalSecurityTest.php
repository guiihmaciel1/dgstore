<?php

namespace Tests\Feature;

use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Models\SupplierUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierPortalSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_cannot_access_main_system()
    {
        $supplier = Supplier::factory()->create();
        $supplierUser = SupplierUser::factory()->create(['supplier_id' => $supplier->id]);

        $this->actingAs($supplierUser, 'supplier');

        $response = $this->get('/dashboard');
        $response->assertRedirect(route('supplier.login'));
    }

    public function test_supplier_cannot_see_other_supplier_items()
    {
        $supplier1 = Supplier::factory()->create(['name' => 'Supplier 1']);
        $supplier2 = Supplier::factory()->create(['name' => 'Supplier 2']);
        
        $supplierUser1 = SupplierUser::factory()->create(['supplier_id' => $supplier1->id]);
        $supplierUser2 = SupplierUser::factory()->create(['supplier_id' => $supplier2->id]);

        $this->actingAs($supplierUser1, 'supplier');

        $itemSupplier2 = \App\Domain\ConsignmentStock\Models\ConsignmentStockItem::factory()->create([
            'supplier_id' => $supplier2->id,
        ]);

        $response = $this->get(route('supplier.stock.show', $itemSupplier2));
        $response->assertStatus(403);
    }

    public function test_supplier_login_has_rate_limiting()
    {
        $supplier = Supplier::factory()->create();
        $supplierUser = SupplierUser::factory()->create([
            'supplier_id' => $supplier->id,
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('supplier.login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('supplier.login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many', $response->getSession()->get('errors')->first('email'));
    }

    public function test_unauthenticated_access_redirects_to_login()
    {
        $response = $this->get(route('supplier.dashboard'));
        $response->assertRedirect(route('supplier.login'));
    }

    public function test_inactive_supplier_user_is_logged_out()
    {
        $supplier = Supplier::factory()->create(['active' => true]);
        $supplierUser = SupplierUser::factory()->create([
            'supplier_id' => $supplier->id,
            'active' => false,
        ]);

        $this->actingAs($supplierUser, 'supplier');

        $response = $this->get(route('supplier.dashboard'));
        $response->assertRedirect(route('supplier.login'));
        $response->assertSessionHasErrors('email');
    }
}
