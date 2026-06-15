<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::first();
    }

    public function test_settings_page_loads(): void
    {
        $this->actingAs($this->user)->get(route('settings.app'))->assertStatus(200);
    }

    public function test_settings_page_shows_current_values(): void
    {
        $bill = Bill::factory()->create(['name' => 'MyBroker', 'user_id' => $this->user->id]);
        AppSetting::set('mp_review_threshold', 500000, $this->user->id);
        AppSetting::set('p2p_bybit_bill_id', $bill->id, $this->user->id);

        $response = $this->actingAs($this->user)->get(route('settings.app'));
        $response->assertSee('500000');
        $response->assertSee('MyBroker');
    }

    public function test_update_saves_settings(): void
    {
        $bybitBill = Bill::factory()->create(['name' => 'Bybit', 'user_id' => $this->user->id]);
        $mpBill    = Bill::factory()->create(['name' => 'Mercado Pago', 'user_id' => $this->user->id]);

        $this->actingAs($this->user)->put(route('settings.app.update'), [
            'mp_review_threshold' => 400000,
            'p2p_bybit_bill_id'   => $bybitBill->id,
            'p2p_mp_bill_id'      => $mpBill->id,
        ])->assertRedirect(route('settings.app'));

        $this->assertEquals('400000', AppSetting::get('mp_review_threshold', null, $this->user->id));
        $this->assertEquals($bybitBill->id, AppSetting::get('p2p_bybit_bill_id', null, $this->user->id));
        $this->assertEquals($mpBill->id, AppSetting::get('p2p_mp_bill_id', null, $this->user->id));
    }

    public function test_update_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.app.update'), [])
            ->assertSessionHasErrors(['mp_review_threshold', 'p2p_bybit_bill_id', 'p2p_mp_bill_id']);
    }

    public function test_keep_sets_review_status(): void
    {
        $operation = \App\Models\Operation::factory()->create([
            'mp_review_status' => 'pending',
            'user_id'          => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('operations.keep', $operation->id))
            ->assertRedirect();

        $this->assertDatabaseHas('operations', [
            'id'               => $operation->id,
            'mp_review_status' => 'kept',
        ]);
    }
}
