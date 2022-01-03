<template>
		<div class="container-fluid" style="max-width: 1200px !important;">

				<template v-if="!Errorgame">
				    <div class="page_not_found container-fluid">
				        <div class="title">{{ $t('error.general_error_title') }}</div>
				        <div class="subtitle">{{ $t('error.game_error_subtitle') }}</div>
				    </div>
				</template>

    			<template v-if="!Loading && externalgame">
				<div :key="componentKey" v-if="!LoadingMode">
					<div :class="`game-container game-${$route.params.id}`">
						<div id="slotcontainer">
							<div class="gameWrapper frame-loader">
								<template v-if="PreviewBlock">
								<div :style="`background: url('https://games.cdn4.dk/games${externalgame.image}?auto=format&fit=crop&sharp=30&q=70') center center / 100% no-repeat`" class="preview-block"></div>
								<div class="mask">
									<div class="tips">
										<div class="wrap">{{ $t('general.games.notice') }}</div>
										<div class="btn-wrap">
											<div class="d-grid gap-2 d-md-block">
											  <button @click="hidePreview(true)" class="btn btn-primary" type="button"><i class="far fa-play-circle" aria-hidden="true"></i> Real Play</button>
											  <button @click="hidePreview(false)" class="btn btn-primary" type="button"><i class="far fa-play-circle" aria-hidden="true"></i> Free Play</button>
											</div>
										</div>
									</div>
								</div>
								</template>
							
								<template v-if="!PreviewBlock">
								<fullscreen :fullscreen.sync="fullscreen">
									<iframe :src="externalgame.url" sandbox = "allow-forms allow-popups allow-pointer-lock allow-same-origin allow-scripts" allowfullscreen="allowfullscreen"></iframe>
								</fullscreen>
								</template>
							</div>
						</div>
						<div class="gameFooter">
							<div class="right">
								<div>
									<div class="items-info">
										<b>{{ externalgame.name }}</b>
										<small style="margin-left:3px;">by <u style="text-transform: capitalize;cursor: pointer;" @click="$router.push(`/game/provider/${externalgame.provider}`)">{{ externalgame.provider }}</u></small>
									</div>
									<div v-if="!PreviewBlock" class="items-info">
										<toggle-button :disabled="isGuest ? true : false" class="toggleDemoMode" :value="RealMode" @change="toggleDemoMode((RealMode == true ? false : true), true)" :labels="{checked: 'real', unchecked: 'demo'}" :width="60" />
				 					</div>
								</div>
								<div class="settings-panel"> 
									<button v-if="!isGuest" @click="toggleFavoriteGame($route.params.id)" class="btn btn-primary btn-settings-fav">
										<template v-if="!favMarkLoading">
										<i aria-hidden="true" :class="`fa${!user.user.favoriteGames || !user.user.favoriteGames.includes($route.params.id) ? 'l' : 's'} fa-star`"></i> {{ $t(!user.user.favoriteGames || !user.user.favoriteGames.includes($route.params.id) ? 'general.sidebar.mark_as_favorite' : 'general.sidebar.remove_from_favorite') }}
										</template>
										<loader v-else></loader>
									</button>
									<button @click="showFull" title="Play Full Screen" class="btn btn-primary btn-settings-full">
										<i aria-hidden="true" class="fas fa-expand"></i>
									</button>
								</div>
							</div>
						</div>
					</div> 
					<div class="game-container-slick">
						<div class="games-arrows" id="c1-arrows">
						  <div class="games-arrow" @click="showPrev"><i class="fas fa-arrow-circle-left"></i></div>
						  <div class="games-arrow" @click="showNext"><i class="fas fa-arrow-circle-right"></i></div>
						</div>
						<VueSlickCarousel ref="casinoCarousel" v-bind="carouselSettings">
							<div v-for="footerGame in footerGames">
								<div :class="`game_poster_${footerGame.type} game-${footerGame.id} game_type-${footerGame.type}`" :style="`cursor: pointer;`">
									<div :class="`game_poster_${footerGame.type}-image game_tp-image`" v-if="footerGame.ext" :style="`background: url('${cdnBase}${footerGame.icon}${cdnParameters}') no-repeat !important; background-size: cover !important;`"  @click="footerGame.ext ? $router.push(`/casino/${footerGame.id}`) : $router.push(`/game/${footerGame.id}`)">
										<div :class="`game_poster_${footerGame.type}-name`" v-if="!footerGame.ext"  @click="footerGame.ext ? $router.push(`/casino/${footerGame.id}`) : $router.push(`/game/${footerGame.id}`)">
											{{ footerGame.name }}
										</div>
									</div>
								</div>
							</div>
						</VueSlickCarousel>
					</div>
				</div>
			</template>
			<div v-else class="games-load">
				<loader></loader>
			</div>
	</div>

