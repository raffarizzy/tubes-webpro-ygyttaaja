<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SpareHubHomeTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testHomePageLoads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            
            // Wait for potential alert
            try {
                $browser->waitForDialog(2);
                $browser->acceptDialog();
            } catch (\Exception $e) {
                // No alert, continue
            }

            $browser->assertSee('SpareHub')
                    ->assertSee('Selamat Datang');
        });
    }
}
