<template>

<button @click="initAddGame()" class="btn btn-success">Add New Game</button>

	<div class="modal fade" tabindex="-1" role="dialog" id="add_game_model">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Add New Game</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="names">Name:</label>
						<input type="text" name="name" id="name" placeholder="Game Name" class="form-control" v-model="game.name">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
					<button type="button" @click="createGame" class="btn btn-primary">Submit</button>
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
			//this.readGames();
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
						this.reset();
						this.games.push(response.data);
						$("#add_game_model").modal("hide");
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
		}
	}

			
</script>