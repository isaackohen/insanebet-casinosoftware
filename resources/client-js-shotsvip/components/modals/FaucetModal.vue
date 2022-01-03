<script>
    import { mapGetters } from 'vuex';
    import Bus from '../../bus';
    import VipModal from "./VipModal";
    import AuthModal from "./AuthModal";

    class Modal {
        constructor(vm) {
            this.vm = vm;
        }

        wheel() {
            const p = this.vm.currencies[this.vm.$data.currency_abs].price, rewards = [
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[0] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[0])),
                    color: '#f46e42'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[1] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[1])),
                    color: '#508bf0'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[2] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[2])),
                    color: '#df1347'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[3] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[3])),
                    color: '#d1d652'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[4] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[4])),
                    color: '#ffc645'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[5] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[5])),
                    color: '#f46e42'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[6] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[6])),
                    color: '#508bf0'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[7] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[7])),
                    color: '#df1347'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[8] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[8])),
                    color: '#d1d652'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[9] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[9])),
                    color: '#ffc645'
                },
                {
                    value: this.vm.usd ? this.vm.$data.wheel_slice[10] : this.vm.rawBitcoin(this.vm.$data.currency_abs, this.vm.usdToToken(p, this.vm.$data.wheel_slice[10])),
                    color: '#f46e42'
                }
            ];

            let slides = [];
            _.forEach(rewards, (reward) => {
                slides.push({
                    text: `${this.vm.usd ? ('$' + reward.value) : reward.value} ${this.vm.usd ? '' : this.vm.currencies[this.vm.$data.currency_abs].name.substr(0, 3)}`,
                    value: slides.length,
                    border: {
                        radius: 3.25,
                        fill: reward.color
                    }
                });
            });

            $('.wheel').wheel({
                slices: slides,
                selector: 'value',
                width: 350,
                text: {
                    color: "white",
                    size: 11,
                    offset: 5,
                    arc: false
                },
                outer: {
                    width: 0,
                    color: 'transparent'
                },
                inner: {
                    width: 11,
                    color: '#222127'
                },
                line: {
                    width: 3,
                    color: '#222127'
                },
                slice: {
                    background: '#2a2a2f'
                }
            });

            $('.wheel').wheel('onStep', () => this.vm.playSound('/sounds/tick.mp3'));

            $('.wheel').wheel('onComplete', () => {
                this.vm.timeout();
                $('.wheelSpin').addClass('disabled');
                $('.wheelBlock').fadeIn('fast');
            });

            $('[data-bonus-modal-contents] .wheelSpin').on('click', () => {
                if($('[data-bonus-modal-contents] .wheelSpin').hasClass('disabled')) return;
                $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', true);

                axios.post('/api/promocode/bonus').then(({ data }) => {
                    $('.wheelBlock').fadeOut('fast');
                    window.next = data.next;
                    $('.wheel').wheel('start', data.slice);
                }).catch((error) => {
                    $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', false);
                    if(error.response.data.code === 2) this.vm.$toast.error(this.vm.$i18n.t('general.error.should_have_empty_balance'));
                });
            });
        }


        promocode() {
            $('#activate').on('click', () => {
                if($('#activate').hasClass('disabled')) return;
                $('#activate').addClass('disabled');

                axios.post('/api/promocode/activate', { code: $('#code').val() }).then(() => {
                    $('#activate').removeClass('disabled');
                    this.vm.$toast.success(this.vm.$i18n.t('bonus.promo.success'));
                }).catch((code) => {
                    if(code.response.data.code === 1) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.invalid'));
                    if(code.response.data.code === 2) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.expired_time'));
                    if(code.response.data.code === 3) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.expired_usages'));
                    if(code.response.data.code === 4) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.used'));
                    if(code.response.data.code === 5) this.vm.$toast.error(this.vm.$i18n.t('general.error.promo_limit'));
                    if(code.response.data.code === 7) this.vm.$toast.error(this.vm.$i18n.t('general.error.vip_only_promocode'));
                    if(code.response.data.code === 8) this.vm.$toast.error(this.vm.$i18n.t('general.error.promo_balance_limit'));

                    $('#activate').removeClass('disabled');
                });
            });
        }

        telegram() {
            $('[data-check-subscription]').on('click', () => {
                if($('[data-check-subscription]').hasClass('disabled')) return;
                $('[data-check-subscription]').addClass('disabled');

                axios.post('/api/promocode/telegram_bonus').then(() => {
                    this.vm.$toast.success(this.vm.$i18n.t('bonus.telegram.success'));
                    this.vm.$router.go();
                }).catch((code) => {
                    this.vm.$toast.error(this.vm.$i18n.t('bonus.telegram.error.' + code.response.data.code));
                    $('[data-check-subscription]').removeClass('disabled');
                });
            });
        }
    }

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'faucet',
                    component: {
                        data() {
                            return {
                                tab: 'wheel',
								currency_abs: null,
								wheel_slice: null,

                                interval: null
                            }
                        },
                        computed: {
                            ...mapGetters(['isGuest', 'user', 'currencies', 'currency'])
                        },
                        watch: {
							currency_abs() {
                                this.loadTab();
                            },
                            tab() {
                                this.loadTab();
                            },
                            currency() {
                                this.loadTab();
                            }
                        },
						created() {
							axios.post('/api/promocode/slices').then(({ data }) => {
								this.currency_abs = data.currency; 
								this.wheel_slice = data.wheel; 
							});
						},
                        methods: {
                            loadTab() {
                                let html = '';
                                switch (this.tab) {
                                    case 'telegram':
                                        html = `
                                            <div class="bonus-side-menu-container text-center">
                                                <div class="header text-center">
                                                    <div class="title">${this.$i18n.t('bonus.telegram.title')}</div>
													<div class="description">${this.$i18n.t('bonus.telegram.description')}</div>
                                                </div>
                                                <div class="bonusContent">
                                                    ${this.user.user.telegram_bonus ? `` : `<div>${this.$i18n.t('bonus.telegram.common_desc')}</div>`}
                                                    ${this.user.user.telegram ? (this.user.user.telegram_bonus ? `<div class="description">${this.$i18n.t('bonus.telegram.error.2')}</div>` : `
                                                        <button class="btn btn-primary btn-block mt-2" data-check-subscription>${this.$i18n.t('bonus.telegram.check')}</button>
                                                    `) : `
                                                        ${this.$i18n.t('bonus.telegram.link')}
                                                        <button class="btn btn-primary btn-block mt-2" onclick="window.location.href = '/profile/${this.user.user._id}#settings'">${this.$i18n.t('bonus.telegram.redirect')}</button>
                                                    `}
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'rain':
                                        html = `<div class="bonus-side-menu-container text-center">
                                                    <div class="header text-center">
                                                        <div class="title">${this.$i18n.t('bonus.rain.title')}</div>
                                                        <div class="description">${this.$i18n.t('bonus.rain.description')}</div>
                                                    </div>
                                                    <div class="bonusContent" style="margin-top: 50px;">${this.$i18n.t('bonus.rain.modal_description')}</div>
                                                </div>`;
                                        break;
                                    case 'promo':
                                        html = `
                                            <div class="bonus-side-menu-container">
                                                <div class="header text-center">
                                                    <div class="title">${this.$i18n.t('bonus.promo.title')}</div>
                                                    <div class="description">${this.$i18n.t('bonus.promo.description')}</div>
                                                </div>
                                                <div class="bonusContent">
                                                    <div class="mt-2">
                                                        <input id="code" type="text" placeholder="${this.$i18n.t('bonus.promo.placeholder')}">
                                                    </div>
                                                    <button id="activate" class="btn btn-primary mt-2">${this.$i18n.t('bonus.promo.activate')}</button>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'wheel':
                                        html = `
                                            <div class="bonus-side-menu-container">
                                                <div class="header">
                                                    <div class="title">${this.$i18n.t('bonus.lucky_spin_everyday')}</div>
                                                    <div class="description">${this.$i18n.t('bonus.have_a_try')}</div>
                                                </div>
                                                <div class="wheelContainer">
                                                    <div class="wheel"></div>
                                                    <div class="wheelSpin">${this.$i18n.t('general.spin')}</div>

                                                    <div class="wheelBlock">
                                                        <svg><use href="#red-diamond"></use></svg>
                                                        <div class="ribbon">
                                                            <div class="ribbon-content"><p><b id="reload"></b></p></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;

                                        window.next = this.user.user.bonus_claim ? +new Date(this.user.user.bonus_claim) / 1000 : 0;
                                        this.timeout();
                                        break;
                                }

                                $('[data-bonus-modal-contents]').hide().html(html).fadeIn('fast');

                                const modal = new Modal(this);
                                switch (this.tab) {
                                    case 'telegram':
                                        modal.telegram();
                                        break;
                                    case 'promo':
                                        modal.promocode();
                                        break;
                                    case 'wheel':
                                        modal.wheel();
                                        this.timeout();
                                        break;
                                    case 'partner':
                                        modal.partner();
                                        break;
                                }
                            },
                            timeout() {
                                if(this.interval != null) {
                                    clearInterval(this.interval);
                                    this.interval = null;
                                }

                                if(window.next && +new Date() / 1000 < window.next) {
                                    const timer = () => {
                                        const diff = ((window.next - (Date.now() / 1000)) | 0);
                                        let hours = Math.floor((diff % (60 * 60 * 24)) / (60 * 60));
                                        let minutes = ((diff % 3600) / 60) | 0;
                                        let seconds = (diff % 60) | 0;

                                        if(hours === 0 && minutes === 0 && seconds < 1) {
                                            clearInterval(this.interval);
                                            $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', false);
                                            $('#reload').html(this.$i18n.t('bonus.spin_now'));
                                            return;
                                        }

                                        hours = hours < 10 ? "0" + hours : hours;
                                        minutes = minutes < 10 ? "0" + minutes : minutes;
                                        seconds = seconds < 10 ? "0" + seconds : seconds;

                                        $('#reload').html(this.$i18n.t('bonus.next_spin') + `${hours}:${minutes}:${seconds}`);
                                        $('.wheelSpin').toggleClass('disabled', true);
                                    };

                                    this.interval = setInterval(() => {
                                        if($('#reload').length === 0) {
                                            clearInterval(this.interval);
                                            return;
                                        }

                                        timer();
                                    }, 1000);
                                    timer();
                                } else {
                                    $('#reload').html(this.$i18n.t('bonus.spin_now'));
                                    $('.wheelSpin').toggleClass('disabled', false);
                                }
                            },
                            openVipModal() {
                                Bus.$emit('modal:close');
                                VipModal.methods.open();
                            }
                        },
                        template: `
                                <div class="bonusContainer">
                                    <div data-bonus-modal-contents>
											<loader v-if="!currency_abs"></loader>
									</div>

                                    <div class="bonusSidebar">
                                        <div :class="'sidebarEntry ' + (tab === 'wheel' ? 'active' : '')" @click="tab = 'wheel'">
                                            <div class="icon">
                                                <icon icon="wheel"></icon>
                                            </div>
                                        </div>
                                        <div class="sidebarEntry" @click="openVipModal()">
											<div class="icon">
												<i class="fas fa-crown"></i>
											</div>
										</div>
                                        <div :class="'sidebarEntry ' + (tab === 'promo' ? 'active' : '')" @click="tab = 'promo'">
                                            <div class="icon">
                                                <i class="fas fa-barcode-alt"></i>
                                            </div>
                                        </div>
                                        <div :class="'sidebarEntry ' + (tab === 'telegram' ? 'active' : '')" @click="tab = 'telegram'">
                                            <div class="icon">
                                                <i class="fab fa-telegram"></i>
                                            </div>
                                        </div>
                                        <div :class="'sidebarEntry ' + (tab === 'rain' ? 'active' : '')" @click="tab = 'rain'">
                                            <div class="icon">
                                                <i class="fas fa-cloud-sun-rain"></i>
                                            </div>
                                        </div>
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

    .xmodal.faucet {
        overflow-x: unset !important;

        .modal_template, .modal_content, .os-viewport, .os-padding, .os-host-overflow {
            overflow: unset !important;
        }

        ul {
            list-style: none;
        }

        .bonusContainer {
            @include themed() {
                height: 100%;
                background: t('sidebar');
                border-radius: 3px;
                border: 1px solid t('border');
                box-shadow: t('shadow');

                .bonus-side-menu-container {
                    .header {
                        background: t('body');
                        width: calc(100% + 40px);
                        text-align: right;
                        padding-right: 45px;
                        padding-top: 15px;
                        padding-bottom: 15px;
                        margin-left: -20px;
                        position: absolute;
                        top: 0;

                        .title {
                            font-size: 1.1em;
                            font-weight: 600;
                        }

                        &.text-center {
                            padding-right: 0;

                            .description {
                                padding-left: 10%;
                                padding-right: 10%;
                            }
                        }
                    }
                }

                .wheel {
                    margin-left: -35%;
                    margin-top: -50%;
                    position: absolute;
                }

                .bonusSidebar {
                    width: 100%;
                    display: flex;
                    position: absolute;
                    bottom: 25px;
                    left: 0;
                    height: 63px;

                    $inactive: t('input');
                    $active: t('secondary');

                    .sidebarEntry {
                        position: relative;
                        display: flex;
                        cursor: pointer;
                        margin-right: 85px;
                        width: 62px;
                        height: 62px;
                        margin-right: 20px;

                        &:first-child {
                            margin-left: auto;
                        }

                        &:last-child {
                            margin-right: auto;
                        }

                        .icon {
                            background: $inactive;
                            width: 62px;
                            height: 62px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transition: background 0.3s ease;

                            i, svg {
                                font-size: 1.1em;
                                color: rgba(t('text'), 0.5);
                                transition: color 0.3s ease;
                            }
                        }
                    }

                    .sidebarEntry.active {
                        .icon {
                            background: $active;

                            i, svg {
                                color: white;
                            }
                        }
                    }
                }
            }
        }

        .bonusContent {
            padding-left: 50px;
            padding-right: 80px;
            display: flex;
            flex-direction: column;
        }

        [data-bonus-modal-contents] {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            min-height: 400px;

            @include themed() {
                .wheelContainer {
                    position: relative;
                    user-select: none;

                    .wheelSpin:not(.btn) {
                        position: absolute;
                        background: linear-gradient(-180deg, darken(t('secondary'), 0.5%), darken(t('secondary'), 3.5%));
                        top: -155px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 101px;
                        height: 102px;
                        left: -71px;
                        text-transform: uppercase;
                        font-size: 1.2em;
                        cursor: pointer;
                        color: #f1f1f1;
                        transition: background .3s ease, color .3s ease;

                        animation: pulse 1s infinite ease-in-out;

                        &.disabled {
                            background: t('sidebar');
                            color: transparent;
                            cursor: default;
                            animation: unset !important;
                        }
                    }

                    @keyframes pulse {
                        0% {
                            background: #d64b4b;
                        }

                        50% {
                            background: #ff5858;
                        }

                        100% {
                            background: #d64b4b;
                        }
                    }
                }

                .wheelBlock {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    position: absolute;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    margin-top: 45px;
                    margin-left: 60px;

                    #reload {
                        text-transform: uppercase;
                    }

                    svg {
                        width: 70px;
                        height: 90px;
                        margin-bottom: 170px;
                    }

                    .ribbon {
                        width: 250px;
                        position: absolute;
                        text-align: center;
                        font-size: 18px !important;
                        background: linear-gradient(-180deg, darken(t('secondary'), 0.5%), darken(t('secondary'), 3.5%));

                    }

                    .ribbon p {
                        font-size: 18px !important;
                        margin: 0;
                        color: #f1f1f1;
                        padding: 15px 10px;
                    }

                    .ribbon:before, .ribbon:after {
                        content: '';
                        position: absolute;
                        display: block;
                        bottom: -1em;
                        border: 1.5em solid darken(t('secondary'), 0.5%);
                        z-index: -1;
                    }

                    .ribbon:before {
                        left: -2em;
                        border-right-width: 1.5em;
                        border-left-color: transparent;
                    }

                    .ribbon:after {
                        right: -2em;
                        border-left-width: 1.5em;
                        border-right-color: transparent;
                    }

                    .ribbon .ribbon-content:before, .ribbon .ribbon-content:after {
                        border-color: darken(t('secondary'), 3.5%) transparent transparent transparent;
                        position: absolute;
                        display: block;
                        border-style: solid;
                        bottom: -1em;
                        content: '';
                    }

                    .ribbon .ribbon-content:before {
                        left: 0;
                        border-width: 1em 0 0 1em;
                    }

                    .ribbon .ribbon-content:after {
                        right: 0;
                        border-width: 1em 1em 0 0;
                    }
                }
            }
        }
    }

    @include media-breakpoint-down(md) {
        .xmodal.faucet {
            .wheel {
                margin-left: 0 !important;
                margin-right: auto !important;
                transform: scale(.8) translate(-5%, -5%);
                margin-top: -270px !important;
            }

            .wheelBlock {
                margin-top: 40px !important;
                margin-left: 140px !important;
                transform: translate(-50%, -50%) scale(0.7) !important;
            }

            .wheelSpin {
                transform: scale(.8) translate(0%, 50%);
                left: 111px !important;
                top: -200px !important;
            }
        }
    }

    @include media-breakpoint-down(sm) {
        .xmodal.faucet {
            .wheel {
                transform: scale(0.65) translate(-25%, -5%);
                margin-top: -310px !important;
            }

            .header * {
                color: transparent !important;
            }

            .bonusSidebar {
                height: 35px !important;
            }

            .sidebarEntry {
                width: 42px !important;
                height: 42px !important;
                margin-right: 5px !important;

                &:last-child {
                    margin-right: auto !important;
                }

                .icon {
                    width: 42px !important;
                    height: 42px !important;

                    svg, i {
                        font-size: 0.6em !important;
                    }
                }
            }

            .wheelSpin {
                transform: scale(0.6) translate(0%, 50%);
                left: 68px !important;
                top: -227px !important;
            }

            .wheelBlock {
                margin-top: 90px !important;
                margin-left: 0 !important;
            }

            .sWheel-wrapper, .wheel {
                width: 350px !important;
                height: 350px !important;
                font-size: 87% !important;
            }
        }
    }
</style>
