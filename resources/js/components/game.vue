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
						<div class="col-lg-4 col-sm-8">
							<form @submit.prevent="addPlayer" method="post" class="mb-2">
								<input required type="text" v-model="newPlayer" name="player" placeholder="Enter player"><button class="button btn btn-success">Add player</button>
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
						<div class="col-lg-2 col-sm-0">
						</div>
						<div class="col-lg-4 col-sm-8">
							<div v-if="players.length > 0">
								<form @submit.prevent="addMovie" method="post" class="mb-2">
									<input required type="text" v-model="newMovie" name="movie" placeholder="Enter movie"><button class="button btn btn-success btn-add">Add Movie</button>
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
										<input type="number" name="guess" id="guess" placeholder="Enter Guess" v-bind:min="0" v-bind:max="100" class="guess" v-model.number="guesses[movie_location][index]"></input>
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
								<p>Enter all of the players. Enter all of the movies. Click on each movie and have each player guess the movie rating. Enter in the critics rating from your favorite movie rating source(e.g. Rotten Tomatoes, IMDB, etc.) The absolute difference between the player's guess and the critic's rating will be calculated in order to determine each player's score. If a player guesses the critic's rating, he or she will be rewarded with a score of -5. The scores from all of the movies will determine each player's overall score.  The player with the lowest score wins!</p>
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
			//Retrieve game information from database
			const id = window.location.href.split('/home#/games/').pop();
			axios.get('/games/'+ id)
				.then(response => {
					//Assign the response data to the variable 'response_data'
					const response_data = response.data.game[0];

					//Assign game information to the view if the key is not null 
					for(let[key, value] of Object.entries(response_data)){
						if(response_data[key] != null) this[key] = value;
					}
				})
				.catch(error => {
                   console.error(error);
               });
		},
		addPlayer(){
			//Retrieve the newPlayer input value and push it to the player array
			let newPlayer = this.newPlayer;
			this.players.push(newPlayer);
			this.newPlayer = '';

			//Add an index to the overall_scores array
			this.overall_scores.push(0);

			//If there are movies, add indexes to the scores and guesses arrays
			if(this.movies.length > 0) {
				for(let [i,movie] of this.movies.entries()){
					this.scores[i].push(0);
					this.guesses[i].push(0);
				}
			}

			//Push updated information to the database
			let id = window.location.href.split('/home#/games/').pop();
			axios.post('/games/' + id + '/add-player', {
				players: this.players,
				overall_scores: this.overall_scores,
				guesses: this.guesses,
				scores: this.scores
			})
				.then(response => {
					console.log(response);
				})
				.catch(error => {
                   console.error(error);
               });
           },
		addMovie(){
			//Retrieve the newMovie value and push it to the movie array
			let newMovie = this.newMovie;
			this.movies.push(newMovie);

			let movie_location = this.movies.length-1;
			let players = this.players;
			let guess=[];
			let score=[];

			//Add an index to the overall_scores array
			for(let [i, player] of this.players.entries()) {
				score.push(0);
				guess.push(0);
			}

			//Add indexes to the scores and guesses arrays
			this.guesses.push(guess);
			this.scores.push(score);
			this.critic_scores.push("0");

			//Reset newMovie input variable
			this.newMovie = '';

			//Push updated information to the database
			let id = window.location.href.split('/home#/games/').pop();
			axios.post('/games/' + id + '/add-movie', {
				movies: this.movies,
				guesses: this.guesses,
				scores: this.scores,
				critic_scores: this.critic_scores
			})
				.then(response => {
					console.log(response);
				})
				.catch(error => {
                   console.error(error);
               });
		},
		initPlayMovie(index){
			//Show movie model
			$("#play_movie_model").modal("show");
			this.movie_location = index;
		},
		showInstructions(){
			//Show instructions model
			$("#instructions_model").modal("show");
		},
		calculateMovie(){
			//Loop through guesses array and critic_scores array to calculate the score of each player for each game
			for(let [i,movie] of this.movies.entries()){
				for([j,player] of this.players.entries()){
					//The score value is caluclated by taking the absolute value of the difference between critic_scores and guesses
					this.scores[i][j] = Math.abs(this.critic_scores[i]-this.guesses[i][j]);
					//If the score value returns 0, give the player a score of -5 
					if ((this.scores[i][j] == 0) && (typeof this.critic_scores[i]) === 'number') this.scores[i][j] = -5;
				}
			}

			//Reset the overall score values to 0 so they can be recalculated in the following function
			for(let [i,player] of this.players.entries()){
				this.overall_scores[i] = 0;
			}

			//Add all of the scores for each player
			for(let [j,player] of this.players.entries()){
				for(let [i,movie] of this.movies.entries()){
					this.overall_scores[j] += this.scores[i][j];
				}
			}

			//Push calculated information to the database
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
				})
				.catch(error => {
                   console.error(error);
               });
	   }
    }
}
</script>