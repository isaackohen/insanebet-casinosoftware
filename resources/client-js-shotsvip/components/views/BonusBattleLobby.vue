<template>
	<div class="bonus-battle-lobby">
		<div class="card-group">
		   <div class="card">
				<div class="card-body"> 
					<div :class="`badge bg-primaryBadge float-end`">{{ lobbyData.stake }}$ <icon :icon="currencies[lobbyData.currency].icon" :style="{ color: currencies[lobbyData.currency].style }"></icon></div>
					<h4><a href="javascript:void(0)" class="cardHeader">{{ lobbyData.g_name }}</a></h4>
					<div class="text-muted mb-4">
					 <img :class="`card-gameThumbnail`" :src="`https://games.cdn4.dk/games6${lobbyData.g_img}?q=99&auto=enhance&w=180&h=180`">
						<hr>
						<div class="battle-desc">Battle Info  - ID{{ lobbyData.room_id }}</div>
						<div class="battle-info">
							<div class="desc">Bonus Buy</div>
							<div class="info">{{ lobbyData.stake }}$ <icon :icon="currencies[lobbyData.currency].icon" :style="{ color: currencies[lobbyData.currency].style }"></icon></div>
						</div>
						<div class="battle-info">
							<div class="desc">Size</div>
							<div class="info">{{ lobbyData.players_active }} / {{ lobbyData.players_max }} players</div>
						</div>
						<div class="battle-info">
							<div class="desc">Creator</div>
							<div class="info creator">{{ lobbyData.player_name_1 }}</div>
						</div>
						<div class="battle-info">
							<div class="desc">Currency</div>
							<div class="info">{{ currencies[lobbyData.currency].name }}</div>
						</div>
					</div> 
				</div>
		   </div>
		   <div class="card">
			  <div class="card-body">
				 <div class="badge bg-primaryBadge float-end">
				 <template v-if="timerCount['status'] === true">
					{{ timerCount['minutes'] + ':' + timerCount['seconds'] }}
				 </template>
				 <template v-else-if="timerCount['status'] === false">
					Verification
				 </template>
				<template v-else>
					Сlosed
				</template>
				 </div>
				 <div class="text-muted mb-4">
					<div class="badge bg-primaryBadge">Players {{ lobbyData.players_active }}</div>
					<br><br>   
					<table class="table table-borderless battle-lobby">
					   <thead>
						  <tr>
							 <th>#</th>
							 <th>PLAYER</th>
							 <th>STATUS</th>
							 <th>BALANCE</th>
							 <th>PRIZE WON</th>
						  </tr>
					   </thead>
					   <tbody class="battle-table-info">
						  <tr v-for="(player, index) in sortedLobby">
							<th>{{ (lobbyData.room_state === 'joinable' || lobbyData.room_state === 'active') ? '-' : (index + 1) }}</th>
							<td><router-link :to="`/profile/${player.id}`">{{ player.name }}</router-link></td>							
							<td class="b-info">{{ (player.status !== undefined) ? player.status : '-' }}</td>
							<td>{{ (lobbyData.room_state === 'joinable' || lobbyData.room_state === 'active' || lobbyData.room_state === 'cancelled' || lobbyData.room_state === 'started') ? '-' : ((player.balance !== undefined) ? (parseFloat(player.balance).toFixed(2) + '$') : '-') }}</td>
							<td>{{ (lobbyData.room_state === 'joinable' || lobbyData.room_state === 'active' || lobbyData.room_state === 'cancelled' || lobbyData.room_state === 'started') ? '-' : (lobbyData.winner_id === player.id ? (parseFloat(lobbyData.winner_amount).toFixed(2) + '$') : '0.00$') }}</td>
						  </tr>
					   </tbody>
					</table>
				 </div>
			  </div>
			  <div class="card-body">
				 <div class="d-grid gap-2 col-6 mx-auto">
					<button v-if="lobbyData.room_state === 'joinable' && user && onside !== true" :key="lobbyData.room_id" @click="joinBattle(lobbyData.room_id)" v-bind:value="lobbyData.room_id" class="btn btn-primary">
						Join - {{ lobbyData.stake }}$
					</button>
					<button v-if="lobbyData.room_state === 'joinable' && !user" :key="lobbyData.room_id" @click="loginToPlay()" v-bind:value="lobbyData.room_id" class="btn btn-primary">
						Login and Join
					</button>
					<button v-if="lobbyData.room_state !== 'completed' && lobbyData.room_state !== 'cancelled' && lobbyData.room_state !== 'joinable' && onside === true" class="btn btn-primary" @click="$router.push(`/bonusbattle/${lobbyData.room_id}/${lobbyData.game}`)">
						Play - {{ lobbyData.stake }}$
					</button>
					<button v-if="user && (lobbyData.winner_id === user.user._id && lobbyData.claimed === false)" class="btn btn-primary" @click="claimBattle(lobbyData.room_id)">
						Claim Winnings {{ parseFloat(lobbyData.winner_amount).toFixed(2) }}$
					</button>
					<button v-if="user && (lobbyData.winner_id === user.user._id && lobbyData.claimed === true)" class="btn btn-secondary disabled" disabled>
						Claimed {{ parseFloat(lobbyData.winner_amount).toFixed(2) }}$
					</button>
				</div>
			  </div>
			  <div class="card-footer">
				 <div class="d-grid gap-2 col-6 mx-auto">
					<button class="btn btn-secondary disabled" v-if="lobbyData.room_state === 'joinable'" @click="waitingFill()">
						Waiting for players..
					</button>
					<button class="btn btn-secondary disabled" v-if="lobbyData.room_state === 'active' || lobbyData.room_state === 'started'">
						Live...
					</button>
					<button class="btn btn-secondary disabled" v-if="lobbyData.room_state === 'completed'" @click="waitingFill()">
						Finished
					</button>
					<button class="btn btn-secondary disabled" v-if="lobbyData.room_state === 'cancelled'" @click="waitingFill()">
						Cancelled
					</button>
				 </div>
				</div>
		   </div>
		</div>
	</div>