</template>

<script>
    import { mapGetters } from 'vuex';
	import VueSlickCarousel from 'vue-slick-carousel';
	import AuthModal from '../modals/AuthModal';
    export default {
        data() {
            return {
            	RealMode: null,
            	roomid: null,
				Loading: true,
				Errorgame: true,
				LoadingMode: true,
				PreviewBlock: true,
				componentKey: 0,
                carouselSettings: {
                  "dots": false,
                  "arrows": false,
                  "infinite": false,
                  "speed": 500,
                  "slidesToShow": 5,
                  "slidesToScroll": 2,
                  "cssEase": 'cubic-bezier(0.175, 0.885, 0.320, 1.275)',
                  "responsive": [
                    {
                      "breakpoint": 950,
                      "settings": {
                        "slidesToShow": 4,
                        "slidesToScroll": 2
                      }
                    },
                    {
                      "breakpoint": 800,
                      "settings": {
                        "slidesToShow": 3,
                        "slidesToScroll": 1
                      }
                    },
                    {
                      "breakpoint": 480,
                      "settings": {
                        "slidesToShow": 2,
                        "slidesToScroll": 1
                      }
                    }
                  ]
                },
                footerGames: [],
                externalgame: null,
				fullscreen: false,
				processing: false,
				favMarkLoading: false
            }
        },
        methods: {
            showNext() {
                this.$refs.casinoCarousel.next()
            },
            showPrev() {
                this.$refs.casinoCarousel.prev()
            },
            toggleDemoMode(mode, state) {
				if (this.processing === true && state === true) {
					this.$toast.error(this.$i18n.t('general.error.throttle'));
					return;
				} 
				this.processing = true;
				
	            axios.post('/api/externalGame/getUrl', { id: this.$route.params.id, currency: this.currency, roomid: this.$route.params.roomid, mode: mode}).then(({ data }) => {
					this.LoadingMode = true; 
	                this.externalgame = data;
	            	this.RealMode = data.mode;
					this.forceRerender();
	           });
			   
				setTimeout(() => {
					this.processing = false;
				}, 600);
			   
            },
			hidePreview(mode) {
				if(mode === true) {
					if(this.isGuest) {
						AuthModal.methods.open('auth');
						this.$toast.error(this.$i18n.t('general.error.auth'));
						return;
					}
					this.PreviewBlock = false;
					return;
				}
				this.toggleDemoMode(mode, true);
				this.PreviewBlock = false;
            },
			forceRerender() {
				  this.componentKey += 1;  
				  this.LoadingMode = false;
			},
			showFull() {
				this.fullscreen = !this.fullscreen;
			},
			toggleFavoriteGame(id) {
                if(this.favMarkLoading) return;
                this.favMarkLoading = true;
                axios.post('/api/user/markGameAsFavorite', { id: id }).then(() => {
                    this.$store.dispatch('update');
					setTimeout(() => {
						this.favMarkLoading = false;
					}, 600);
                }).catch(() => this.favMarkLoading = false);
            }
        },
        computed: {
            ...mapGetters(['cdnBase', 'cdnParameters', 'isGuest', 'user', 'currency', 'gameInstance'])
        },
		watch: {
			currency() {
				this.toggleDemoMode(this.RealMode, false);
            },
		},
        components: { VueSlickCarousel },
        created() {
			this.RealMode = true;
            axios.post('/api/externalGame/getUrl', { id: this.$route.params.id, currency: this.currency, roomid: this.$route.params.roomid, mode: this.RealMode }).then(({ data }) => {
                this.externalgame = data;
				this.$store.dispatch('pushRecentGame', data.id);
				this.RealMode = data.mode;
				this.Loading = false;
				this.LoadingMode = false;
           }).catch((error) => {
                        switch (error.response.data.code) {
                     	case 1:
                     	this.Errorgame = false;
                        this.$toast.error(this.$i18n.t('error.game_load_error'));
                        break;
                }
                });
            axios.post('/api/externalGame/getGamesbyProvider', { id: this.$route.params.id }).then(({ data }) => {
                this.footerGames = data;
            });
        }

    }
