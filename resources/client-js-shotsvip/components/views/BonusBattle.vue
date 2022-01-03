		<template>
				<div class="gameCategory"  v-if="currencies.bonusBattle === 'true'">
					<div class="header">
						{{ $t('general.sidebar.bonusbattle') }}
						<div class="tabs-battle">
							<template v-if="tab !== 'lobby'">
								<div class="tabs-status">
									<div :class="`tabs-available extra-letter-spacing ${tab === 'available' ? 'active' : ''}`" @click="tab = 'available'">Available</div>
									<div :class="`tabs-active extra-letter-spacing ${tab === 'active' ? 'active' : ''}`" @click="tab = 'active'">In-play</div>
									<div :class="`tabs-completed extra-letter-spacing ${tab === 'completed' ? 'active' : ''}`" @click="tab = 'completed'">Completed</div>
									<div v-if="user" :class="`tabs-withme extra-letter-spacing ${tab === 'withme' ? 'active' : ''}`" @click="tab = 'withme'">My Battles</div>
								</div>
								<div class="btn-group">
									<button class="btn btn-primary" @click="OpenBonusBattleModal">Create</button>
								</div>
							</template>
							<div v-else class="tabs-status">
								<div class="tabs-available" @click="$router.push(`/bonusbattle`)">Back to list</div>
							</div>
						</div>
					</div>
				<template v-if="!pageLoading">
				<div class="container-fluid" style="max-width: 1600px; margin: 0px auto;">
					<div class="row mt-3">
						<div class="warning" v-if="(Object.keys(battles).length === 0) && tab !== 'lobby'">
							There are no open lobbies currently.
							<hr>
							<button class="btn btn-primary" @click="OpenBonusBattleModal">Create a Bonus Battle</button>
						</div>
						<bonus-battle-lobby v-if="tab === 'lobby'" :lobby="lobby"></bonus-battle-lobby>
						<div v-else class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3" v-for="battle in battles">
							<div class="card">
								 <div v-if="battle.room_state === 'completed' || battle.room_state === 'cancelled'">
										<div class="unavailable">
											<div class="slanting" @click="lobbyOpen(battle.room_id)">
												<div class="unavailableContent" v-html="$t('bonusbattle.completed')"></div>
												<div class="won" v-if="battle.winner_name && battle.winner_amount > '0'">{{ battle.winner_name }} won ${{ parseFloat(battle.winner_amount).toFixed(2) }} <icon :icon="currencies[battle.currency].icon" :style="{ color: currencies[battle.currency].style }"></icon></div>
												<hr>
												<div class="card-body"> 
													<div class="text-muted mb-4">     
														   <h4><a href="javascript:void(0)" class="cardHeader">{{ battle.stake }}$ BONUS BUY</a></h4>
																<div class="reward" v-if="battle.player_name_1">{{ battle.player_name_1 }} - {{ battle.player_final_1 }} -  {{ parseFloat(battle.player_balance_1).toFixed(2) }}$</div>
																<div class="reward" v-if="battle.player_name_2">{{ battle.player_name_2 }} - {{ battle.player_final_2 }} -  {{ parseFloat(battle.player_balance_2).toFixed(2) }}$</div>
																<div class="reward" v-if="battle.player_name_3">{{ battle.player_name_3 }} - {{ battle.player_final_3 }} -  {{ parseFloat(battle.player_balance_3).toFixed(2) }}$</div>
																<div class="reward" v-if="battle.player_name_4">{{ battle.player_name_4 }} - {{ battle.player_final_3 }} -  {{ parseFloat(battle.player_balance_4).toFixed(2) }}$</div>
														   <h4><a href="javascript:void(0)" class="cardHeader">{{ battle.g_name }}</a></h4>
													</div>
												</div>
										   </div>
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col-sm-auto">
											<ul class="list-inline mb-0">
												<li class="list-inline-item pr-2">
													<button v-if="user && (battle.winner_id === user.user._id && battle.claimed === false)" class="btn btn-primary" @click="claimBattle(battle.room_id)">
														Claim Winnings {{ parseFloat(battle.winner_amount).toFixed(2) }}$
													</button>
													<button v-if="user && (battle.winner_id === user.user._id && battle.claimed === true)" class="btn btn-secondary disabled" disabled>
														Claimed {{ parseFloat(battle.winner_amount).toFixed(2) }}$
													</button>
												</li>
											</ul> 
										</div>
									</div>
								</div> 
								</div>
								</div>
								<div class="card-body"> 
									<div :class="`badge bg-primaryBadge float-end`">{{ battle.stake }}$ <icon :icon="currencies[battle.currency].icon" :style="{ color: currencies[battle.currency].style }"></icon></div>
									<h4><a href="javascript:void(0)" class="cardHeader">{{ battle.g_name }}</a></h4>
									<div class="text-muted mb-4">
									 <img :class="`card-gameThumbnail`" :src="`${cdnBase}${battle.g_img}${cdnParameters}`">
										<hr>
										<div class="battle-desc">Battle Info  - ID{{ battle.room_id }}</div>
										<div class="battle-info">
											<div class="desc">Bonus Buy</div>
											<div class="info">{{ battle.stake }}$ <icon :icon="currencies[battle.currency].icon" :style="{ color: currencies[battle.currency].style }"></icon></div>
										</div>
										<div class="battle-info">
											<div class="desc">Size</div>
											<div class="info">{{ battle.players_active }} / {{ battle.players_max }} players</div>
										</div>
										<div class="battle-info">
											<div class="desc">Creator</div>
											<div class="info creator">{{ battle.player_name_1 }}</div>
										</div>
										<div class="battle-info">
											<div class="desc">Currency</div>
											<div class="info">{{ currencies[battle.currency].name }}</div>
										</div>
										<div class="battle-info">
											<div class="desc">State</div>
											<div class="info">{{ battle.room_state }}</div>
										</div>

									</div> 
								</div>
								<div class="card-body">
									<div class="d-grid gap-2">
										<button v-if="battle.room_state === 'joinable'" :key="battle.room_id" @click="lobbyOpen(battle.room_id)" v-bind:value="battle.room_id" class="btn btn-primary">
											View battle
										</button>
										<button v-if="battle.room_state === 'started'" :key="battle.room_id" @click="lobbyOpen(battle.room_id)" v-bind:value="battle.room_id" class="btn btn-primary">
											View battle
										</button>
										<button v-if="battle.room_state === 'active'" class="btn btn-primary" @click="lobbyOpen(battle.room_id)">
											View battle
										</button>
									</div>
								</div> 
								<div class="card-footer">
									<div class="d-grid gap-2">
										<button class="btn btn-secondary disabled loadingdots" v-if="battle.room_state === 'active'">
											In-play - Waiting on results
										</button>
										<button class="btn btn-secondary disabled loadingdots" v-if="battle.room_state === 'joinable'" @click="waitingFill()">
											Waiting for players
										</button>
										<button class="btn btn-secondary disabled loadingdots" v-if="battle.room_state === 'cancelled'" @click="waitingFill()">
											Cancelled
										</button>
										<button class="btn btn-secondary disabled loadingdots" v-if="battle.room_state === 'completed'" @click="waitingFill()">
											Completed
										</button>
									</div>
								</div>
							</div>
						</div> 
					</div>
				</div>
					<template v-if="!showLoading">
						<template v-if="(show.page * show.depth < show.count)">
								<div class="divider">
									<div class="line"></div>
									<div class="divider-title">
										<div class="show-more_progress-track">
											<div :style="{ width: ((show.page * show.depth / show.count) * 100) + '%' }" class="show-more_progress-bar"></div>
										</div>
										<div class="show-more_text">Shown <span> {{ show.page * show.depth }} </span> from <span>{{ show.count }}</span> battles</div>
										<button @click="loadBattles()" href="javascript:void(0)" class="btn show-more btn-primary"><i class="fas fa-random" aria-hidden="true"></i> Show more</button>				
									</div>
									<div class="line"></div>
								</div>
						</template>
					</template>
					<div v-else class="battle-load">
						<loader></loader>
					</div>
				</template>
				<div v-else class="battle-load">
					<loader></loader>
				</div>
				</div>
			</div>
		</template> 

