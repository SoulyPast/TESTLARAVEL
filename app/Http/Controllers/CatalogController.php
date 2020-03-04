<?php

namespace App\Http\Controllers;
use App\Movie;
use App\Reviews;
use App\Categorie;
use Illuminate\Http\Request;
use Notify;
use DB; 
use Laranotify;
use Illuminate\Support\Facades\Auth;
class CatalogController extends Controller
{
      
    public function search(Request $request){
        $search =$request->get('search');
        $peliculas=Movie::where('title','like','%'.$search.'%')->paginate(15);
        return view('catalog.index', array( 'peliculas' => $peliculas));
    }

    public function millor(Request $request){
        $reviews=Reviews::Select('movie_id',DB::raw('AVG(stars) as quantity'))->groupBy('movie_id')->get();
        $reviews = collect($reviews)->sortBy('quantity')->reverse()->take(3);
        return view('catalog.millor', array( 'reviews' => $reviews));
    }

   
    public function getIndex(){
        $peliculas=Movie::all();
        return view('catalog.index', array( 'peliculas' => $peliculas));
    }

    public function getShow($id){
        $pelicula=Movie::findOrFail($id);
        $reviews=Reviews::all()->where('movie_id','=',$id);
        return view('catalog.show',array( 'pelicula' => $pelicula),array('reviews' => $reviews));

    }

    public function postShow(Request $request ,$id){
        $review = new Reviews;
        $review->title = $request->input('title');
        $review->review = $request->input('review');
        $review->stars = $request->input('stars');
        $review->user_id = Auth::id();;
        $review->movie_id = $id;
        $review->save();
        return redirect()->to('/catalog/show/'.$id);
}




    public function getCreate(){
        $Categories=Categorie::all();
        return view('catalog.create',array( 'Categories' => $Categories));
    }

    public function getEdit($id){
        $Categories=Categorie::all();
        $pelicula=Movie::findOrFail($id);
        return view('catalog.edit', array('pelicula'=>$pelicula),array( 'Categories' => $Categories));
    }

    public function postCreate(Request $data){
        $Categories=Categorie::where('title','=',$data['categoria'])->pluck('id');
        Movie::create([
            'title' => $data['title'],
            'year' => $data['year'],
            'director' => $data['director'],
            'poster' => $data['poster'],
            'category_id' =>$Categories[0],
            'synopsis' => $data['synopsis'],
            'rented' => false
            ]);
            Notify::success("La película se ha guardado correctamente");
            return redirect('/catalog');
    }
    public function putEdit(Request $request, $id){
        $Categories=Categorie::where('title','=',$request->input('categoria'))->pluck('id');
        $request['categoria']=$Categories[0];
        $movie=Movie::findOrFail($id);
            $movie->title = $request->input('title');
            $movie->year = $request->input('year');
            $movie->director = $request->input('director');
            $movie->poster = $request->input('poster');
            $movie->synopsis = $request->input('synopsis');
            $movie->category_id = $request->input('categoria');
            $movie->save();
            Notify::success("La película se ha modificado correctamente");
            return redirect()->to('/catalog/show/'.$id);
       
    }

    public function putRent($id){
            $movie=Movie::findOrFail($id);
            $movie->update(['rented' => TRUE]);
            notify()->success('Pelicula Rentada')->delay(1500);
            return redirect()->action('CatalogController@getShow', [$id]);
    }

    public function putReturn($id)
    {
            $movie=Movie::findOrFail($id);
            $movie->update(['rented' => FALSE]);
            notify()->warning('Pelicula Devuelta')->delay(1500);
            return redirect()->action('CatalogController@getShow', [$id]);
    }
    
    public function deleteMovie($id)
    {
            $movie=Movie::findOrFail($id);
            $movie->delete();
            notify()->error('Pelicula Borrada')->delay(1500);
            return redirect('catalog');
    }
}