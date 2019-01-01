<template>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<a href="/"><p>Back to Games</p></a>
				<div class="panel panel-default">
				<h1>Game:</h1>
				</div>
				<div class="table">
					<div class="row">
						<div class="col-md-4">
							<form @submit.prevent="addPlayer" method="post">
								<input type="text" v-model="newPlayer" name="player"><button class="button btn btn-success">Add player</button>
							</form>
							<div v-if="players">
								<div v-for="(player, index) in this.players">
									<p>{{ player }}</p>
								</div>
							</div>
							<div v-if="overall_scores.length > 0">
								<div v-for="(score, index) in this.overall_scores">
									<p>{{ score }}</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
						</div>
						<div class="col-md-4">
							<form @submit.prevent="addMovie" method="post">
								<input type="text" v-model="newMovie" name="movie"><button class="button btn btn-success">Add Movie</button>
							</form>
							<div v-if="movies">
								<div v-for="(movie, index) in this.movies">
									<button @click="initPlayMovie(index)">{{ movie }}</button> 
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" tabindex="-1" role="dialog" id="play_movie_model">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Play Game</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<div v-for="(player, index) in players" v-if="guesses.length > 0">
										<label for="guess">{{ player }}:</label>
										<input type="integer" name="guess" id="guess" placeholder="Enter Guess" class="guess" v-model="guesses[movie_location][index]"></input>
										
									</div>
									<div>
										<label for="critic_score">Critic Score:</label>
										<input type="integer" name="critic_score" id="critic_score" placeholder="Enter Critic Score" v-model="critic_scores[movie_location]"></input>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" @click="calculateMovie" class="btn btn-primary">Submit</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
			movie_location: 0

		}
	},
	mounted() {
		this.readGame();
	},
	methods: {
		readGame(){
			let id = window.location.href.split('/#/games/').pop();
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

			let id = window.location.href.split('/#/games/').pop();
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

			// //add guesses
			// let guess=[];
			// this.players.forEach(function(player, j){
			// 		guess.push(0);
			// })
			// this.guesses.push(guess);


			// //add scores
			// let score=[];
			// this.players.forEach(function(player, j){
			// 		score.push(0);
			// })
			// this.scores.push(score);

			//add critic scores
			this.critic_scores.push(0);
			

			this.newMovie = '';
			//update players with newPlayer
			let id = window.location.href.split('/#/games/').pop();
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
		calculateMovie(){

			let players = this.players;
			let movies = this.movies;
			let scores = this.scores;
			let critic_scores = this.critic_scores;
			let guesses = this.guesses;
			let overall_scores=this.overall_scores;


			for(var i=0; i < this.movies.length; i++){
				for(var j=0; j < this.players.length; j++){
					this.scores[i][j] = Math.abs(this.critic_scores[i]-this.guesses[i][j]);
					if (this.scores[i][j] == 0) this.scores[i][j] = -5;
					// if(this.scores[i][j] == 0){
					// 	this.scores[i][j] = -5;
					// }
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
			let id = window.location.href.split('/#/games/').pop();
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