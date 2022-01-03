<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'bonus_balance',
                    title: 'general.head.wallet_bonus',
                    component: {
                        computed: {
                            ...mapGetters(['currency', 'currencies', 'user', 'usd'])
                        },
                        data() {
                            return {
                                getting: false,
								
								sum: 0,
								exchangeSend: null,
								exchangeReceive: null,

								exchanging: false,

								exchangeAvailable: false,
								exchangeSendExpand: false,
								exchangeReceiveExpand: false,
								bonus_wagered: 0,
								bonus_goal: 0,

								money: {
									decimal: '.',
									thousands: '',
									prefix: '',
									suffix: '',
									precision: 2,
									masked: false
								}
                            }
                        },
						created() {
							this.exchangeSend = this.currencies['local_bonus'];
							this.exchangeReceive = this.currencies[Object.keys(this.currencies).filter(e => e.includes("_"))[1]];
							axios.post('/api/promocode/bonusStatus').then(({ data }) => {
								this.bonus_wagered = data.bonus_wagered; 
								this.bonus_goal = data.bonus_goal; 
								this.sum = (data.bonus_balance).toFixed(2); 
							});
							this.predictExchangeResult();
						},
                        methods: {
                            get() {
                                this.getting = true;
                                axios.post('/api/promocode/demo').then(() => Bus.$emit('close')).catch(() => this.getting = false);
                            },
							exchange() {
								if(this.exchanging) return;
								this.exchanging = true;

								axios.post('/api/promocode/exchangeBonus', {
									amount: (this.usd ? (parseFloat(this.sum) / this.currencies[this.exchangeSend.id].price) : parseFloat(this.sum)),
									from: this.exchangeSend.id,
									to: this.exchangeReceive.id
								}).then(() => {
									this.exchanging = false;
									this.$toast.success(this.$i18n.t('wallet.exchange_success'));
								}).catch((error) => {
									this.exchanging = false;
									switch (error.response.data.code) {
										case 1:
											this.$toast.error(this.$i18n.t('general.chat_commands.modal.rain.invalid_amount'));
											break;
										case 2:
											this.$toast.error(this.$i18n.t('general.error.invalid_data'));
											break;
										case 3:
											this.$toast.error(this.$i18n.t('general.error.invalid_conditions'));
											break;
									}
								});
							},
							predictExchangeResult() {
								return this.usd ? (this.tokenToUsd(this.exchangeReceive.price, this.usdToToken(this.exchangeReceive.price, this.tokenToUsd(this.exchangeSend.price, parseFloat(this.sum)))).toFixed(2)) : (this.usdToToken(this.exchangeReceive.price, this.tokenToUsd(this.exchangeSend.price, parseFloat(this.sum))).toFixed(8));
							}
                        },
                        template: `
                            <div>
                                <div class="divider">
                                    <div class="line"></div>
                                    <i class="icon fak fa-bonuys"></i>
                                    <div class="line"></div>
                                </div>
                                <div class="wallet-content">
                                    <div class="notice" v-if="currencies[currency].balance.demo > 0">
                                        {{ $t('general.wallet.demo.error') }}
                                    </div>
									<loader v-if="!bonus_wagered"></loader>
									<div v-else class="walletTabContent bonusExchange">
										<div class="walletExchangeSelectors">
											<div class="walletExchangeSelector" @click="exchangeSendExpand = !exchangeSendExpand; exchangeReceiveExpand = false">
												<div class="wesContainer">
													<div class="icon"><icon :icon="exchangeSend.icon" :style="{ color: exchangeSend.style }"></icon></div>
													<div class="name">{{ exchangeSend.name }}</div>
												</div>
												<div class="exchangeList" v-if="exchangeSendExpand">
													<overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
														<div class="elEntry" v-for="currency in currencies" v-if="currency.id === 'local_bonus'" @click="exchangeSend = currency">
															<div class="icon"><icon :icon="currency.icon" :style="{ color: currency.style }"></icon></div>
															<div class="name">{{ currency.name }}</div>
														</div>
													</overlay-scrollbars>
												</div>
											</div>
											<icon icon="fal fa-chevron-right"></icon>
											<div class="walletExchangeSelector" @click="exchangeReceiveExpand = !exchangeReceiveExpand; exchangeSendExpand = false">
												<div class="wesContainer">
													<div class="icon"><icon :icon="exchangeReceive.icon" :style="{ color: exchangeReceive.style }"></icon></div>
													<div class="name">{{ exchangeReceive.name }}</div>
												</div>
												<div class="exchangeList" v-if="exchangeReceiveExpand">
													<overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
														<div class="elEntry" v-for="currency in currencies" v-if="currency.balance && currency.id !== 'local_bonus'" @click="exchangeReceive = currency">
															<div class="icon"><icon :icon="currency.icon" :style="{ color: currency.style }"></icon></div>
															<div class="name">{{ currency.name }}</div>
														</div>
													</overlay-scrollbars>
												</div>
											</div>
										</div>
										<input v-model="sum" v-money="money">
										<div class="exchangeResult">
											{{ usd ? '$' : ''}} {{ parseFloat(sum).toFixed(2) }} <icon :icon="exchangeSend.icon" :style="{ color: exchangeSend.style }"></icon><strong>=</strong> {{ usd ? '$' : ''}} {{ predictExchangeResult() }} <icon :icon="exchangeReceive.icon" :style="{ color: exchangeReceive.style }"></icon>
										</div>
										<div class="bonus-info">Bonus Wagered:<b><span> {{ bonus_wagered }}</span>₹</b></div>
										<div class="bonus-info">Bonus Goal:<b><span> {{ bonus_goal }}</span>₹</b></div>
										<div v-if="bonus_wagered <= bonus_goal" class="alert alert-secondary mb-2 mt-2 text-center" role="alert">{{ $t('bonus.info.1', { rules: currencies.depoBonusRoll }) }}</div>
										<div v-if="bonus_wagered > bonus_goal" class="alert alert-success mb-2 mt-2 text-center" role="alert">{{ $t('bonus.info.2') }}</div>
										<div class="btn btn-primary btn-block" @click="exchange" :disabled="exchanging">{{ $t('wallet.exchange') }}</div>
									</div>
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
	
	.bonusExchange {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 25px 0;

		input {
			width: 250px;
			margin-top: 15px;
			text-align: center;
		}

		.btn {
			width: 250px;
			margin-top: 15px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 600;
			text-transform: uppercase;
		}

		.exchangeResult {
			margin-top: 15px;

			strong {
				margin-left: 5px;
				margin-right: 5px;
			}
		}
	}

	.walletExchangeSelectors {
		display: flex;

		i {
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 15px;
		}

		.walletExchangeSelector {
			position: relative;
			cursor: pointer;
			margin-right: 15px;

			&:last-child {
				margin-right: 0;
			}

			.exchangeList {
				position: absolute;
				left: 0;
				width: 100%;
				z-index: 5;

				.os-host {
					max-height: 300px;
				}

				.elEntry {
					@include themed() {
						background: t('body');
					}
					transition: background .3s ease;
					display: flex;
					align-items: center;
					justify-content: center;
					padding: 10px 0;

					&:hover {
						@include themed() {
							background: lighten(t('body'), 5%);
						}
					}

					.icon {
						width: 25px;
						display: flex;
						align-items: center;
						justify-content: center;
						margin-right: 5px;
					}
				}
			}

			.wesContainer {
				display: flex;
				padding: 6px 13px;
				border-radius: 3px;
				@include themed() {
					background: t('body');
				}
				transition: background .3s ease;

				&:hover {
					@include themed() {
						background: lighten(t('body'), 5%);
					}
				}

				.icon {
					width: 30px;
					display: flex;
					align-items: center;
					justify-content: center;
					margin-right: 5px;
				}

				.name {
					margin-right: 10px;
				}
			}
		}
	}

    .xmodal.bonus_balance {
        max-width: 500px;

        .wallet-content {
            margin-top: 15px;
			
			.loaderContainer {
				display: flex;
				align-items: center;
				justify-content: center;
				margin-top: 15px;
				margin-bottom: 55px;
			}
			
			.bonus-info {
				margin-top: 8px;
			}
			
        }

        @include themed() {
            .notice {
                border-radius: 3px;
                border: 1px solid rgba(t('text'), 0.2);
                padding: 20px;
                text-align: center;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 95px;
                flex-direction: column;
            }
        }
    }
</style>
