<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SpareHubEndToEndTest extends DuskTestCase
{
    /**
     * @test
     */
    public function testProductExploration(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8000/')
                    ->assertSee('SpareHub')
                    ->assertSee('Busi Racing');
        });
    }

    /**
     * @test
     */
    public function testCartInteraction(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8000/keranjang')
                    ->assertSee('Keranjang Belanja')
                    ->assertSee('Busi Racing');
        });
    }

    /**
     * @test
     */
    public function testCheckoutProcess(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8000/checkout')
                    ->assertSee('Detail Pengiriman')
                    ->assertSee('Busi Racing');
        });
    }
}