<script> 
    import { mapGetters } from 'vuex';
    import Bus from "../../bus";
    import create_bonus_battle from "../modals/CreateBonusBattle";

    window.flatpickr = require('flatpickr').default;

    export default {
        data() {
            return {
				tab: 'available',
                battles: [],
				lobby : null,
				pageLoading: true,
				showLoading: true,
				show: {
					page: 0,
					depth: 12,
					count: 0
				}
            }
        },

        mounted() {
            Bus.$on('event:BonusBattleNew', (e) => {
            	this.getBattles();
            });
            Bus.$on('event:BonusBattleStarted', (e) => {
            	this.getBattles();
            });
            Bus.$on('event:BonusBattleUpdate', (e) => {
            	this.getBattles();
            });
        },
        watch: {
            tab() {
                this.loadTab();
            }
        },
        computed: {
            ...mapGetters(['cdnBase', 'cdnParameters', 'currencies', 'user'])
        },
        created() {
			if(this.$route.params.roomid !== undefined) {
				this.getBattleLobbyInfo();
			} else {
				this.getBattles();
			}
            flatpickr('#expires', {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true
            });
        }, 
		methods: {  
            joinBattle(id) {
				axios.post('/api/bonusbuybattle/joinBattle', { battleroom: id }).then(() =>  Bus.$emit('modal:close')).catch((error) => {
					switch (error.response.data.code) {
						case 1:
							this.$toast.error(this.$i18n.t('bonusbattle.errors.not_enough_balance'));
							break;
						case 2:
							this.$toast.error(this.$i18n.t('bonusbattle.errors.already_joined'));
							break;
						case 3:
							this.$toast.error(this.$i18n.t('bonusbattle.errors.started_already'));
							break;
						case 4:
							this.$toast.error(this.$i18n.t('general.bonusbattle.started_already'));
						break;
					}
				});
            },
            claimBattle(id) {
				axios.post('/api/bonusbuybattle/claimBattle', { battleroom: id }).then(() =>  Bus.$emit('modal:close')).catch((error) => {
					switch (error.response.data.code) {
						case 1:
							this.$toast.error(this.$i18n.t('bonusbattle.errors.claimed_already'));
							break;
					}
				});
            },
            waitingFill() {
                this.$toast.error(this.$i18n.t('general.bonusbattle.waiting_to_fill'));
            },
            getBattleLobbyInfo() {
            axios.post('/api/bonusbattle/getBonusBattleRoom', { room: this.$route.params.roomid}).then(({ data }) => {
					this.lobby = data;
					this.tab = 'lobby';
					this.pageLoading = false;
            		this.getBattles();
				}).catch((error) => {
					switch (error.response.data.code) {
						case 1:
							this.$router.push(`/bonusbattle`);
							break;
					}
				});
            },
            getBattles() {
				this.pageLoading = true;
				this.battles = [];
				this.show.page = 0;
				this.loadBattles();
            },
            OpenBonusBattleModal() {
                create_bonus_battle.methods.open();
            },
			loadTab() {
				if(this.tab !== 'lobby') {
					this.getBattles();
				}
			},
			lobbyOpen(room_id) {
				this.pageLoading = true;
				this.$router.push(`/bonusbattle/`+room_id);
			},
			loadBattles() {
				this.showLoading = true;
				if(this.tab == 'available') {
					status = 'joinable';
				} 
				else if(this.tab == 'active') {
					status = 'active';
				}
				else if(this.tab == 'completed') {
					status = 'completed';
				} 
				else if(this.tab == 'withme') {	
					status = 'withme';
				}
				axios.post('/api/bonusbattle/getBonusBattle', { status: status, page: this.show.page, depth: this.show.depth }).then(({ data }) => {
					this.show.page += 1;
					this.battles = this.battles.concat(data[0].battles);
					this.show.count = data[0].count;
					this.showLoading = false;
					this.pageLoading = false;
				});
			}
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";
	/* loading dots */

.loadingdots:after {
  content: ' .';
  animation: dots 2s steps(5, end) infinite;
}

@keyframes dots {
  0%, 20% {
    color: rgba(0,0,0,0);
    text-shadow:
      .25em 0 0 rgba(0,0,0,0),
      .5em 0 0 rgba(0,0,0,0);
  }
  40% {
    color: white;
    text-shadow:
      .25em 0 0 rgba(0,0,0,0),
      .5em 0 0 rgba(0,0,0,0);
  }
  60% {
    text-shadow:
      .25em 0 0 white,
      .5em 0 0 rgba(0,0,0,0);
  }
  80%, 100% {
    text-shadow:
      .25em 0 0 white,
      .5em 0 0 white;
      }
  }

	.battle-load {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 50px;
	}
	
	.battle-info {
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		padding-top: 8px;
		min-height: 32px;
		
		.info {
			@include themed() {
				color: t('text');
			}
		}
		
		.info.creator {
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}

		.desc {
			margin-right: auto;
			min-width: 110px;
			white-space: nowrap;
			@include themed() {
				color: t('link');
			}
		}
	}

	.tabs-user {
		margin: 0px 0px -25px;
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		flex-wrap: wrap;
		-webkit-box-pack: justify;
		justify-content: space-between;
	}

	.tabs-battle {
		margin: 0px 0px -25px;
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		flex-wrap: wrap;
		-webkit-box-pack: justify;
		justify-content: space-between;
	}

	.tabs-status {
		margin: 10px 0px 0px;
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		margin-bottom: 25px;
		white-space: nowrap;
		overflow: auto hidden;
	}

	.tabs-active, .tabs-available, .tabs-completed {
		margin-right: 20px;
	}
	
	.tabs-active, .tabs-available, .tabs-completed, .tabs-withme {
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		height: 30px;
		min-height: 30px;
		padding-bottom: 6px;
		cursor: pointer;
		user-select: none;
		@include themed() {
			color: t('link');
		}
		text-transform: uppercase;
		font-size: 16px;
		font-weight: 600;
		font-style: normal;
	}
	
	.tabs-available.active, .tabs-active.active, .tabs-completed.active, .tabs-withme.active {
		@include themed() {
			border-bottom: 2px solid t('secondary');
        }
		@include themed() {
			color: t('secondary');
		}
	}

	.progress {
        @include themed() {
        background-color: t('secondary');
        }
    }
	.btn.show-more {
		padding: 8px 20px;
	}
	
    .warning {
        width: 100%;
        text-align: center;
        font-size: 1.1em;
        margin-top: 15px;
        margin-bottom: 15px;

     }

	.bg-primaryBadge {
	 @include themed() {
        background-color: t('body');
        transition: all 0.3s ease;
        opacity: 0.9;
        color: t('text');
        font-size: 0.82em;
        letter-spacing: 0.1em;
        padding: 5px 12px 5px 12px;
        border-radius: 12px;

        &:hover {
        	opacity: 1;
        }
      }
	}

     .card {
        @include themed() {
        padding: 5px;
        margin: 5px;
        border-radius: 11px;
        background-color: rgba(t('sidebar'), .8);
		overflow: hidden;

            .unavailable {
                z-index: 4;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                @include blur(t('sidebar'), 0.7, 0.85, 10px);
                border-radius: 5px;
                .slanting {
					cursor: pointer;
                    transform: skewY(-5deg) translateY(-50%);
                    padding: 25px;
                    position: absolute;
                    top: 45%;
                    background: rgba(t('text'), 0.05);
                    width: 100%;
                    .unavailableContent {
                        font-size: 15px;
                        transform: skewY(5deg);
                        text-align: center;
                    }
                    .won {
                        font-size: 22px;
                        font-weight: 600;
                        transform: skewY(5deg);
                        text-align: center;
                    }
                }
            }


        		.border-top {
        			border-top: 1px solid darken(t('text'), 2.5%) !important;
        		}

        		.battle-desc {
                	color: t('text');
                    font-weight: 500;
					text-align: center;
           		}

                .cardHeader {
                	color: t('text');
                    text-transform: uppercase;
                    font-weight: 500;
                    font-size: 0.95em;
                    letter-spacing: 0.05em;
                }

                .text-muted {
                	color: darken(t('text'), 2.5%);
                }
    	}
     }

     .card-gameThumbnail {
     	display: block;
        min-width: 100px;
        max-width: 220px;
     	border-radius: 11px;
     	object-fit: cover;
  		margin-left: auto;
  		margin-right: auto;
  		width: 60%;
  		cursor: pointer;
     }

    .gameCategory {
        @include themed() {
            .header {
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                border-bottom: 2px solid t('border');
                margin-top: -15px;
                padding: 35px 45px;
                font-size: 1.8em;
                position: sticky;
                top: 73px;
                z-index: 555;
            }
        }
    }
	
	.game_poster_external-provider {
		bottom: 15px !important;
		text-transform: capitalize;
	}
</style>
