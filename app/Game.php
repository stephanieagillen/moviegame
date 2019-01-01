<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
        protected $fillable = [
    	'name',
    	'user_id',
    	'players',
    	'movies',
    	'guesses',
    	'scores',
    	'critic_scores',
        'overall_scores'
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }


    protected $casts = [
        'players' => 'array',
        'movies' => 'array',
        'guesses' => 'array',
        'scores' => 'array',
        'critic_scores' => 'array',
        'overall_scores' => 'array'
    ];


}
