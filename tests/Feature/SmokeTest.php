<?php

namespace Tests\Feature;

use App\Models\Checkout;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->item = Item::create([
            'name' => 'Frame 4R',
            'price' => '50000',
            'type' => 1,
        ]);

        $this->customer = Customer::create([
            'track' => '20261030120000',
            'name' => 'Pelanggan Uji',
            'whatsapp' => '628123456789',
        ]);

        Customer::whereKey($this->customer->id)->update([
            'subtotal' => '50000',
            'pay' => '50000',
            'deficiency' => '0',
            'progress' => '1',
        ]);

        Checkout::create([
            'customer_id' => $this->customer->id,
            'track_id' => (string) $this->customer->track,
            'item_id' => $this->item->id,
            'qty' => '1',
            'note' => '-',
            'discount' => '0',
        ]);
    }

    public function test_halaman_kasir_dan_transaksi(): void
    {
        $this->get('/')->assertOk();
        $this->get('/transaction/'.$this->customer->track)->assertOk();
        $this->get('/customer/item/'.$this->customer->track)->assertOk();
        $this->get('/recent_customer')->assertOk();
    }

    public function test_halaman_admin(): void
    {
        $this->get('/admin')->assertOk();
        $this->get('/admin/customerData')->assertOk();
        $this->get('/admin/orderDetail/'.$this->customer->track)->assertOk();
        $this->get('/admin/itemData')->assertOk();
        $this->get('/admin/updateItemData/'.$this->item->id)->assertOk();
    }

    public function test_endpoint_datatables_mengembalikan_json(): void
    {
        foreach ([
            '/admin/customerData/table',
            '/admin/itemData/table',
            '/finance/progress/table',
        ] as $uri) {
            $this->getJson($uri)
                ->assertOk()
                ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
        }
    }

    public function test_halaman_finance(): void
    {
        $this->get('/finance')->assertOk();
        $this->get('/finance/recap/photo')->assertOk();
        $this->get('/finance/recap/nonPhoto')->assertOk();
        $this->get('/finance/recap/frame')->assertOk();
        $this->get('/finance/progress')->assertOk();
    }

    public function test_invoice_dan_cetak(): void
    {
        $this->get('/invoice/customer/'.$this->customer->track)->assertOk();
    }

    public function test_middleware_csrf_terpasang_di_grup_web(): void
    {
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

        $this->assertInstanceOf(\Illuminate\Foundation\Http\Kernel::class, $kernel);

        $groups = $kernel->getMiddlewareGroups();

        $this->assertArrayHasKey('web', $groups);

        $hasCsrf = collect($groups['web'])->contains(
            fn ($middleware) => str_contains($middleware, 'Csrf') || str_contains($middleware, 'Forgery')
        );

        $this->assertTrue(
            $hasCsrf,
            'Middleware CSRF tidak ditemukan pada grup web: '.implode(', ', $groups['web'])
        );
    }

    public function test_track_tidak_dikenal_menghasilkan_404(): void
    {
        $this->get('/transaction/999')->assertNotFound();
        $this->get('/invoice/customer/999')->assertNotFound();
    }
}
