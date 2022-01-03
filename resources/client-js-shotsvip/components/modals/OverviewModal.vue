<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['isGuest', 'currencies'])
        },
        methods: {
            open(game_id, api_id) {
                Bus.$emit('modal:new', {
                    name: 'overview',
                    component: {
                        computed: {
                            ...mapGetters(['isGuest', 'channel', 'usd'])
                        },
                        data() {
                            return {
                                response: null
                            }
                        },
                        created() {
                            axios.post('/api/game/info/' + game_id).then((data) => this.response = data.data);
                        },
                        methods: {
                            close() {
                                Bus.$emit('modal:close');
                            },
                            share(type) {
                                if(!this.response) return;

                                const share_url = `${window.location.origin}?game=${this.response.info.game}-${this.response.info._id}`;
                                switch (type) {
                                   case "vk":
                                       window.open(`https://vk.com/share.php?url=${share_url}&title=${this.$i18n.t('general.share_text')}`, '_blank');
                                       break;
                                   case "chat":
                                       if(this.isGuest) return;
                                       Bus.$emit('modal:close');
                                       axios.post('/api/chat/link_game', { id: game_id, channel: this.channel });
                                       break;
                                   case "twitter":
                                       window.open(`https://twitter.com/intent/tweet?hashtags=playin.team&text=${this.$i18n.t('general.share_text')}&url=${share_url}`, '_blank');
                                       break;
                                   case "telegram":
                                       window.open(`https://telegram.me/share/url?url=${share_url}&text=${this.$i18n.t('general.share_text')}`, '_blank');
                                       break;
                               }
                            }
                        },
                        template: `
                            <div>
                                <div class="overview-share-options" v-if="response">
                                    <a @click="share('chat')" v-if="!isGuest" href="javascript:void(0)">
                                        <i class="fas fa-comments"></i>
                                    </a>
                                    <a @click="share('vk')" href="javascript:void(0)">
                                        <i class="fab fa-vk"></i>
                                    </a>
                                    <a @click="share('twitter')" href="javascript:void(0)">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a @click="share('telegram')" href="javascript:void(0)">
                                        <i class="fab fa-telegram"></i>
                                    </a>
                                </div>

                                <loader v-if="!response"></loader>

                                <div class="heading-overview" v-if="response">
                                    <strong>{{ response.metadata.name }}</strong> #{{ response.info.id }}
                                </div>

                                <div class="overview-player" v-if="response">
                                    <div>{{ $t('general.bets.player') }}:
                                    <router-link :to="'/profile/' + response.info.user" @click="close" v-if="!response.user.private_bets">
                                       {{ response.user.name }}
                                    </router-link>
                                    <a href="javascript:void(0)" v-else>{{ $t('general.bets.hidden_name') }}</a>
                                </div>
                                </div>
                                <div class="overview-bet" v-if="response">
                                    <div class="option">{{ $t('general.bets.bet') }}: <span><icon :icon="currencies[response.info.currency].icon" :style="{ color: currencies[response.info.currency].style }"></icon> <span v-if="usd">$</span> {{ this.usd ? (response.info.wager * this.currencies[response.info.currency].price).toFixed(2) : rawBitcoin(response.info.currency, response.info.wager) }}</span></div>
                                    <div class="option">{{ $t('general.bets.mul') }}: <span>{{ response.info.status === 'lose' ? '0.00' : response.info.multiplier.toFixed(2) }}x</span></div>
                                    <div class="option">{{ $t('general.bets.win') }}: <span><icon :icon="currencies[response.info.currency].icon" :style="{ color: currencies[response.info.currency].style }"></icon> <span v-if="usd">$</span> {{ this.usd ? (response.info.profit * this.currencies[response.info.currency].price).toFixed(2) : rawBitcoin(response.info.currency, response.info.profit) }}</span></div>
                                </div>

                                <template v-if="response.info.server_seed !== '-1' && response.info.nonce !== '-1'">
									<div class="fair-info">
										<div class="client_seed mt-2" v-if="response && response.info.nonce !== -1">
											<div>{{ $t('general.fairness.client_seed') }}</div>
											<router-link :to="'/fairness?verify='+response.info.game+'-'+response.info.server_seed+'-'+response.info.client_seed+'-'+response.info.nonce" @click.native="close">{{ response.info.client_seed }}</router-link>
										</div>
										<div class="server_seed mt-2" v-if="response && response.info.nonce !== -1">
											<div>{{ $t('general.fairness.server_seed') }}</div>
											<router-link :to="'/fairness?verify='+response.info.game+'-'+response.info.server_seed+'-'+response.info.client_seed+'-'+response.info.nonce" @click.native="close">{{ response.info.server_seed }}</router-link>
										</div>
										<div class="nonce mt-2" v-if="response && response.info.nonce !== -1">
											<div>{{ $t('general.fairness.nonce') }}</div>
											<router-link :to="'/fairness?verify='+response.info.game+'-'+response.info.server_seed+'-'+response.info.client_seed+'-'+response.info.nonce" @click.native="close">{{ response.info.nonce }}</router-link>
										</div>
										<div class="nonVerifiable mt-2" v-if="response && response.info.nonce === -1">
											{{ $t('general.non_verifiable') }}
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
					background: rgba(t('header'), .6);
					box-shadow: rgb(0 0 0 / 40%) 2px 2px 8px 0px;
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
		
		.fair-info {
		    margin-top: 10px;
			border-radius: 3px;
			@include themed() {
				border: 1px solid rgba(t('text'), .1);
			}
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