</script>

<style lang="scss">
	@import "resources/sass/variables";
	
	.preview-block {
		padding-top: 50%;
		overflow: hidden;
		content: "";
		inset: 5px;
		filter: blur(18px);
	}

	.mask {
		inset: 0px;
		background-color: rgba(0, 0, 0, 0.5);
		position: absolute;
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		-webkit-box-pack: center;
		justify-content: center;
		
		.tips {
			width: 70%;
			text-align: center;
			
				.wrap {
					max-width: 34.375rem;
					margin: 0px auto;
					color: rgba(255, 255, 255, 0.6);
				}

				.btn-wrap {
					display: flex;
					margin: 20px auto 0px;
					-webkit-box-pack: center;
					justify-content: center;
				}
			
		}
		
	}
		
		
	.games-load {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 30px;
	}

	.game-type-third-party {
		display: flex;
		flex-direction: column;
		position: relative;
		min-height: 550px
	}

	.games-arrow {
		opacity: 0.7;
		cursor: pointer;
		margin-left: 4px;
		transition: opacity 0.3s ease;

		&:hover {
			opacity: 1;
		}
	}
	.games-arrows {
			display: flex;
	}

	.game-container-slick {
		margin-top: 20px;
		margin-bottom: 50px;
	}

	.game-type-third-party iframe {
		 position: absolute;
		 top: 0;
		 border-top-left-radius: 8px;
		 border-top-right-radius: 8px;
		 left: 0;
		 width: 100%;
		 height: 100%;
	}

	#slotcontainer {
		outline: none !important;
		border-bottom-right-radius: 0px;
		border-bottom-left-radius: 0px;
		margin-top: 25px;
		max-width: 1500px;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		background: #00000091;
	}
	.gameWrapper {
		overflow: hidden;
		position: relative;
		padding-bottom: 56.25%;
		height: 0;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
	}

	.gameWrapper iframe {
		position: absolute;
		top: 0;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.gameFooter {
		padding: 15px 5px 15px 25px; 
		border-bottom-left-radius: 7px;
		border-bottom-right-radius: 7px;
		@include themed() {
			background: darken(t('body'), 1.5%);
		} 
	}

	.v-switch-core {
			@include themed() {
				background-color: t('secondary') !important;
			}
	}

	@include media-breakpoint-down(sm) {
	 
		 .game-container-slick {
			display: none;
		}
			
		 .gameWrapper {
			 min-height: 250px;
			 overflow: hidden;
			 position: relative;
			 padding-bottom: 1px !important;
			 height: 100%;
			 border-top-left-radius: 7px;
			 border-top-right-radius: 7px;
			 border: none !important;
		}
		
		 .gameWrapper iframe {
			 position: relative;
			 top: 0;
			 border-top-left-radius: 7px;
			 border-top-right-radius: 7px;
			 left: 0;
			 width: 100%;
			 height: 100%;
			 min-height: 80vh;
		}
		
		 #slotcontainer {
			 background: #00000091;
			 margin-top: 5px;
			 max-width: 1500px;
			 border-top-left-radius: 0px;
			 border-top-right-radius: 0px;
		}
	}
		
	@media (min-width: 992px) {
		.container-lg, .container-md, .container-sm, .container {
			max-width: 960px !important;
		}
	}
	
	@media only screen 
	  and (min-width: 1024px) 
	  and (max-height: 1366px) 
	  and (-webkit-min-device-pixel-ratio: 1.5) {
		.container-lg, .container-md, .container-sm, .container {
			max-width: 660px !important;
		}
	}
	
	.settings-panel {
		margin-right: 10px; 
		margin-left: auto;
	
		.btn-settings-fav {
			height: auto;
			margin-right: 10px;
			margin-left: auto;
			
			.loaderContainer {
				transform: scale(.7) translate(-5%);
				margin-top: -15px;
			}
			
		}
		
		.btn-settings-full {
			height: auto;
			width: auto;
			margin-left: auto;
		}
		
	}
	
	@media (max-width: 767.98px) { 
	
		.settings-panel {
			transform: scale(0.8);
			display: flex;
		}
		
	}

</style>