</template>

<script type="text/javascript">
    import { mapGetters } from 'vuex';
	import Bus from "../../bus";
	import AuthModal from '../modals/AuthModal';
    export default {
        props: ['lobby'],
        data() {
            return {
				lobbyData: this.lobby,
				onside: false,
				timerCount: {
					minutes: 0,
					seconds: 0,
					status: true
				}
            }
        },
		mounted() {
			let countDownDate = new Date(this.lobbyData.expires).getTime();
            let timer = () => {
                let now = new Date().getTime();
                let distance = countDownDate - now;
                const pad = (n) => String("0" + n).slice(-2);
                this.timerCount['minutes'] = pad(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)));
                this.timerCount['seconds'] = pad(Math.floor((distance % (1000 * 60)) / 1000));
                if (distance < 0) {
                    clearInterval(interval);
					this.timerCount['minutes'] = '00';
					this.timerCount['seconds'] = '00';
					this.timerCount['status'] = false;
                }
				if((this.lobbyData.room_state === 'cancelled') || (this.lobbyData.room_state === 'completed')) {
					this.timerCount['status'] = null;
				}
            };
            let interval = setInterval(timer, 1000);
            timer();
            Bus.$on('event:BonusBattleUpdate', (e) => {
				if(e.bonusbattle.room_id === this.lobbyData.room_id) {
					this.lobbyData = e.bonusbattle;
					if((this.lobbyData.room_state === 'cancelled') || (this.lobbyData.room_state === 'completed')) {
						this.timerCount['status'] = null;
					} else {
						console.log('New timer setup');
						this.timerCount['status'] = true;
						clearInterval(interval);
						countDownDate = new Date(this.lobbyData.expires).getTime();
						interval = setInterval(timer, 1000);
						timer();
					}
				}
            });
		},
        computed: {
            ...mapGetters(['currencies', 'user']),
			sortedLobby() {
				let array = [];
				let players = this.lobbyData.players;
				_.forEach(players, (player, key) => {
					if(this.user){
						if(player === this.user.user._id) this.onside = true;
					}
					let name = 'player_name_' + (key + 1);
					let balance = 'player_balance_' + (key + 1);
					let status = 'player_final_' + (key + 1);
					array.push(
						{
							'id': player, 
							'name': this.lobbyData[name],
							'balance': this.lobbyData[balance],
							'status': this.lobbyData[status]
						}
					);
				});
			    return array.sort(function(a, b) {
					return b.balance - a.balance;
				});
			}
        },
		methods: {
            joinBattle(id) {
				axios.post('/api/bonusbuybattle/joinBattle', { battleroom: id }).then(({ data }) =>  Bus.$emit('modal:close')).catch((error) => {
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
				axios.post('/api/bonusbuybattle/claimBattle', { battleroom: id }).then(({ data }) =>  {
					this.lobbyData.claimed = true;
				}).catch((error) => {
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
            OpenBonusBattleModal() {
                create_bonus_battle.methods.open();
            },
			loginToPlay() {
				AuthModal.methods.open('auth');
			}
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";

	tbody.battle-table-info {
		font-weight: 600;
		
		td.b-info {
			text-transform: capitalize;
		}
		
	}
	
	table.table.battle-lobby {
		@include themed() {
			color: t('text');
		}
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

	.tabs-available {
		margin-right: 20px;
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

	.tabs-active {
		margin-right: 20px;
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

	.tabs-completed {
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
	
	.tabs-available.active, .tabs-active.active, .tabs-completed.active {
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
