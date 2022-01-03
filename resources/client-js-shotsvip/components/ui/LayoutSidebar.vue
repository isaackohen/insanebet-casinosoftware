 <template>
    <div :class="'sidebar ' + (sidebar ? 'visible' : 'hidden')">
        <div class="fixed">
		    <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" class="games">
                <div class="main-header" style="border-top: unset !important;">
                    {{ $t('general.head.person') }} 
                    <icon :icon="sidebar ? 'fal fa-times' : 'fal fa-bars'" class="sidebar-switch" @click.native="$store.dispatch('toggleSidebar')"></icon>
                </div>
                <div v-if="isGuest" @click="openAuthModal('auth')" class="game">
                    <icon icon="fas fa-sign-in"></icon>
                    <div class="letter-spacing">{{ $t('general.auth.login') }}</div>
                </div>

                <div v-if="isGuest" @click="openAuthModal('auth')" class="game">
                    <icon icon="fas fa-user-plus"></icon>
                    <div class="letter-spacing">{{ $t('general.auth.register') }}</div>
                </div>

				<router-link v-if="!isGuest" tag="div" to="/game/category/favorite" class="game">
                    <icon icon="fas fa-star"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.favorite') }}</div>
                </router-link>

                <router-link v-if="!isGuest" tag="div" to="/game/category/recent" class="game">
                    <icon icon="fas fa-history"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.recent') }}</div>
                </router-link>
    
                <router-link tag="div" to="/challenges" class="game">
                    <icon icon="fas fa-bullseye-arrow"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.challenges') }}</div>
                </router-link>
				
				<template v-if="currencies.bonusBattle === 'true'">
					<router-link tag="div" to="/bonusbattle" class="game">
						<icon icon="fas fa-swords"></icon>
						<div class="letter-spacing">{{ $t('general.sidebar.bonusbattle') }}</div>
					</router-link>
				</template>

                <div @click="openVipModal()" class="game">
                    <icon icon="fas fa-crown"></icon>
                    <div class="letter-spacing">VIP Club</div>
                </div>
                <div @click="openRakebackModal('rakeback')" class="game">
                    <icon icon="fas fa-piggy-bank"></icon>
                    <div class="letter-spacing">Rakeback</div>
                </div>

                <div @click="openFaucetModal()" class="game">
                    <icon icon="fas fa-stroopwafel"></icon>
                    <div class="letter-spacing">{{ $t('general.head.dailyspin') }}</div>
                </div>
                <div @click="openPromocodeModal()" class="game">
                    <icon icon="fas fa-barcode"></icon>
                    <div class="letter-spacing">{{ $t('bonus.promo.title') }}</div>
                </div>

                <router-link v-if="!isGuest && user.user.freespins > 0" tag="div" :to="`/game/subcategory/freespins`" class="game">
                    <icon icon="fal fa-user"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.freespins') }}   <span class="badge badge-info">{{ user.user.freespins }}</span>
                    </div>
                </router-link>

                <div onclick="window.open(window.location.origin + '/admin')" v-if="!isGuest && user.user.access === 'admin'" class="game">
                    <i class="fas fa-cog"></i>
                    <div class="letter-spacing">{{ $t('general.sidebar.admin') }}</div>
                </div>
				
                <div class="divider"></div>

				<div class="main-header">
					{{ $t('general.head.games') }}
				</div>
	
                <router-link tag="div" to="/" class="game">
                    <icon icon="fas fa-th-large"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.all') }}</div>
                </router-link>

                <router-link tag="div" to="/game/namecategory/andar" class="game">
                    <icon icon="fa fa-chess-queen"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.andar') }}</div>
                </router-link>

                <router-link tag="div" to="/game/namecategory/teen" class="game">
                    <icon icon="fa fa-scarf"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.teen') }}</div>
                </router-link>

                <router-link tag="div" to="/game/namecategory/blackjack" class="game">
                    <icon icon="fak fa-poker-cards"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.blackjack') }}</div>
                </router-link>

                <router-link tag="div" to="/game/namecategory/roulette" class="game">
                    <icon icon="fa fa-dharmachakra"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.roulette') }}</div>
                </router-link>

                <router-link tag="div" to="/game/category/slots" class="game">
                    <icon icon="fak fa-cherry"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.slots') }}</div>
                </router-link>
	
                <router-link tag="div" to="/game/category/live" class="game">
                    <icon icon="fad fa-star-shooting"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.live') }}</div>
                </router-link>

                <router-link tag="div" to="/providers" class="game">
                    <icon icon="fas fa-gamepad"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.providers') }}</div>
                </router-link>


                <div class="divider"></div>

                <div class="recentTpGames">
                    <loader v-if="!lastGames"></loader>
                    <template v-else>
                        <div class="recentTpGame" v-for="game in lastGames" :key="game.game._id">
                            <div class="info">
                                <router-link tag="div" :to="`/game/${game.metadata.id}`" class="image">
                                    <icon :icon="game.metadata.icon"></icon>
                                </router-link>
                                <div class="meta">
                                    <router-link :to="`/profile/${game.game.user}`" class="player">{{ game.user.name }}</router-link>
                                    <div class="currency"><icon :icon="currencies[game.game.currency].icon" :style="{ color: currencies[game.game.currency].style }"></icon> <span style="margin-right:2px;" v-if="usd">$</span><unit :to="game.game.currency" :value="game.game.profit">$</unit></div>
                                    <router-link :to="`${game.game.type === 'external' ? (`/casino/` + game.metadata.id) : (`/game/` + game.metadata.id)}`" class="gameName">{{ game.metadata.name }}</router-link>
                                </div>
                            </div>
                            <router-link tag="div" :to="`${game.game.type === 'external' ? (`/casino/` + game.metadata.id) : (`/game/` + game.metadata.id)}`" class="btn btn-primary">{{ $t('general.play_now') }}</router-link>
                        </div>
                    </template>
                </div>
            </overlay-scrollbars>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import RakebackModal from "../modals/RakebackModal";
    import Bus from "../../bus";
    import FaucetModal from "../modals/FaucetModal";
    import VipModal from '../modals/VipModal';
    import PromocodeModal from "../modals/PromoCode";

    export default {
        computed: {
            ...mapGetters(['isGuest', 'user', 'theme', 'games', 'currencies', 'sidebar', 'usd'])
        },
        data() {
            return {
                lastGames: null,
                maxEntries: 3
            }
        },
        created() {
            this.getGames();
            Bus.$on('event:liveGame', (e) => setTimeout(() => this.lastGames.unshift(e), e.delay));

            Bus.$on('event:liveGame', (e) => setTimeout(() => this.lastGames.unshift(e), e.delay));
        },
        watch: {
            lastGames() {
                if(this.lastGames && this.lastGames.length >= this.maxEntries) this.lastGames.pop();
            }
        },
        methods: {
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            openRakebackModal(type) {
                if(this.isGuest) return this.openAuthModal('auth');
                RakebackModal.methods.open(type);
            },
            openVipModal() {
                if(this.isGuest) return this.openAuthModal('auth');
                VipModal.methods.open();
            },
            openFaucetModal() {
                if(this.isGuest) return this.openAuthModal('auth');
                FaucetModal.methods.open();
            },
            openPromocodeModal() {
                if(this.isGuest) return this.openAuthModal('auth');
                PromocodeModal.methods.open();
            },
            getBonus() {
                this.lastGames = null;
                axios.post('/api/data/latestGames', {
                    type: 'all',
                    count: this.maxEntries
                }).then(({ data }) => this.lastGames = data.reverse());
            },        
            getGames() {
                this.lastGames = null;
                axios.post('/api/data/latestGames', {
                    type: 'all',
                    count: this.maxEntries
                }).then(({ data }) => this.lastGames = data.reverse());
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .badge-info {
        @include themed() {
            color: rgba(t('secondary'), .99) !important;
        }
    }


    .sidebar.visible {
        width: $sidebar-width-expanded;

        .recentTpGames {
            display: flex !important;
        }

        .fixed {
            width: $sidebar-width-expanded;
			
			.main-header {
				padding: 15px;
				font-size: 12px;
				@include themed() {
					color: rgba(t('text'), .8);
				}
				font-weight: 600;
				white-space: nowrap;
				text-transform: uppercase;
				display: block !important;
				@include themed() {
					border-bottom: 2px solid t('border');
					border-top: 2px solid t('border');
				}
			}

            .game.router-link-exact-active {
                @include themed() {
                    background: t('body');
                                    i {
                    color: t('secondary');
                }
                color: t('link-active');
                }

                &:before {

                }
            }

            .game {
                justify-content: unset;
                padding-left: 7px;
                padding-right: 17px;
                position: relative;

                i {
                @include themed() {
                background: t('body');
                }
                border-radius: 5px;
                padding: 6px;
                margin-right: 10px;
                background: black;
                font-size: 12px;
                }

                svg {
                    margin-right: 11px;
                }

                div {
                    display: block;
                    opacity: 1;
                }
            }
        }
    }

    .sidebar.visible ~.pageWrapper {
        padding-left: $sidebar-width-expanded;
    }

    .letter-spacing {
        letter-spacing: 0.20px;
    }

    .sidebar {
        width: $sidebar-width;
        flex-shrink: 0;
        z-index: 38002;
        transition: width 0.3s ease;

        .fixed {
            position: fixed;
            top: 0;
            width: $sidebar-width;
            height: 100%;
            border-radius: 3px;
            padding: 7px 0;
			
			.main-header {
				display: none;
			}

            @include themed() {
                border-right: 2px solid t('border');
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                transition: background 0.15s ease, width .3s ease;

                .games {
                    height: calc(100% - 35px);
                    //height: 100%;
                    border-radius: 3px;

                    .divider {
                        margin-top: 10px !important;
                        margin-bottom: 10px !important;
                    }

                    .recentTpGames {
                        display: none;
                        width: 100%;
                        flex-direction: column;
                        margin-top: 10px;
                        border-top: 2px solid t('border');
                        padding-top: 15px;

                        .loaderContainer {
                            width: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transform: scale(0.6);
                        }

                        .btn {
                            text-transform: uppercase;
                            margin-top: 10px;
                        }

                        .recentTpGame {
                            display: flex;
                            flex-direction: column;

                            .info {
                                display: flex;
                                padding-left: 15px;
                                padding-right: 15px;

                                .image {
                                    width: 15px;
                                    height: 15px;
                                    background-position: center;
                                    background-size: cover;
                                    margin-right: 12px;
                                    margin-top: 10px;
                                    border-radius: 3px;
                                    cursor: pointer;
                                    display: flex;
                                    opacity: .95;

                                    svg, i {
                                        margin: auto;
                                        font-size: 1em;
                                        color: t('text');
                                        opacity: .95;
                                    }
                                }

                                .meta {
                                    width: calc(100% - 50px);
                                    font-size: 0.75em;
                                    font-weight: 500;

                                    .gameName {
                                        text-transform: uppercase;
                                    }
                                }
                            }
                        }
                    }

                    .btn {
                        width: calc(100% - 30px);
                        margin-left: 15px;
                        margin-right: 15px;
                        margin-bottom: 15px;
                        border-radius: 20px;
                        font-size: 0.8em;
                        &.btn-primary {
                            border-bottom: 3px solid darken(t('secondary'), 15%);
                        }

                        &.btn-secondary {
                            border-bottom: 3px solid darken($gray-600, 15%);
                        }
                    }
                }
            }

            .game {
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 1;
                transition: all 0.3s ease;
                @include themed() {
                color: t('link');
                }
                height: 40px;
                font-size: 12px;
                cursor: pointer;
                position: relative;

                &:before {
                    transition: background .3s ease;
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                }

                div {
                    display: none;
                    opacity: 0;
                    transition: opacity 1s ease;
                }

                .vue-content-placeholders-img {
                    height: 15px;
                    width: 15px;
                    border-radius: 3px;
                }

                img {
                    width: 12px;
                    height: 12px;
                }

                i {
                    cursor: pointer;
                }

                &:hover {
                @include themed() {
                    i {
                        color: t('secondary');
                    }
                        background: t('body');
                        color: t('text');
                    }
                }

                .online {
                    position: absolute !important;
                    top: 4px !important;
                    left: 17px !important;
                    border-radius: 50%;
                    width: 15px;
                    @include themed() {
                        background: t('secondary');
                    }
                    color: white;
                    height: 15px;
                    font-size: 0.5em;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }
            }

            .game.router-link-exact-active {
				@include themed() {
					box-shadow: inset 3px 0 0 0 t('secondary');
				}
                opacity: 1;
            }
        }
    }

    @include media-breakpoint-down(md) {
        .sidebar {
            display: none;
        }
    }
</style>
