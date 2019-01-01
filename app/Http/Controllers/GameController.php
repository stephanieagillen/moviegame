<?php

namespace App\Http\Controllers;

use App\Game;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class GameController extends Controller
{
    //return all of the games for the games.vue component
    public function list(){
        $games=Game::where(['user_id'=>Auth::user()->id])->get();
        return response()->json([
            'games' => $games,
            ], 200);
    }

    //return the information for the selected game
    public function show($id){
        $game=Game::where(
            ['id'=>$id],
            ['user_id'=>Auth::user()->id]
            )->get();

        return response()->json([
            'game' => $game,
            ], 200);
    }

    public function create() {

    }

    //Save new game to database
    public function store(Request $request){
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	return Game::create([
    		'name' => request('name'),
    	 	'user_id' => Auth::user()->id,
    	 	'players' => (null),
    		'movies' => (null),
			'guesses' => (null),
			'scores' => (null),
			'critic_scores' => (null),
            'overall_scores' => (null),
    		]);
    }

    public function edit(Games $game){

    }

    public function update(Request $request, $id){
    	$this->validate($request, [
    		'name'	=>	'required|max:255'
    	]);

        $game = Game::findOrFail($id);

    	$game->players = request('players');
    	$game->save();
    	return response()->json([
    		'message' => 'Game updated successfully!'
    	], 200);
    }

    public function destroy(Games $game){
    	return response()->json([
    		'message' => 'Game deleted successfully!'
    	], 200);
    }

    public function __contruct() {
    	$this->middleware('auth');
    }

    //individual game
    public function game(){
        return view('game');
    }

    //Add new player to database
    public function addPlayer(Request $request, $id){
        //return $id;
        $game=Game::where(
            ['id'=>$id],
            ['user_id'=>Auth::user()->id]
            )->first();

        $game->players = request('players');
        $game->overall_scores = request('overall_scores');
        $game->guesses = request('guesses');
        $game->scores = request('scores');
        $game->save();
        return response()->json([
            'message' => 'Game updated successfully!'
        ], 200);
    }

    public function addMovie(Request $request, $id){
        //return $id;
        $game=Game::where(
            ['id'=>$id],
            ['user_id'=>Auth::user()->id]
            )->first();

        $game->movies = request('movies');
        $game->guesses = request('guesses');
        $game->scores = request('scores');
        $game->critic_scores = request('critic_scores');
        $game->save();
        return response()->json([
            'message' => 'Game updated successfully!'
        ], 200);
    }

    public function calculate(Request $request, $id){
        //return $id;
        $game=Game::where(
            ['id'=>$id],
            ['user_id'=>Auth::user()->id]
            )->first();

        $game->guesses = request('guesses');
        $game->critic_scores = request('critic_scores');
        $game->scores = request('scores');
        $game->overall_scores = request('overall_scores');
        $game->save();
        return response()->json([
            'message' => 'Game calculated successfully!'
        ], 200);
    }
}
