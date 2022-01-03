<template>
    <header>
        <div class="fixed">
            <div class="container-fluid inner">
                <div class="header-container">
                    <div class="logo">
                    <router-link to="/" tag="div" class="logo-image"></router-link>
                    </div>
                    <icon :icon="sidebar ? 'fal fa-times' : 'fas fa-bars'" class="sidebar-switch" @click.native="$store.dispatch('toggleSidebar')"></icon>

                    <content-placeholders v-if="!isGuest && !currencies" class="wallet_loader">
                        <content-placeholders-img/>
                    </content-placeholders>
                    <div class="wallet" v-if="!isGuest && currencies" v-click-outside="() => expand = false">
                        <div :class="`wallet-switcher ${expand ? 'active' : ''}`">
                            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                                <div v-for="(currency, i) in currencies" v-if="currency.balance" class="option" :key="i" @click="$store.commit('setCurrency', currency.id); setCookie('currency', currency.id, 365); expand = false">
                                    <div class="wallet-switcher-icon">
                                        <icon :icon="currency.icon" :style="{ color: currency.style }"></icon>
                                    </div>
                                    <div class="wallet-switcher-content">
                                        <div>
										<span v-if="usd">$</span>
										<unit :to="currency.id" :value="demo ? currency.balance.demo : currency.balance.real"></unit>
										</div>
                                        <span>{{ currency.name }}</span>
                                    </div>
                                </div>
                            </overlay-scrollbars>
							<!-- <div class="option mt-2" @click="$store.commit('setUsd', !usd)">
                                <div class="wallet-switcher-icon">
                                    <i :class="usd ? 'fas fa-check-square' : 'far fa-square'"></i>
                                </div>
                                <div class="wallet-switcher-content">
                                    {{ $t('general.head.view_usd') }}
                                </div>
                            </div> -->
                        </div>
                        <div class="btn btn-secondary icon" @click="expand = !expand">
                            <icon :icon="currencies[currency].icon" v-if="currencies[currency]" :style="{ color: currencies[currency].style }"></icon>
                            <i class="fal fa-angle-down"></i>
                        </div>
                        <div class="balance" @click="demo ? openDemoBalanceModal() : $router.push('/wallet')">
							<span style="margin-right:2px;" v-if="usd">$</span>
                            <unit :to="currency" :value="currencies[currency].balance[demo ? 'demo' : 'real']"></unit>
                            <transition-group mode="out-in" name="balance-a" :style="{ position: 'absolute' }">
                                <span :key="`key-${i}`" v-for="(animate, i) in animated" :class="`animated text-${animate.diff.action === 'subtract' ? 'danger' : 'success'}`">
                                    <unit :to="currency" :value="animate.diff.value"></unit>
                                </span>
                            </transition-group>
                        </div>
                        <div class="btn btn-primary wallet-open" @click="demo ? openDemoBalanceModal() : ((currency === 'local_bonus') ? openBonusBalanceModal() : $router.push('/wallet'))">{{ demo ? $t('general.head.wallet_open_demo') : ((currency === 'local_bonus') ? $t('wallet.exchange') : $t('general.head.wallet')) }} <i class="far fa-plus" style="margin-left: 5px;font-size: 13px;font-weight: 300;" aria-hidden="true"></i></div>
                    </div>
                    <div v-if="isGuest" :class="`right ${isGuest ? 'ml-auto' : ''}`">
						<div class="action">
							<div @click="$store.commit('switchTheme', true)">
								<i :class="`fas ${theme == 'dark' ? 'fa-sun' : 'fa-moon'}`" aria-hidden="true"></i>
							</div>
						</div>
                        <button class="btn btn-secondary" @click="openAuthModal('auth')">{{ $t('general.auth.login') }}</button>
                        <button style="margin-left:  5px;" class="btn btn-primary" @click="openAuthModal('register')">{{ $t('general.auth.register') }}</button>
                    </div>
                    <div v-else :class="`right ${isGuest ? 'ml-auto' : ''}`">
						<div class="action">
							<div @click="$store.commit('switchTheme', true)">
								<i :class="`fas ${theme == 'dark' ? 'fa-sun' : 'fa-moon'}`" aria-hidden="true"></i>
							</div>
						</div>
                        <div id="leaderboard-action" class="action" @click="openRankingsModal()">
                            <i class="fas fa-trophy-alt"></i>
                        </div>
                        <div class="action" data-notification-view @click="displayNotifications()">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <router-link tag="img" :to="`/profile/${user.user._id}`" :src="user.user.avatar"></router-link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import DemoBalanceModal from "../modals/DemoBalanceModal";
	import BonusBalanceModal from "../modals/BonusBalanceModal";
    import RankingsModal from "../modals/RankingsModal";
    import FaucetModal from "../modals/FaucetModal";
    import TermsModal from "../modals/TermsModal";

    export default {
        computed: {
            ...mapGetters(['user', 'isGuest', 'demo', 'unit', 'usd', 'currency', 'currencies', 'sidebar', 'theme'])
        },
        data() {
            return {
                expand: false,
                selectedUnit: 'btc',
                animated: [],

                nonLocalFound: false
            }
        },
        mounted() {
            this.selectedUnit = this.unit;

            Bus.$on('event:balanceModification', (e) => {
                setTimeout(() => {
                    const currencies = this.currencies;
                    currencies[e.currency].balance = {
                        real: e.balance,
                        demo: e.demo_balance
                    };
                    this.$store.dispatch('setCurrencies', currencies);

                    this.animated.push(e);
                    setTimeout(() => this.animated = this.animated.filter((a) => a !== e), 1000);
                }, e.delay);
            });
        },
        methods: {
            displayNotifications() {
                Bus.$emit('notifications:toggle');
            },
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            openDemoBalanceModal() {
                DemoBalanceModal.methods.open();
            },
			openBonusBalanceModal() {
                BonusBalanceModal.methods.open();
            },
            openRankingsModal() {
                RankingsModal.methods.open(this.currencies);
            },
            openFaucetModal() {
                if(this.isGuest) return this.openAuthModal('auth');
                FaucetModal.methods.open();
            },
            openTerms(type) {
                TermsModal.methods.open(type);
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";



    header {
        height: $header-height;
        display: initial !important;
        flex-shrink: 0;
        margin-top: -1px;
        z-index: 38001;

        button {
            font-size: 0.9em !important;
            padding: 10px 20px !important;
        }

        .fixed {

            max-width: $header-max-width;
            margin: $header-margin;
            height: $header-height;
            position: $header-position;
            left: 0;
            top: 0;
            margin-bottom: 5px;
            width: 100%;
            z-index: 10001;

            .local_last {
                background: red;
            }

            .inner {
                max-width: $header-inner-max-width;
                margin: $header-inner-margin;
            }

            .sidebar-switch {
                font-size: 1.25rem;
                margin-bottom: 8px;
                opacity: .95;
                @include themed() {
                color: rgba(t('secondary'), .95);
                }
                margin-right: 25px;
                margin-left: 10px;
                cursor: pointer;
            }

            .header-container {
                display: flex;
                align-items: center;
                height: $header-height;

                .menuSwitcher {
                    display: none;
                    margin-left: 15px;
                    opacity: .5;
                    transition: opacity .3s ease;
                    cursor: pointer;

                    &:hover {
                        opacity: 1;
                    }
                }
            }

                @include themed() {
                    background: t('header-top-bg');
                    color: t('header-top-color');
                

                .logo {
                    width: 105px;
                    height: 85px;
                    margin-right: 10px;
                    margin-left: 10px;
                    display: flex;
                    cursor: pointer;    

                    .logo-image {
                    background: t('logo') no-repeat center;
                    background-size: contain;
                    width: 105px;
                    height: 75px;
                    position: absolute;
                    }
                }
            }

            .menu {
                left: 125px;
                display: flex;

                @include themed() {
                    div {
                        margin: 10px;
                        color: t('header-top-color');
                        transition: all 0.3s ease;
                        position: relative;
                        cursor: pointer;
                        text-transform: uppercase;
                        font-weight: 600;

                        &:first-child {
                            margin-left: 0;
                        }

                        &:last-child {
                            margin-right: 0;
                        }

                    }

                    div.hover {
                        color: t('secondary-alternative');

                    }

                    div.active {
                        color: t('secondary-alternative');
                    }
                }
            }

            .right {
                display: flex;
                margin-left: 12px;
                align-items: center;

                img {
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    cursor: pointer;
                    margin-left: 10px;
                }

                .action {
                    display: flex;
                    align-content: center;
                    position: relative;

                    .notification {
                        position: absolute !important;
                        top: 7px !important;
                        left: 19px !important;
                        width: 8px !important;
                        height: 8px !important;
                    }

                    i {
                        font-size: 1.25em;
                        margin: 10px;
                        opacity: 0.65;
                        transition: opacity 0.3s ease;

                        &:hover {
                            opacity: 1;
                            cursor: pointer;
                        }
                    }
                }
            }

            @include themed() {
                transition: border-bottom 0.15s ease;
            }
        }
    }

    @include only_safari('header', (
        display: contents !important
    ));

    @media(max-width: 991px) {
        header .sidebar-switch {
            display: none;
        }
        #leaderboard-action {
            display: none;
        }
    }

    @media(max-width: 480px) {
        header .logo, .logo-image {
            display: none !important;
        }

    }

    .balance-a-enter-active, .balance-a-leave-active {
        transition: all 1s ease;
    }

    .balance-a-enter {
        margin-top: 25px;
        opacity: 1 !important;
    }

    .balance-a-enter-to {
        margin-top: 0;
        opacity: 0 !important;
    }

    .balance-a-leave, .balance-a-leave-to {
        opacity: 0 !important;
    }
</style>
