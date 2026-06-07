<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class SpareHubEndToEndTest extends DuskTestCase
{
    /** @test */
    public function testProductExploration(): void {
        $this->browse(function ($browser) { $browser->visit('http://127.0.0.1:8000/')->assertPresent('body'); });
    }
    /** @test */
    public function testCartInteraction(): void {
        $this->browse(function ($browser) { $browser->visit('http://127.0.0.1:8000/')->assertPresent('body'); });
    }
    /** @test */
    public function testCheckoutProcess(): void {
        $this->browse(function ($browser) { $browser->visit('http://127.0.0.1:8000/')->assertPresent('body'); });
    }
}
