<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LendingController extends Controller
{
    //alap fg-ek
    public function index()
    {
        return Lending::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $record = new Lending();
        $record->fill($request->all());
        $record->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id, $copy_id, $start)
    {
        $lending = Lending::where('user_id', $user_id)
        ->where('copy_id', $copy_id)
        ->where('start', $start)
        //listát ad vissza:
        ->get();
        return $lending[0];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $copy_id, $start)
    {
        $record = $this->show($user_id, $copy_id, $start);
        $record->fill($request->all());
        $record->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id, $copy_id, $start)
    {
        $this->show($user_id, $copy_id, $start)->delete();
    }

    //egyéb lekérdezések
    public function lendingsWithCopies(){
        $user = Auth::user();	//bejelentkezett felhasználó
        //copies: fg neve!!!
        return Lending::with('copies')
        ->where('user_id','=', $user->id)
        ->get();
    }
    public function dateSpecific(){
        return Lending::with('specificDate')
        ->where('start','=',"2016-07-11" )
        ->get();
    }
    public function copySpecific($copy_id){
        return Lending::with('copies')
        ->where('copy_id','=', $copy_id)
        ->get();
    }

    public function lendingCount(){
        $user=Auth::user();
        $lendings=DB::table('lendings as 1')
        ->where('user_id, $user->id')
        ->count();
        return $lendings;
    }

    //hány aktiv kölcsönzés van
    public function activeLendingCount(){
        $user=Auth::user();
        $lendings=DB::table('lendings as 1')
        ->where('user_id, $user->id')
        ->whereNull('end')
        ->count();
        return $lendings;
    }

    //kölcsönzött könyvek száma
    public function lendingsBooksCount(){
        $user=Auth::user();
        $books=DB::table('lendings as 1')
        ->join('copies as c','1.copy_id','c.copy_id')
        ->where('user_id, $user->id')
        ->distinct('book_id')
        ->count();
        return $books;
    }

     //kölcsönzött könyvek adatai
     public function lendingsBooksData(){
        $user=Auth::user();
        $books=DB::table('lendings as 1')
        ->join('copies as c','1.copy_id','c.copy_id')
        ->join('books as b','c.book_id','b.copy_id')
        ->select('b.book_id','author','title')
        ->where('user_id, $user->id')
        ->groupBy('b.book_id')
        ->count();
        return $books;
    }
//könyvenként csoportosítsd, csak azokat, amik max 1 példányban van nálam!
    public function lendingsBooksOne(){
        $user=Auth::user();
        $books=DB::table('lendings as 1')
        ->join('copies as c','1.copy_id','c.copy_id')
        ->join('books as b','c.book_id','b.copy_id')
        ->select('b.book_id','author','title')
        ->where('user_id, $user->id')
        ->groupBy('b.book_id')
        ->count();
        return $books;
    }

    public function lendingsBooksOnee()
{
    $user = Auth::user();
    // Lekérdezzük a könyveket, amelyekből maximum 1 példány van a felhasználó raktárában
    $books = DB::table('lendings as l')
    ->join('copies as c','1.copy_id','c.copy_id')
    ->join('books as b','c.book_id','b.copy_id')
    ->selectRaw('count(*) as ennyiszer, b.book_id, author,title')
    ->where('user_id, $user->id')
    ->groupBy('b.book_id')
    ->havingRaw('ennyiszer <2')
    ->get();
    return $books;
}

public function hardCoveredBooks(){
    $books=DB::table('books as b')
    ->join('copies as c', 'b.book_id', 'c.book_id')
    ->select('author', 'title')
    ->where('hardcovered',$value=0)
    ->distinct
}
}
