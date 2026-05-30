<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_login_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Password');
        });
    }

    public function test_alpine_is_loaded(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertScript('typeof window.Alpine', 'object');
        });
    }
}
