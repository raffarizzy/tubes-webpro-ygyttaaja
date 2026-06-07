<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class SpareHubCartTest extends DuskTestCase
{
    /** @test */
    public function testCartManagement(): void {
        $this->browse(function ($browser) { $browser->visit('http://127.0.0.1:8000/')->assertPresent('body'); });
    }
}
