<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['isGuest', 'currencies'])
        },
        methods: {
            open(game_id, game_data) {
                Bus.$emit('modal:new', {
                    name: 'overview',
                    component: {
                        computed: {
                            ...mapGetters(['isGuest', 'channel', 'usd', 'currencies'])
                        },
                        data() {
                            return {
								game_id: game_id,
                                game_data: game_data
                            }
                        },
                        created() {
                            axios.post('/api/game/info/' + game_id).then((data) => this.response = data.data);
                        },
                        methods: {
                            close() {
                                Bus.$emit('modal:close');
                            }
                        },
                        template: `
                            <div>
                                <loader v-if="!game_id"></loader>

                                <div class="heading-overview" v-if="game_id">
                                    #{{ game_id }}
                                </div>
                                <template v-if="game_data">
								<div class="fair-info">
                                    <div class="client_seed mt-2">
                                        <div>{{ JSON.stringify(game_data) }}</div>
                                    </div>
								</div>
                                </template>
                            </div>
                        </div>`
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.overview {
        width: 450px;

        .overview-share-options {
            display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			align-content: center;
			justify-content: space-around;
			margin-top: 10px;
			margin-bottom: 10px;
			
			a {
				display: inline-flex;
				width: 30px;
				height: 30px;
				border-radius: 5px;
				cursor: pointer;
				margin-right: 10px;
				align-items: center;
				justify-content: center;
				@include themed() {
					box-shadow: rgb(0 0 0 / 25%) 2px 2px 8px 0px;
				}
				transition: box-shadow 0.3s ease, color 0.3s ease;
			}
			
        }
		
		.heading-overview {
			font-size: 1.2em;
			padding: 5px;
			display: flex;
		}
		
		.overview-player {
			text-align: center;
		}
		
		.heading-overview, .overview-player {
			justify-content: center;
			align-items: center;
		}
		.overview-bet {
			display: flex;
			margin-top: 5px;
	
			.option {
				padding: 7px;
				width: 33.33%;
				display: inline-flex;
				border: 1px solid rgb(0 0 0 / 10%);
				border-radius: 3px;
				margin: 5px;
				flex-direction: column;
				text-align: center;
				justify-content: center;
				align-items: center;
				
				span {
					white-space: nowrap;
					width: 90%;
					text-overflow: ellipsis;
					overflow: hidden;
				}
				
			}
		}
		
		.fair-info {
		    margin-top: 10px;
			border-radius: 3px;
			border: 1px solid rgb(0 0 0 / 10%);
			padding: 2px;
			display: flex;
			flex-direction: column;
			text-align: center;
		}

        .option span {
            white-space: nowrap;
            width: 90%;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .nonVerifiable {
            text-align: center;
            color: lightgray;
        }
    }
</style>
