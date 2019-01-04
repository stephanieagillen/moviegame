<template>
	<div class="container" id="game-page">
		<div class="row">
			<div class="col-md-12">
			<a href="/home#/"><p>Back to Games</p></a>
			<button type="button" @click="showInstructions()" class="btn btn-success">Instructions</button>
				<div class="panel panel-default mt-5 mb-3">
				<h1>{{ name }}</h1>
				</div>
				<div class="table">
					<div class="row">
						<div class="col-md-4">
							<form @submit.prevent="addPlayer" method="post">
								<input type="text" v-model="newPlayer" name="player"><button class="button btn btn-success">Add player</button>
							</form>
							<div class="d-inline-block mr-4" v-if="players.length > 0">
								<u><p>Player</p></u>
								<div v-if="players">
									<div v-for="(player, index) in this.players">
										<p>{{ player }}</p>
									</div>
								</div>
							</div>
							<div class="d-inline-block" v-if="players.length > 0">
								<u><p>Overall Score</p></u>
								<div v-if="overall_scores.length > 0">
									<div v-for="(score, index) in this.overall_scores">
										<p>{{ score }}</p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-2">
						</div>
						<div class="col-md-4">
							<div v-if="players.length > 0">
								<form @submit.prevent="addMovie" method="post">
									<input type="text" v-model="newMovie" name="movie"><button class="button btn btn-success">Add Movie</button>
								</form>
							</div>
							<div v-if="movies">
								<div v-for="(movie, index) in this.movies">
									<button @click="initPlayMovie(index)" class="btn-movie mb-3 p-2">{{ movie }}</button> 
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" tabindex="-1" role="dialog" id="play_movie_model">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Play Game</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
								<div class="form-group">
								<div class="d-inline-block">
									<div v-for="(player, index) in players" v-if="guesses.length > 0">
										<p><label for="guess">{{ player }}:</label></p>
									</div>
								</div>
								<div class="d-inline-block">
									<div v-for="(player, index) in players" v-if="guesses.length > 0">
										<input type="number" name="guess" id="guess" placeholder="Enter Guess" class="guess" v-model.number="guesses[movie_location][index]"></input>
									</div>
								</div>
									<div>
										<label for="critic_score"><strong>Critic Score:</strong></label>
										<input type="number" name="critic_score" id="critic_score" placeholder="Enter Critic Score" v-model.number="critic_scores[movie_location]"></input>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" @click="calculateMovie" class="btn btn-success">Submit</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" tabindex="-1" role="dialog" id="instructions_model">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Instructions</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
								<p>Enter in each player's guess, then enter the critics score. The difference between the two will be calculated in order to determine each player's score. If you guess the correct score, you will be rewarded by subtracting 5 points.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data(){
		return {
			name: '',
			newPlayer: '',
			players: [],
			newMovie: '',
			movies: [],
			newGuess: '',
			guesses: [],
			scores: [],
			critic_scores: [],
			overall_scores:[],
			movie_location: 0,
		}
	},
	mounted() {
		this.readGame();
	},
	methods: {
		readGame(){
			let id = window.location.href.split('/home#/games/').pop();
			axios.get('/games/'+ id)
				.then(response => {
					this.name = response.data.game[0].name;

					if(response.data.game[0].players != null ) {
						this.players = response.data.game[0].players;
					}
					if(response.data.game[0].movies != null ) {
						this.movies = response.data.game[0].movies;
					}
					if(response.data.game[0].guesses != null ) {
						this.guesses = response.data.game[0].guesses;
					}
					if(response.data.game[0].scores != null ) {
						this.scores = response.data.game[0].scores;
					}
					if(response.data.game[0].critic_scores != null ) {
						this.critic_scores = response.data.game[0].critic_scores;
					}
					if(response.data.game[0].overall_scores != null ) {
						this.overall_scores = response.data.game[0].overall_scores;
					}
			});
		},
		addPlayer(){
			let newPlayer = this.newPlayer;
			this.players.push(newPlayer);
			this.newPlayer = '';
			this.overall_scores.push(0);
			console.log(this.movies.length);

			if(this.movies.length > 0) {
				console.log('in if statement');
				for(let i=0; this.movies.length>i; i++) {
						this.scores[i].push(0);
						this.guesses[i].push(0);
				}
			}
			//update players with newPlayer

			let id = window.location.href.split('/home#/games/').pop();
			axios.post('/games/' + id + '/add-player', {
				players: this.players,
				overall_scores: this.overall_scores,
				guesses: this.guesses,
				scores: this.scores
			})
				.then(response => {
					this.reset();
					this.players.push(response.data);
					this.overall_scores.push(response.data);
					this.guesses.push(response.data);
					this.scores.push(response.data);
					//go to the game page
				})
			.catch(error => {
                   this.errors = [];
                   if (error.response.data.errors && error.response.data.errors.name) {
                       this.errors.push(error.response.data.errors.name[0]);
                   }
			if (error.response.data.errors && error.response.data.errors.description)
                  {
                       this.errors.push(error.response.data.errors.description[0]);
                   }
               });
           },
		addMovie(){
			let newMovie = this.newMovie;
			this.movies.push(newMovie);

			let movie_location = this.movies.length-1;
			let players = this.players;

			//determine if previous players have been added
			let guess=[];
			let score=[];

			for(let j=0; this.players.length>j; j++){
					score.push(0);
					guess.push(0);
				}

			this.guesses.push(guess);
			this.scores.push(score);
			this.critic_scores.push("0");
			console.log(this.critic_scores);
			this.newMovie = '';
			//update players with newPlayer
			let id = window.location.href.split('/home#/games/').pop();
			axios.post('/games/' + id + '/add-movie', {
				movies: this.movies,
				guesses: this.guesses,
				scores: this.scores,
				critic_scores: this.critic_scores
			})
				.then(response => {
					this.reset();
					this.movies.push(response.data);
					this.guesses.push(response.data);
					this.scores.push(response.data);
					this.critic_scores.push(response.data);
					//$("#play_movie_model").modal("hide");
					//go to the game page
				})
			.catch(error => {
                   this.errors = [];
                   if (error.responfse.data.errors && error.response.data.errors.name) {
                       this.errors.push(error.response.data.errors.name[0]);
                   }
			if (error.response.data.errors && error.response.data.errors.description)
                  {
                       this.errors.push(error.response.data.errors.description[0]);
                   }
               });

		},
		initPlayMovie(index){
			$("#play_movie_model").modal("show");
			this.movie_location = index;
		},
		showInstructions(){
			$("#instructions_model").modal("show");
		},
		calculateMovie(){
			let players = this.players;
			let movies = this.movies;
			let scores = this.scores;
			let critic_scores = this.critic_scores;
			let guesses = this.guesses;
			let overall_scores=this.overall_scores;
			console.log(critic_scores);

			for(var i=0; i < this.movies.length; i++){
				for(var j=0; j < this.players.length; j++){
					this.scores[i][j] = Math.abs(this.critic_scores[i]-this.guesses[i][j]);
					if ((this.scores[i][j] == 0) && (typeof this.critic_scores[i]) === 'number') this.scores[i][j] = -5;
				}
			}

			for(var j=0; j<this.players.length; j++){
				this.overall_scores[j] = 0;
			}

			for(var j=0; j<this.players.length; j++){
				for(var i=0; i<this.movies.length; i++){
					this.overall_scores[j] += this.scores[i][j];
				}
			}
			let id = window.location.href.split('/home#/games/').pop();
			axios.post('/calculate/' + id, {
				guesses: this.guesses,
				critic_scores: this.critic_scores,
				scores: this.scores,
				overall_scores: this.overall_scores
			})
				.then(response => {
					$("#play_movie_model").modal("hide");
					this.$forceUpdate();
					this.reset();
					this.guesses.push(response.data);
					this.critic_scores.push(response.data);
					this.scores.push(response.data);
					this.overall_scores.push(response.data);
					
					//go to the game page
				})
			.catch(error => {
			       this.errors = [];
			       if (error.response.data.errors && error.response.data.errors.name) {

			           this.errors.push(error.response.data.errors.name[0]);
			       }
			if (error.response.data.errors && error.response.data.errors.description)
			      {
			           this.errors.push(error.response.data.errors.description[0]);
			       }
			   });
       }
    }
}
</script>