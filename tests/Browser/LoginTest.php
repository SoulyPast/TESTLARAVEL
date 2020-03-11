<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function scrollTo()
    {
       return $this->driver->executeScript('window.scrollTo(0, 500);'); 
    }

    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser
            //Visita la pàgina
            ->visit('/videoclub/public/login')
            ->waitForText('Login')
            //Fes un login amb un usuari vàlid
            ->type('email', 'mssoulaimane@gmail.com')
            ->type('password', 'soulaimane')
            ->press('Login')
            ->assertPathIs('/videoclub/public/catalog')
            //una cerca d'una pel·lícula que no existeixi
            ->pause(2000)  
            ->type('search','resssss') 
            ->press('Search')
            //una cerca d'una pel·lícula que existeixi
            ->pause(2000) 
            ->type('search','Pulp Fiction') 
            ->press('Search')
            ->pause(2500)
            //Visualització de la pàgina de detall d'una pel·lícula trobada
            ->clickLink('Pulp Fiction')
            ->pause(2500)
            // scroll fins al final de la pàgina.
            ->driver->executeScript('window.scrollTo(0, 10000);');
            // Afegeix un comentari amb valoració 5 estrelles
            $browser->type('title', 'no esta bien')
            ->select('stars','5')
            ->type('review', 'no esta peliiii')
            ->press('Valorar')
            ->pause(2500)
            // una nova pel·lícula
            ->clickLink('Nueva película')
            ->type('title', 'va que rapido') 
            ->pause(2000)
            ->type('year', 1988)
            ->type('director', 'yooo')
            ->type('poster', 'https://images')
            ->type('synopsis', 'mucha cosa para hacer poco tiempo')
            ->select('categoria', 'Terror')
            ->press('Añadir película')
            //Sortir de la sessió
            ->pause(2000)
            ->press('Cerrar sesión')
            ->assertPathIs('/videoclub/public/login');
        });
    }
}

