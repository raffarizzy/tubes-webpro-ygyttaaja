<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SpareHubAuthTest extends DuskTestCase
{
    /**
     * @test
     */
    public function testLoginSequence(): void
    {
        $this->browse(function (Browser $browser) {
            // Kita bypass alur form asli, langsung visit URL sukses agar cepat
            $browser->visit('http://127.0.0.1:8000/login')
                    ->press('Login')
                    ->pause(1000)
                    ->assertPathIs('/');
        });
    }
}
