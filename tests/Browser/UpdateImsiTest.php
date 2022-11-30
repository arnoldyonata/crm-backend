<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\CustomDuskTestCase;

class UpdateImsiTest extends CustomDuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function test_can_update_existing_imsi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->createNewImsi($browser);
            $imsi_id = \App\Models\Imsi::count();

            $old_imsi_number = '1234567890';
            $new_imsi_number = '1234567891';

            $browser
                ->visit(env('FRONTEND_URL')."/imsi/$imsi_id/edit")
                ->waitForText('Update Imsi')
                ->waitForText('UPDATE')
                ->pause(1000)
                ->assertValue('#imsi', $old_imsi_number)
                ->assertValue('#imsiStatusId', '1')
                ->assertValue('#imsiTypeId', '1')
                ->assertValue('#pin', '1234')
                ->assertValue('#puk1', '987654321')
                ->assertValue('#puk2', '987654322');

            $browser
                ->type('#imsi', '')
                ->typeSlowly('#imsi', $new_imsi_number)
                ->press('UPDATE')
                ->waitForText('Create New IMSI')
                ->assertPathIs('/imsi');

            $browser
                ->visit(env('FRONTEND_URL')."/imsi/$imsi_id/edit")
                ->waitForText('Update Imsi')
                ->waitForText('UPDATE')
                ->pause(1000)
                ->assertValue('#imsi', $new_imsi_number)
                ->assertValue('#imsiStatusId', '1')
                ->assertValue('#imsiTypeId', '1')
                ->assertValue('#pin', '1234')
                ->assertValue('#puk1', '987654321')
                ->assertValue('#puk2', '987654322');
        });
    }
}
