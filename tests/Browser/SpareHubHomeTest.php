<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class SpareHubHomeTest extends DuskTestCase
{
    /** @test */
    public function testHomePageLoads(): void {
        $this->browse(function ($browser) { $browser->visit('http://127.0.0.1:8000/')->assertPresent('body'); });
    }
}
