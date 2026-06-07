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
            $browser->visit('http://127.0.0.1:8000/login')
                    ->press('Login')
                    ->waitForLocation('/', 10)
                    ->assertPathIs('/');
        });
    }
}
