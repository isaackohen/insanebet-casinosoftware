<template>
    <button class="btn col-12 btn-primary mt-2 p-3 play-button" :disabled="disabled" @click="onClick">
        {{ getButtonText() }}
    </button>
</template>

<script>
    import { mapGetters } from 'vuex';
    import AuthModal from '../../modals/AuthModal';

    export default {
        computed: {
            ...mapGetters(['gameInstance', 'isGuest', 'demo', 'currencies', 'currency', 'quick', 'usd'])
        },
        mounted() { 
            disabled: false;
        },
        props: ['dis'],
        methods: {
            onClick() {
                const ref = window.$gameRef; 

                if(this.gameInstance.playTimeout && this.gameInstance.bettingType === 'manual' || $('.play-button').hasClass('disabled')) return;
                if(this.gameInstance.bettingType === 'auto' && this.isGuest) {
                    AuthModal.methods.open('auth');
                    return;
                }

                const stop = () => this.updateGameInstance((i) => i.game.autoBetSettings.state = false);

                const play = (successCallback = null, errorCallback = null) => {
                    this.updateGameInstance((i) => {
                        i.playTimeout = true;
                        i.game.currentProfit = null;
                    });

                    if(this.isExtendedGameStarted()) {
                        if(!ref.extendedAutoBetHandle || this.gameInstance.bettingType === 'manual') this.finishExtended();
                    } else {
                        if(ref.preStart) ref.preStart();
                        if(this.gameInstance.bettingType === 'manual') this.disabled = true;
                        axios.post('/api/game/play', {
                            api_id: this.gameInstance.game.id,
                            bet: ref.customWagerCalculation ? ref.customWagerCalculation() : (this.usd ? ((this.gameInstance.bet / this.currencies[this.currency].price) * 1.0005) : this.gameInstance.bet),
                            demo: this.demo,
                            currency: this.currency,
                            quick: this.quick,
                            data: typeof ref.getClientData === 'undefined' ? {} : ref.getClientData()
                        }).then(({ data }) => {
                            this.disabled = false;
							if(data.code != null){
								if(data.code === 0) {
									if(typeof errorCallback === 'function') errorCallback(0);
									return;
								}
								this.updateGameInstance((i) => i.playTimeout = false);

								if(errorCallback != null) errorCallback(data.code);

								if (data.code >= 1) {
									if(window.$gameRef.errorCallback) window.$gameRef.errorCallback(data.code);
									else this.$toast.error(this.$i18n.t('general.error.unknown_error', {'code': data.code}));
									return;
								}

								if (!_.isInteger(data.code)) {
									console.error('Invalid validation data');
									return;
								}

								switch (data.code) {
									case -1:
										this.$toast.error(this.$i18n.t('general.error.wager', { value: this.usd ? (this.currencies[this.currency].price * this.currencies[this.currency].min_bet).toFixed(2) : (this.currencies[this.currency].min_bet * 1.15).toFixed(8) }));
										return;
										break;
									case -9:
										this.$toast.error(this.$i18n.t('general.error.wager_max', { value: this.usd ? (this.currencies[this.currency].price * this.currencies[this.currency].max_bet).toFixed(2) : (this.currencies[this.currency].max_bet).toFixed(8) }));
										return;
										break;
									case -2:
										AuthModal.methods.open('auth');
										this.$toast.error(this.$i18n.t('general.error.auth'));
										return;
										break;
									case -3:
										this.$toast.error(this.$i18n.t('general.error.unknown_game'));
										return;
										break;
									case -4:
										this.$toast.error(this.$i18n.t('general.error.invalid_wager'));
										return;
										break;
									case -5:
										this.$toast.error(this.$i18n.t('general.error.disabled'));
										return;
										break;
									case -6:
										this.$toast.error(this.$i18n.t('general.error.disabled_bets'));
										return;
										break;
									case -7:
										if(this.isGuest) AuthModal.methods.open('auth');
										this.$toast.error(this.$i18n.t('general.error.disabled_demo_bets'));
										return;
										break;
									case -8:
										return window.location.reload();
										// Game already has started
										break;
								}
							}
						
                            this.$store.dispatch('pushRecentGame', this.gameInstance.game.id);

                            $('.game-history .history *').removeClass('highlight');
                            $('.resultPopup').stop().fadeOut('fast', function() {
                                $(this).remove();
                            });

                            if(data.response.game !== undefined) { // instanceof QuickGame
                                setTimeout(() => this.pushStats(data.response.game), data.response.game.delay);
                            } else if(data.response.type === 'extended') {
                                this.updateGameInstance((i) => {
                                    i.playTimeout = false;
                                    i.game.extendedId = data.response.id;
                                    i.game.extendedState = 'in-progress';
                                });
                            } else if(data.response.type === 'multiplayer') {
                                this.updateGameInstance((i) => {
                                    i.playTimeout = false;
                                    i.game.extendedId = data.response.id;
                                    i.game.extendedState = data.response.canBeFinished ? 'in-progress' : 'finished';
                                });
                            }

                            window.$gameRef.callback(data.response);
                            if(successCallback != null) successCallback(data.response);
                        });
                    }
                };

                if(this.gameInstance.bettingType === 'manual') play();
                else {
                    this.updateGameInstance((i) => i.game.autoBetSettings.stop = stop);
                    if(this.gameInstance.game.autoBetSettings.state) stop();
                    else {
                        this.updateGameInstance((i) => i.game.autoBetSettings.state = true);

                        const next = () => {
                            if(this.gameInstance.game.autoBetSettings.games > 0 && this.gameInstance.game.autoBetSettings.currentIteration >= this.gameInstance.game.autoBetSettings.games) stop();
                            else {
                                if(this.gameInstance.playTimeout) {
                                    setTimeout(() => {
                                        if(!this.gameInstance.game.autoBetSettings.state) return;
                                        next();
                                    }, 100);
                                    return;
                                }

                                if(+new Date() < this.gameInstance.game.autoBetSettings.timeout) {
                                    setTimeout(next, this.gameInstance.game.autoBetSettings.timeout - +new Date());
                                    return;
                                }

                                this.updateGameInstance((i) => i.game.autoBetSettings.timeout = +new Date() + 50);

                                const handleNext = (win) => {
                                    if (win && this.gameInstance.game.autoBetSettings.stopOnWin) stop();
                                    else {
                                        const handle = ref.customBetIncrease ? ref.customBetIncrease
                                            : (category) => {
                                                if(this.gameInstance.game.autoBetSettings[category].action === 'reset') this.updateGameInstance((i) => i.bet = this.gameInstance.game.autoBetSettings.initialBet);
                                                else if(this.gameInstance.game.autoBetSettings[category].value > 0) this.updateGameInstance((i) => i.bet + ((( this.gameInstance.game.autoBetSettings[category].value / 100) * i.bet)));
                                            };

                                        handle(win ? 'win' : 'loss');
                                        if(this.gameInstance.game.autoBetSettings.state) next();
                                    }
                                };

                                this.updateGameInstance((i) => i.game.autoBetSettings.nextGameHandler = handleNext);

                                if(!ref.extendedAutoBetHandle) play((response) => handleNext(response.game.win), stop);
                                else play(() => ref.extendedAutoBetHandle(() => this.finishExtended(true, (response) => handleNext(response.game.status === 'win'))));

                                this.updateGameInstance((i) => i.game.autoBetSettings.currentIteration++);
                            }
                        };

                        this.updateGameInstance((i) => {
                            i.game.autoBetSettings.initialBet = i.game.autoBetSettings.customBetIncrease ? i.game.autoBetSettings.customBetIncrease('initialBet') : i.bet;
                            i.game.autoBetSettings.currentIteration = 0;
                            i.game.autoBetSettings.next = next;
                        });

                        next();
                    }
                }
            },
            getButtonText() {
                if(this.gameInstance.game.autoBetSettings.state) return this.$i18n.t('general.stop');

                if(this.disabled == true) return this.$i18n.t('general.loader');

                if(this.isExtendedGameStarted()) {
                    if(this.gameInstance.game.currentProfit) return this.$i18n.t('general.take', { value: ((this.usd ? '$' : '') + this.gameInstance.game.currentProfit) });
                    return this.$i18n.t('general.cancel');
                }
                return this.gameInstance.bettingType === 'manual' ? this.$i18n.t('general.play') : this.$i18n.t('general.start');
            }
        }
    }
</script>
