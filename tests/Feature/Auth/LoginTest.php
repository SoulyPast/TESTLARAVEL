<?php

namespace Tests\Feature\Auth;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use WithoutMiddleware;
class LoginTest extends TestCase
{
  
  // Login correcte (quan faci un POST amb dades correctes, comprovi que faci un codi de redirecció).
    public function testLogin()
    {
        $credential = [
            'email' => 'mssoulaimane@gmail.com',
            'password' => 'soulaimane'
        ];

        $response = $this->post('login',$credential);
        $response->assertRedirect('/catalog');
    }

    //Login erroni (enviar POST sense dades, comprova que el camp que falla és el camp email).
    public function testDoesNotLoginAnInvalidemail()
    {
        $credential = [
            'email' => '',
            'password' => ''
        ];

        $response = $this->post('login',$credential);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    //Accedir a tot el llistat del catàleg (petició GET i que carregui la vista del catàleg).
    public function testcatalog()
    {
        $this->withoutMiddleware();
        $response = $this->get('/catalog');
        $response->assertStatus(200);
        $response->assertViewis('catalog.index');
    }

    //Accedir al detall de la 1a pel·lícula. (petició GET i que carregui la vista de detall).
    public function testcatalog1()
    {
        $this->withoutMiddleware();
        $response = $this->get('/catalog/show/1');
        $response->assertStatus(200);
        $response->assertViewis('catalog.show'); 
    }

    //Afegir un comentari (buit sense dades).
    public function testcatalogsindades()
    {
      $user = User::find(1);
      $this->actingAs($user);
        $response = $this->post('/catalog/show/1',[
            'title' => '',
            'review' => '',
            'stars' =>'',
          ]);
          $this->assertDatabaseHas('reviews',[
            'title' => NULL
          ]);
          
    }

    // Afegir un comentari (amb dades preestablertes). Verificar també la Base de Dades.
    public function testcatalognew()
    {
      $user = User::find(1);
      $this->actingAs($user);
        $response = $this->post('/catalog/show/1',[
            'title' => 'no estoy rapido mierda',
            'review' => 'el final',
            'stars' => '1',
          ]);

          $this->assertDatabaseHas('reviews',[
            'title' => 'no estoy rapido mierda'
          ]);
    }

    //Canviar l'estat d'una pel·lícula a "alquilada" i viceversa (via API).
    public function testalquilada(){
      $this->withoutMiddleware();
      $response = $this->json('PUT','api/v1/catalog/2/rent');
      $response->assertStatus(200);
      
    }
    
    public function testdevuleta(){
      $this->withoutMiddleware();
      $response = $this->json('PUT','api/v1/catalog/2/return');
      $response->assertStatus(200);
    }
    
   // Editar correctament una pel·lícula (via web, no API).
    public function testupdatepeli()
    {
        $user = User::find(1);
        $this->actingAs($user);
        $response = $this->put('catalog/edit/1',[
        'title' => 'mi ultima esperanza',
        'year' => '2020',
        'director' => 'soulaimane mastour',
        'poster' => 'mi ultima esperanza',
        'synopsis' => 'mi ultima esperanza',
        'categoria' => 'Terror'
      ]);

      $this->assertDatabaseHas('movies',[
        'title' => 'mi ultima esperanza'
      ]);
    }

// Afegir una pel·lícula sense dades (no ha de "petar"...).
    public function testsinepeli()
    {
        $user = User::find(1);
        $this->actingAs($user);
        $response = $this->post('catalog/create',[
        'title' => '',
        'year' => '',
        'director' => '',
        'poster' => '',
        'synopsis' => '',
        'categoria' => 'Terror'
      ]);
      $this->assertDatabaseHas('movies',[
        'title' => null
      ]);
      
    }
 //Afegir una pel·lícula via API (s'envien les dades necessàries). Verificar també la Base de Dades.
    public function testpeliculaapi(){       
      $this->withoutMiddleware();
      $response = $this->json('POST','/api/v1/catalog', [
              'title' => 'soulay',
              'year' => 2020,
              'director' => 'soulay',
              'poster' => 'poster',
              'rented' => 0,
              'synopsis' => 'test API'
            ]);
        $this->assertDatabaseHas('movies',[
            'director' => 'soulay'
          ]);
        }
    
}
