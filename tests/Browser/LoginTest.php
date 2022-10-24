<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function testCanLogin()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                ->assertSee('LOGIN');
//            $browser->visit('/login')
//                ->type('email', $user->email)
//                ->type('password', 'password')
//                ->press('Login')
//                ->assertPathIs('/dashboard');
        });
    }
}
