<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SpareHubCartTest extends DuskTestCase
{
    /**
     * @test
     */
    public function testCartManagement(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8000/keranjang')
                    ->assertSee('Keranjang Belanja')
                    ->assertSee('Busi Racing')
                    ->assertSee('Total Harga');
        });
    }
}
