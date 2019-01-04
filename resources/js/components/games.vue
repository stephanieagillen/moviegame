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
										<input type="text" name="name" id="name" placeholder="Game Name" class="form-control" v-model="game.name">
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
									<button type="button" @click="createGame" class="btn btn-primary">Add Game</button>
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
				$("#add_game_model").modal("show");
			},
			createGame(){
				axios.post('/games', {
					name: this.game.name
				})
					.then(response => {
						let game_id = response.data.id;
						this.reset();
						this.games.push(response.data);
						$("#add_game_model").modal("hide");
						this.$router.push({ name: 'gameIndex', params: { id: game_id}});
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
           deleteGame(id){
				axios.delete('/games/' + id)
					.then(response => {
						this.readGames();
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
			reset(){
				this.game.name='';
			},
			readGames(){
				axios.get('/games-list')
					.then(response => {
						this.games = response.data.games;
					});
			}
		}
	}
</script>