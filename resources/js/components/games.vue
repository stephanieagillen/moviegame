<template>
	<div class="container" id="games-page">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading"><br>
						<button @click="initAddGame()" class="btn btn-success">Add New Game</button>
					</div>
					<div class="panel-body">
					<table class="table table-bordered table-striped table-responsive" v-if="games.length > 0">
						<tbody>
							<tr>
								<th></th>
								<th>No.</th>
								<th>Name</th>
							</tr>
							<tr v-for="(game, index) in games">
								<td><button type="button" @click="deleteGame(game.id)"><span aria-hidden="true">&times;</span></button></td>
								<td><p>{{ index + 1 }}</p></td>
								<td>
								<router-link :to="{ name: 'gameIndex', params: { id: game.id}}"><p>{{ game.name }}</p></router-link>
								</td>
							</tr>
						</tbody>
					</table>
					</div>
					<div class="modal fade" tabindex="-1" role="dialog" id="add_game_model">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

								</div>
								<div class="modal-body">
									<div class="form-group">
										<label for="name">New Game:</label>
										<form @submit.prevent="createGame" method="post" class="mb-2">
											<input type="text" v-model="game.name" name="movie" placeholder="Game Name"><button class="button btn btn-success">Add game</button>
										</form>
									</div>
								</div>
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
		data() {
			return {
				game: {
					name: ''
				},
				games:[]
			}
		},
		mounted() {
			this.readGames();
		},
		methods: {
			initAddGame(){
				//show game model
				$("#add_game_model").modal("show");
			},
			createGame(){
				//create a new game in the database
				axios.post('/games', {
					name: this.game.name
				})
					.then(response => {
						let game_id = response.data.id;
						this.reset();
						this.games.push(response.data);
						$("#add_game_model").modal("hide");
						//go to the game
						this.$router.push({ name: 'gameIndex', params: { id: game_id}});
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
           deleteGame(id){
           		//delete game in database
				axios.delete('/games/' + id)
					.then(response => {
						this.readGames();
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
			reset(){
				this.game.name='';
			},
			readGames(){
				//retrieve all of the users games from the database
				axios.get('/games-list')
					.then(response => {
						this.games = response.data.games;
					});
			}
		}
	}
</script>