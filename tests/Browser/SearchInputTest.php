<?php

namespace Tests\Browser;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchInputTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Currency::factory()->create(['name' => 'USD', 'is_default' => true, 'is_crypto' => false]);
    }

    /** @dataProvider searchPages */
    public function test_search_input_icon_does_not_overlap_text(string $route, string $placeholder): void
    {
        $this->browse(function (Browser $browser) use ($route, $placeholder) {
            $browser->loginAs($this->user)->visit(route($route));

            $input = $browser->element("input[placeholder=\"{$placeholder}\"]");
            $this->assertNotNull($input, "Search input not found on {$route}");

            // computed padding-left must be >= 28px (2rem at 14px base) so the icon doesn't overlap
            $paddingLeft = $browser->script(
                "return parseFloat(window.getComputedStyle(
                    document.querySelector('input[placeholder=\"{$placeholder}\"]')
                ).paddingLeft)"
            )[0];

            $this->assertGreaterThanOrEqual(
                28,
                $paddingLeft,
                "Search input on {$route} has padding-left={$paddingLeft}px, expected >= 28px to avoid icon overlap"
            );
        });
    }

    public static function searchPages(): array
    {
        return [
            'categories' => ['categories.index', 'Search categories...'],
            'places'     => ['places.index',     'Search places...'],
        ];
    }
}
