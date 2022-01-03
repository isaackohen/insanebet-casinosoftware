<template>
    <div class="container-fluid">
        <div class="walletPage">
            <div class="walletUiBlocker" style="display: none">
                <div class="successfulWalletAction">
                    <div class="heading"></div>
                    <div class="content"></div>
                    <div class="d-flex ml-auto btn btn-primary close-action-notify" onclick="window.location.reload()">{{ $t('general.close') }}</div>
                </div>
            </div>
            <div class="walletTabs">
				<div :style="{ display: currency.startsWith('local_bonus') ? 'none' : 'block' }" :class="`walletTab ${tab === 'deposit' ? 'active' : ''}`" @click="loadTab('deposit')">{{ $t('wallet.tabs.deposit') }}</div>
				<div :style="{ display: currency.startsWith('local_bonus') ? 'none' : 'block' }" :class="`walletTab ${tab === 'withdraw' ? 'active' : ''}`" @click="loadTab('withdraw')">{{ $t('wallet.tabs.withdraw') }}</div>
				<div :style="{ display: currency.startsWith('local_bonus') ? 'none' : 'block' }" :class="`walletTab ${tab === 'history' ? 'active' : ''}`" @click="loadTab('history')">{{ $t('wallet.tabs.history') }}</div>
            </div>
            <div class="walletTabContent walletExchange" v-if="tab === 'exchange'">
				<template v-if="deposit !== 'local_bonus'">
					<div class="walletExchangeSelectors">
						<div class="walletExchangeSelector" @click="exchangeSendExpand = !exchangeSendExpand; exchangeReceiveExpand = false">
							<div class="wesContainer">
								<div class="icon"><icon :icon="exchangeSend.icon" :style="{ color: exchangeSend.style }"></icon></div>
								<div class="name">{{ exchangeSend.name }}</div>
							</div>
							<div class="exchangeList" v-if="exchangeSendExpand">
								<overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
									<div class="elEntry" v-for="currency in currencies" v-if="currency.balance && currency.id !== 'local_bonus'" @click="exchangeSend = currency">
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
						{{ usd ? '$' : ''}} {{ parseFloat(sum).toFixed(usd ? 2 : 8) }} <icon :icon="exchangeSend.icon" :style="{ color: exchangeSend.style }"></icon><strong>=</strong> {{ usd ? '$' : ''}} {{ predictExchangeResult() }} <icon :icon="exchangeReceive.icon" :style="{ color: exchangeReceive.style }"></icon>
					</div>
                    <div class="btn btn-primary btn-block  disabled">COMING SOON</div>
				</template>
            </div>
            <div class="walletTabContent" v-else-if="tab === 'deposit'">
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                        <div class="walletColumnContent">
                            <div class="mb-3">{{ $t('wallet.method') }}</div>
                            <div class="paymentMethods">
                                <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                                    <div :class="`paymentMethod ${deposit === currency.id ? 'active' : ''}`" v-for="currency in currencies" v-if="currency.balance && currency.id !== 'local_bonus'" @click="deposit = currency.id">
                                        <div class="icon">
                                            <icon :icon="currency.icon" :style="{ color: currency.style }"></icon>
                                        </div>
                                        <div class="name">
                                            {{ currency.displayName }}
                                        </div>
                                    </div>
                                </overlay-scrollbars>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8 col-xl-9">
                        <div class="walletColumnContent">
                            <template v-if="!deposit.startsWith('local_')">
                                <div class="paymentMethodDesc">
                                    {{ $t('wallet.method') }}
                                    <div class="mt-3 paymentDesc">
                                        <icon :icon="currencies[deposit].icon" :style="{ color: currencies[deposit].style }"></icon>
                                        {{ currencies[deposit].name }}
                                    </div>
                                </div>
								<template v-if="currencies.depoBonus === 'true'">
									<template v-if="!user.user.first_deposit_bonus || user.user.first_deposit_bonus !== 'used'"> 
										<div class="divider">
											<div class="line"></div>
											<i class="fal fa-angle-down"></i>
											<div class="line"></div>
										</div>
										<div class="walletOut deposit-bonus">
											<h6> <i class="fa fa-sack-dollar" aria-hidden="true"></i> {{ $t('bonus.doubler_deposit.title') }}</h6>
											<template v-if="!user.user.first_deposit_bonus">
												<div>{{ $t('bonus.doubler_deposit.description') }}</div>
												<div style="margin-top: 10px;">{{ $t('bonus.doubler_deposit.rules', { rules: currencies.depoBonusRoll }) }}</div>   
											</template>
											<template v-if="user.user.first_deposit_bonus === 'activated'">
												<div>{{ $t('bonus.doubler_deposit.activated.1') }}</div>
												<div style="margin-top: 10px;">{{ $t('bonus.doubler_deposit.activated.2') }}</div>   
											</template>
											<button v-if="!user.user.first_deposit_bonus" class="btn btn-primary" @click="activateFirstDepositBonus()" style="margin-top: 12px;">{{ $t('bonus.promo.activate') }}</button>
											<button v-if="user.user.first_deposit_bonus === 'activated'" class="btn btn-primary" @click="deactivateFirstDepositBonus()" style="margin-top: 12px;">{{ $t('general.cancel') }}</button>
										</div>
									</template>
								</template>
                                <div class="divider">
                                    <div class="line"></div>
                                    <i class="fal fa-angle-down"></i>
                                    <div class="line"></div>
                                </div>
                                <div class="walletOut">
                                    <div>{{ $t('wallet.deposit.address', { currency: currencies[deposit].name }) }}</div>
                                    <div class="input-loader">
                                        <input onclick="this.select()" style="cursor: pointer !important;" type="text" readonly v-model="depositWallet">
                                        <loader v-if="!depositWallet"></loader>
                                    </div>
									<div v-if="depositWallet" v-html="$t('wallet.deposit.min_deposit', { min_usd: depositMinUsd, min: depositMin, currency: currencies[deposit].name })"></div>
                                    <div class="qr">
                                        <loader v-if="!depositWallet"></loader>
                                        <div id="qr"></div>
                                    </div>
                                    <div>{{ $t('wallet.deposit.confirmations', { currency: currencies[deposit].name }) }}</div>
                                </div>
                                <div class="walletInfo mt-2">
                                    <div class="walletInfoBlock">
                                        <i class="fas fa-stopwatch"></i>
                                        <div class="mt-3" v-html="$t('wallet.fast')"></div>
                                    </div>
                                    <div class="walletInfoBlock">
                                        <i class="fas fa-headset"></i>
                                        <div class="mt-3" v-html="$t('wallet.troubles')"></div>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="divider">
                                    <div class="line"></div>
                                    <i class="fal fa-angle-down"></i>
                                    <div class="line"></div>
                                </div>
                                <div class="mt-2 mb-2">{{ $t('wallet.deposit.sum') }}</div>
                                <div class="walletButtons">
                                    <div :class="`walletButton ${sum === 500 ? 'active' : ''}`" @click="sum = 500">
                                        500₹
                                    </div>
                                    <div :class="`walletButton ${sum === 1000 ? 'active' : ''}`" @click="sum = 1000">
                                        1000₹
                                    </div>
                                    <div :class="`walletButton ${sum === 2500 ? 'active' : ''}`" @click="sum = 2500">
                                        2500₹
                                    </div>
                                    <div :class="`walletButton ${sum === 5000 ? 'active' : ''}`" @click="sum = 5000">
                                        5000₹
                                    </div>
                                    <div :class="`walletButton ${sum === 10000 ? 'active' : ''}`" @click="sum = 10000">
                                        10000₹
                                    </div>
                                    <div :class="`walletButton ${sum === 25000 ? 'active' : ''}`" @click="sum = 25000">
                                        25000₹
                                    </div>
                                    <div :class="`walletButton ${sum === 50000 ? 'active' : ''}`" @click="sum = 50000">
                                        50000₹
                                    </div>
                                    <div :class="`walletButton ${sum === 100000 ? 'active' : ''}`" @click="sum = 100000">
                                        100000₹
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="line"></div>
                                    <i class="fal fa-angle-down"></i>
                                    <div class="line"></div>
                                </div>
                                                                        <!--

                                <div class="aggregators">
                                    <div :class="`aggregator ${aggregator === 'jumponline' ? 'active' : ''}`" @click="aggregator = 'jumponline'">
                                        <div class="icon">
                                            <img class="jumptalk" alt src="/img/payment/jumptalk.png">
                                        </div>
                                        <div class="name">
                                            JTALK
                                        </div>
                                    </div>
                                </div>

                                <div class="divider">
                                    <div class="line"></div>
                                    <i class="fal fa-angle-down"></i>
                                    <div class="line"></div>
                                </div>
                                                                                                        !-->

                                <div class="walletOut">
                                    <div>{{ $t('wallet.deposit.sum') }} (INR₹)</div>
                                    <input type="text" v-model="sum" placeholder="Enter amount you wish to deposit..">
                                    <button class="btn btn-primary" @click="makeDeposit()">{{ $t('wallet.deposit.go') }}</button>
                                </div>
                                <div class="walletInfo mt-2">
                                    <div class="walletInfoBlock">
                                        <i class="fas fa-stopwatch"></i>
                                        <div class="mt-3" v-html="$t('wallet.fast')"></div>
                                    </div>
                                    <div class="walletInfoBlock">
                                        <i class="fas fa-headset"></i>
                                        <div class="mt-3" v-html="$t('wallet.troubles')"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="walletTabContent" v-if="tab === 'withdraw'">
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                        <div class="walletColumnContent">
                            <div class="mb-3">{{ $t('wallet.withdraw.method') }}</div>
                            <div class="paymentMethods">
                                <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                                    <div :class="`paymentMethod ${withdraw === currency.id ? 'active' : ''}`" v-for="currency in currencies" v-if="currency.balance && currency.id !== 'local_bonus'" @click="withdraw = currency.id">
                                        <div class="icon">
                                            <icon :icon="currency.icon" :style="{ color: currency.style }"></icon>
                                        </div>
                                        <div class="name">
                                            {{ currency.displayName }}
                                        </div>
                                    </div>
                                </overlay-scrollbars>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8 col-xl-9">
                        <div class="walletColumnContent">
                            <div class="paymentMethodDesc">
                                {{ $t('wallet.withdraw.method') }}
                                <div class="mt-3 paymentDesc">
                                    <icon :icon="currencies[withdraw].icon" :style="{ color: currencies[withdraw].style }"></icon>
                                    {{ currencies[withdraw].name }}
                                </div>
                            </div>
                            <div class="divider"> 
                                <div class="line"></div>
                                <i class="fal fa-angle-down"></i>
                                <div class="line"></div>
                            </div>
                            <div class="mt-2">
                                <div class="mb-2">{{ $t('wallet.withdraw.enter_banknumber') }}</div>
                                <input :placeholder="$t('wallet.withdraw.banknumber')" v-model="wallet">
                            </div>
                            <div class="mt-2">
                                <div class="mb-2">{{ $t('wallet.withdraw.enter_bankaccountname') }}</div>
                                <input :placeholder="$t('wallet.withdraw.bankaccountname')" v-model="bankaccountname">
                            </div>
                            <div class="mt-2">
                                <div class="mb-2">{{ $t('wallet.withdraw.enter_ifsc') }}</div>
                                <input :placeholder="$t('wallet.withdraw.ifsc')" v-model="ifsc">
                            </div>


                            <div class="divider">
                                <div class="line"></div>
                                <i class="fal fa-angle-down"></i>
                                <div class="line"></div>
                            </div>
                            <template v-if="withdraw.startsWith('local_')">
                                <div class="aggregators">
                                    <div :class="`aggregator ${localWithdrawType === 'bank' ? 'active' : ''}`" @click="localWithdrawType = 'bank'">
                                        <div class="icon">
                                            <i class="fas fa-money-check-alt"></i>
                                        </div>
                                        <div class="name">
                                            Bank
                                        </div>
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="line"></div>
                                    <i class="fal fa-angle-down"></i>
                                    <div class="line"></div>
                                </div>
                            </template>
                            <div class="walletOut">
                                <div>{{ $t('wallet.withdraw.amount', { min: (this.usd ? (currencies[withdraw].minimalWithdraw * this.currencies[withdraw].price).toFixed(2) : this.rawBitcoin(withdraw, currencies[withdraw].minimalWithdraw)), currency: (this.usd ? '$' : currencies[withdraw].name) }) }}</div>
                                <input type="text" v-model="sum" v-money="money">
                                <div class="mb-3" v-if="!withdraw.startsWith('local_')">{{ $t('wallet.withdraw.fee', { fee: (this.usd ? (currencies[withdraw].withdrawFee * this.currencies[withdraw].price).toFixed(2) : this.rawBitcoin(withdraw, currencies[withdraw].withdrawFee)), currency: (this.usd ? '$' : currencies[withdraw].name) }) }}</div>
                                <button class="btn btn-primary" @click="performWithdraw" :disabled="disableWithdraw">{{ $t('wallet.withdraw.go') }}</button>
                            </div>
                            <div class="walletInfo mt-2">
                                <div class="walletInfoBlock">
                                    <i class="fas fa-stopwatch"></i>
                                    <div class="mt-3" v-html="$t('wallet.fast')"></div>
                                </div>
                                <div class="walletInfoBlock">
                                    <i class="fas fa-headset"></i>
                                    <div class="mt-3" v-html="$t('wallet.troubles')"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="walletTabContent" v-if="tab === 'history'">
                <div class="walletHistory">
                    <div class="walletTabs">
                        <div :class="`walletTab ${historyTab === 'deposits' ? 'active' : ''}`" @click="historyTab = 'deposits'">{{ $t('wallet.tabs.deposits') }}</div>
                        <div :class="`walletTab ${historyTab === 'withdraws' ? 'active' : ''}`" @click="historyTab = 'withdraws'">{{ $t('wallet.tabs.withdraws') }}</div>
                    </div>
                    <div class="history-tab-content">
                        <loader v-if="historyLoading"></loader>
                        <template v-else-if="historyTab === 'deposits'">
                            <div v-if="deposits.length === 0" class="nothingInHistory">
                                <icon icon="waiting"></icon>
                                <div>{{ $t('wallet.history.empty') }}</div>
                            </div>
                            <table class="live-table" v-else>
                                <thead>
                                    <tr>
                                        <th>{{ $t('wallet.history.name') }}</th>
                                        <th class="d-none d-md-table-cell">{{ $t('wallet.history.sum') }}</th>
                                        <th>{{ $t('wallet.history.date') }}</th>
                                        <th>{{ $t('wallet.history.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="live_games">
                                    <tr v-for="deposit in deposits">
                                        <th>
                                            <div>
                                                <div>
                                                    <icon :icon="currencies[deposit.currency].icon" :style="{ color: currencies[deposit.currency].style }"></icon>
                                                    {{ currencies[deposit.currency].name }}
                                                </div>
                                            </div>
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ deposit.sum == null ? '~~~' : deposit.sum }} <icon :icon="currencies[deposit.currency].icon" :style="{ color: currencies[deposit.currency].style }"></icon>
                                        </th>
                                        <th>
                                            <div>{{ new Date(deposit.created_at).toLocaleString() }}</div>
                                        </th>
                                        <th>
                                            <div>
                                                <template v-if="deposit.status === 0">{{ $t('wallet.history.not_paid') }}</template>
                                                <template v-if="deposit.status === 1">{{ $t('wallet.history.paid') }}</template>
												<template v-if="deposit.status === 2">{{ $t('wallet.history.error') }}</template>
                                            </div>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </template>
                        <template v-else-if="historyTab === 'withdraws'">
                            <div v-if="withdraws.length === 0" class="nothingInHistory">
                                <icon icon="waiting"></icon>
                                <div>{{ $t('wallet.history.empty') }}</div>
                            </div>
                            <table class="live-table" v-else>
                                <thead>
                                    <tr>
                                        <th>
                                            {{ $t('wallet.history.name') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ $t('wallet.history.sum') }}
                                        </th>
                                        <th>
                                            {{ $t('wallet.history.date') }}
                                        </th>
                                        <th>
                                            {{ $t('wallet.history.status') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="live_games">
                                    <tr v-for="withdraw in withdraws">
                                        <th>
                                            <div>
                                                <div><icon :icon="currencies[withdraw.currency].icon" :style="{ color: currencies[withdraw.currency].style }"></icon> {{ currencies[withdraw.currency].name }}</div>
                                                <div data-highlight>{{ withdraw.address }}</div>
                                            </div>
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            <div>
                                                {{ withdraw.sum }} <icon :icon="currencies[withdraw.currency].icon" :style="{ color: currencies[withdraw.currency].style }"></icon>
                                            </div>
                                        </th>
                                        <th>
                                            <div>
                                                {{ new Date(withdraw.created_at).toLocaleString() }}
                                            </div>
                                        </th>
                                        <th>
                                            <span v-if="withdraw.status === 0 || withdraw.status === 3">
                                                {{ $t('wallet.history.withdraw_status.moderation') }}
                                                <div v-if="withdraw.status === 0 && !withdraw.auto" data-highlight class="clickable" @click="cancelWithdraw(withdraw._id)">{{ $t('wallet.history.cancel') }}</div>
                                            </span>
                                            <span v-else-if="withdraw.status === 1">
                                                <div class="text-success">{{ $t('wallet.history.withdraw_status.accepted') }}</div>
                                            </span>
                                            <span v-else-if="withdraw.withdraw_meta === 'SENDING' || withdraw.withdraw_meta === 'WAITING' || withdraw.withdraw_meta === 'PROCESSING'">
                                                <div class="text-warning">{{ $t('wallet.history.withdraw_status.sending') }}</div>
                                            </span>
                                            <span v-else-if="withdraw.withdraw_meta === 'FAILED'">
                                                <div class="text-warning">{{ $t('wallet.history.withdraw_status.failed') }}</div>
                                            </span>
                                            <span v-else-if="withdraw.status === 2">
                                                <div class="text-danger">{{ $t('wallet.history.withdraw_status.declined') }}</div>
                                                <div data-highlight>{{ $t('wallet.history.withdraw_status.reason') }} {{ withdraw.decline_reason }}</div>
                                            </span>
                                            <span v-else-if="withdraw.status === 4">{{ $t('wallet.history.withdraw_status.cancelled') }}</span>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </template>
                    </div>
                </div>
            </div>
        </div>
		<form id="jumponline" action="https://jumptalk.online/jtpayment/request.php" method="post">
		<input v-if="payData" type="text" name="clientId" :value="payData.data.clientId" style="display:none;"/>
		<input v-if="payData" type="text" name="merchantCode" :value="payData.data.merchantCode" style="display:none;"/>
		<input v-if="payData" type="text" name="securedText" :value="payData.data.securedText" style="display:none;"/>
		<input v-if="payData" type="text" name="orderId" :value="payData.data.orderId" style="display:none;"/>
		<input v-if="payData"type="text" name="amount" :value="payData.data.amount" style="display:none;"/>
		<input v-if="payData"type="text" name="email" :value="payData.data.email" style="display:none;"/>
		<input v-if="payData" type="text" name="phone" :value="payData.data.phone" style="display:none;"/>
		<input v-if="payData" type="text" name="redirectUrl" :value="payData.data.redirectUrl" style="display:none;"/>
		</form>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    const qr = require('qrcode');

    export default {
        data() {
            return {
                tab: 'deposit',
                historyTab: 'deposits',

                wallet: '',
                banknumber: '',
                bankaccountname: '',
                ifsc:'',
                deposit: null,
                withdraw: null,
                sum: null,

                aggregator: 'jumponline',
				payData: null,
                localWithdrawType: 'bank',

                deposits: [],
                withdraws: [],

                historyLoading: false,
                disableWithdraw: false,
 
                depositWallet: null,
				depositMin: null,
				depositMinUsd: null,

                exchangeSend: null,
                exchangeReceive: null,

                exchanging: false,

                exchangeAvailable: false,
                exchangeSendExpand: false,
                exchangeReceiveExpand: false,

                money: {
                    decimal: '.',
                    thousands: '',
                    prefix: '',
                    suffix: '',
                    precision: 8,
                    masked: false
                }
            }
        },
        created() {
            this.deposit = this.currency;
            this.withdraw = this.currency;

            this.exchangeSend = this.currencies[Object.keys(this.currencies).filter(e => e.includes("_"))[0]];
            this.exchangeReceive = this.currencies[Object.keys(this.currencies).filter(e => e.includes("_"))[1]];
			
			if(!this.currency.startsWith('local_')) this.getDepositInfo();

            let availableCurrencies = 0;
            _.forEach(this.currencies, (c) => {
                if(c.balance) availableCurrencies++;
            });

            this.exchangeAvailable = availableCurrencies > 1;
			
			if(this.currency == 'local_bonus') this.$router.push('/');
			this.setPrecision();
        },
        watch: {
            tab() {
                switch (this.tab) {
                    case "deposit": this.getDepositInfo(); break;
                    case "history": this.loadHistory(); break;
                }
            },
            historyTab() {
                this.loadHistory();
            },
            deposit() {
                if(!this.deposit.startsWith('local_')) this.getDepositInfo();
            },
			currency() {
				if(this.currency == 'local_bonus') { 
					this.$router.push('/');
				} else {
					this.tab = 'deposit';
					this.exchangeSend = this.currencies[Object.keys(this.currencies).filter(e => e.includes("_"))[0]];
				}
				this.deposit = this.currency;
				this.withdraw = this.currency;
			},
			usd() {
				this.setPrecision();
			}
        },
        computed: {
            ...mapGetters(['currencies', 'currency', 'user', 'usd'])
        },
        methods: {
			setPrecision() {
                this.money.precision = this.currency.startsWith('local_') ? 2 : (this.usd ? 2 : 8);
            },
			activateFirstDepositBonus() {
				axios.post('/api/promocode/depositBonus').then(({ data }) => {
					window.location.reload();
                });
			},
			deactivateFirstDepositBonus() {
				axios.post('/api/promocode/depositBonusCancel').then(({ data }) => {
					this.$store.dispatch('update');
                });
			},
            makeDeposit() {
				if(this.aggregator !== 'jumponline'){
					const win = window.open('', '_blank');
				}
                axios.post('/api/wallet/deposit', {
                    sum: parseFloat(this.sum),
                    type: this.deposit,
                    aggregator: this.aggregator
                }).then(({ data }) => {
					if(this.aggregator === 'jumponline'){
						this.payData = data.url;
						this.$toast.success(this.$i18n.t('wallet.s.waiting_to_redirect'));
						setTimeout(function () {
							$('#jumponline').submit();
						}, 2000);
					} else {
						win.location.href = data.url;
					}
                }).catch((error) => {
					if(this.aggregator !== 'jumponline'){
						win.close();
					}
                    this.$toast.error(this.$i18n.t('wallet.s.invalid_deposit'));
                });
            },
            exchange() {
                if(this.exchanging) return;
                this.exchanging = true;

                axios.post('/api/wallet/exchange', {
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
					}
                });
            },
            predictExchangeResult() {
                return this.usd ? (parseFloat(this.sum)).toFixed(2) : (this.usdToToken(this.exchangeReceive.price, this.tokenToUsd(this.exchangeSend.price, parseFloat(this.sum))).toFixed(8));
            },
            cancelWithdraw(id) {
                this.historyLoading = true;

                axios.post('/api/wallet/cancel_withdraw', { id: id }).then(() => {
                    this.$toast.success(this.$i18n.t('wallet.history.withdraw_cancelled'));
                    this.loadHistory();
                });
            },
            loadHistory() {
                this.historyLoading = true;

                this.deposits = [];
                this.withdraws = [];

                switch (this.historyTab) {
                    case "deposits":
                        axios.post('/api/wallet/history/deposits').then(({ data }) => {
                            this.deposits = data;
                            this.historyLoading = false;
                        });
                        break;
                    case "withdraws":
                        axios.post('/api/wallet/history/withdraws').then(({ data }) => {
                            this.withdraws = data;
                            this.historyLoading = false;
                        });
                        break;
                }
            },
            getDepositInfo() {
                const canvas = $(`<canvas></canvas>`);

                this.depositWallet = null;
                $('#qr canvas').remove();
                $('#qr .loader').show();

                axios.post('/api/wallet/getDepositWallet', { currency: this.deposit }).then(({ data }) => {
                    if(data.currency !== this.deposit) return;

                    this.depositWallet = data.wallet;
					this.depositMin = data.min_deposit;
					this.depositMinUsd = data.min_deposit_usd;

                    $('#qr canvas').remove();
                    qr.toCanvas(canvas[0], data.wallet);

                    $('#qr .loader').hide();
                    $('#qr').append(canvas[0]);
                }).catch((error) => {
                    switch (error.response.data.code) {
                        case 1: this.$toast.error(this.$i18n.t('general.error.deposit_disabled')); break;
                    }
					return;
                });
            },
            loadTab(tab) {
                this.tab = tab;
            },
            performWithdraw() {
                if(this.wallet.length < 5) {
                    this.$toast.error(this.$i18n.t('general.error.enter_wallet'));
                    return;
                }

                this.disableWithdraw = true;

                axios.post('/api/wallet/withdraw', {
                    sum: (this.usd ? (parseFloat(this.sum) / this.currencies[this.withdraw].price) : parseFloat(this.sum)),
                    currency: this.withdraw,
                    wallet: this.wallet,
                    banknumber: this.banknumber,
                    bankaccountname: this.bankaccountname,
                    ifsc: this.ifsc,
                    type: this.withdraw.startsWith('local_') ? this.localWithdrawType : null
                }).then(({ data }) => {
                    $('.successfulWalletAction .heading').html(this.$i18n.t('wallet.withdraw.title'));
                    $('.successfulWalletAction .content').html(`${this.$i18n.t('wallet.withdraw.content')} ${data.notifyAboutVip ? this.$i18n.t('wallet.withdraw.vip_content') : ''}`);
                    $('.successfulWalletAction, .walletUiBlocker').fadeIn('fast');

                    this.disableWithdraw = false;
                }).catch((error) => {
                    switch (error.response.data.code) {
                        case 1: this.$toast.error(this.$i18n.t('general.error.invalid_withdraw')); break;
                        case 2: this.$toast.error(this.$i18n.t('general.error.invalid_wager')); break;
                        case 3: this.$toast.error(this.$i18n.t('general.error.only_one_withdraw')); break;
                    }

                    this.disableWithdraw = false;
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";


    .walletPage {
        @include themed() {
            background: t('sidebar');
            border-radius: 3px;
            margin-top: 20px;
            margin-bottom: 20px;
            box-shadow: t('shadow');
            border: 1px solid t('border');
            position: relative;

            .aggregators {
                margin-top: 7px;
                display: flex;
                flex-wrap: wrap;
				justify-content: center;

                .aggregator.active {
                    background: darken(t('body'), 3%);
                    color: t('text');
                    border-bottom-color: t('secondary');
                }

                .aggregator {
                    display: flex;
                    flex-direction: row;
                    width: calc(25% - 10px);
                    border: 1px solid rgba(t('text'), 0.1);
                    transition: border 0.3s ease;
                    padding: 15px;
                    margin: 5px;
                    cursor: pointer;
                    align-items: center;

                    .icon {
                        margin-right: 8px;

                        img {
                            width: 22px;
                            height: 22px;
                        }
						
						.jumptalk {
							width: 50px !important;
							height: 25px !important;
							border-radius: 3px !important;
						}
                    }
                }
            }

            .walletExchange {
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
                            background: t('body');
                            transition: background .3s ease;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 10px 0;

                            &:hover {
                                background: lighten(t('body'), 5%);
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
                        background: t('body');
                        transition: background .3s ease;

                        &:hover {
                            background: lighten(t('body'), 5%);
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

            .nothingInHistory {
                text-align: center;
                margin: auto;

                svg {
                    margin-bottom: 15px;
                    font-size: 4.5em;
                }
            }

            .history-tab-content {
                padding: 25px;
                display: flex;
                flex-direction: column;
                flex: 1;

                .loaderContainer {
                    margin: auto;
                }
            }

            .walletUiBlocker {
                position: absolute;
                @include blur(t('sidebar'), 0.65, 0.95, 10px);
                width: 100%;
                height: 100%;
                display: flex;
                align-content: center;
                justify-content: center;
                z-index: 50;
                border-radius: 3px;

                .loaderContainer {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                }

                .successfulWalletAction {
                    height: fit-content;
                    display: flex;
                    flex-direction: column;
                    margin-top: auto;
                    margin-bottom: auto;
                    background: t('sidebar');
                    border-radius: 3px;
                    width: 100%;
                    padding: 20px;
                    z-index: 500;
                    border-top: 1px solid t('secondary');
                    border-bottom: 1px solid t('secondary');

                    .heading {
                        font-size: 1.1em;
                        font-weight: 600;
                    }

                    .content {
                        margin-top: 15px;
                        margin-bottom: 15px;
                    }

                    .btn {
                        width: 100px;
                        align-content: center;
                        justify-content: center;
                        text-align: center;
                    }
                }
            }

            .walletTabs {
                display: flex;
                flex-direction: row;
                border-top-left-radius: 3px;
                border-top-right-radius: 3px;
                .walletTab {
                    width: 100%;
                    display: inline-flex;
                    align-content: center;
                    justify-content: center;
                    padding: 15px;
                    text-align: center;
                    background: darken(t('input'), 1%);
                    border-bottom: 2px solid transparent;
                    color: rgba(t('text'), 0.7);
                    transition: all 0.3s ease;
                    cursor: pointer;

                    &:hover {
                        color: t('text');
                    }

                    &:first-child {
                        border-top-left-radius: 3px;
                        border-top-right-radius: 3px;
                    }
                }

                .walletTab.active {
                    background: darken(t('body'), 4%);
                    color: t('text');
                    border-bottom-color: t('secondary');
                }
            }

            .paymentDesc {
                img {
                    margin-right: 5px;
                }
            }

            .walletHistory {
                min-height: 600px;
                display: flex;
                flex-direction: column;
            }

            .walletHistoryEmpty {
                height: 400px;
                display: flex;
                flex-direction: column;
                align-content: center;
                justify-content: center;

                svg {
                    display: flex;
                    margin-left: auto;
                    margin-right: auto;
                    font-size: 6.5em;
                }

                div {
                    margin-top: 15px;
                    text-align: center;
                    font-size: 1.2em;
                }
            }

            .walletButtons {
                display: flex;
                flex-wrap: wrap;
                .walletButton {
                    width: calc(25% - 10px);
                    border: 1px solid rgba(t('text'), 0.1);
                    padding: 15px;
                    margin: 5px;
                    text-align: center;
                    transition: border-color 0.3s ease, color 0.3s ease;
                    color: rgba(t('text'), 0.8);
                    cursor: pointer;

                    &:hover {
                        border-color: rgba(t('text'), 0.15);
                        color: t('text');
                    }
                }

                .walletButton.active {
                    border-color: t('secondary');
                    color: t('text');
                }
            }

            .walletColumnContent {
                padding: 25px;
                height: 100%;
            }

            .walletInfo {
                display: flex;
                flex-direction: row;
                .walletInfoBlock {
                    display: inline-flex;
                    align-content: center;
                    justify-content: center;
                    text-align: center;
                    width: 50%;
                    height: 100%;
                    flex-direction: column;
                    position: relative;
                    padding: 30px;

                    i {
                        font-size: 2.5em;
                        margin-left: auto;
                        margin-right: auto;
                    }

                    &:first-child {
                        border-right: 1px solid rgba(t('text'), 0.1);
                    }
                }
            }

            .walletOut {
                padding: 20px;
                border-radius: 3px;
                border: 2px solid t('secondary');
                margin-top: 25px;
                margin-bottom: 25px;
                text-align: center;

                input {
                    margin-top: 15px;
                    margin-bottom: 15px;
                    text-align: center;
                }

                .btn {
                    width: 35%;
                    display: flex;
                    margin-left: auto;
                    margin-right: auto;
                    text-align: center;
                    padding: 20px;
                    font-weight: 600;
                    justify-content: center;
                    text-transform: uppercase;
                }
            }

            .deposit-bonus {
                border: 2px solid t('secondary');
            }
			
			.walletOut.deposit-bonus .btn {
				width: 100% !important;
				padding: 10px !important;
			}

            .paymentMethods {
                display: flex;
                flex-direction: column;

                .os-host {
                    max-height: 600px;
                }

                .paymentMethod {
                    border-left: 3px solid darken(t('input'), 5.5%);
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: row;
                    align-content: center;
                    margin-bottom: 10px;
                    cursor: pointer;

                    &:last-child {
                        margin-bottom: 0;
                    }

                    .icon {
                        border-top-right-radius: 7px;
                        border-bottom-right-radius: 7px;
                        padding: 6px 10px;
                        background: t('input');
                        transition: background 0.3s ease;
                    }

                    .name {
                        padding: 5px 15px;
                    }

                    &:hover {
                        border-left: 2px solid t('secondary');
                        background: t('secondary-alternative');
                        .icon {
                            background: darken(t('input'), 0.5%);
                        }
                    }
                }

                .paymentMethod.active {
                    border-left: 3px solid t('secondary');
                    background: darken(t('input'), 0.5%);
                    .icon {
                        background: darken(t('input'), 5.5%);
                    }
                }
            }
        }
    }

    .qr {
        width: 128px;
        height: 128px;
        background: white;
        margin-top: 1rem;
        margin-bottom: 1.7rem;
        position: relative;
        left: 50%;
        transform: translateX(-50%);

        canvas {
            width: 128px !important;
            height: 128px !important;
        }

        @include themed() {
            .loaderContainer {
                width: 100%;
                height: 100%;
                position: absolute;
                transform: scale(0.5) translate(-80%, -80%);
                top: 50%;
                left: 50%;
				
				.lds-ellipsis div {
					background: #000 !important;
				}
            }
        }
    }

    .theme--light {
        .qr {
            box-shadow: 0 0 1px 2px black;
        }
    }

    @include media-breakpoint-down(md) {
        .aggregator {
            width: calc(50% - 10px) !important;
        }
    }

    @include media-breakpoint-down(sm) {
        .walletColumnContent {
            border-right: none !important;
        }

        .walletOut {
            .btn {
                width: 100% !important;
            }
        }

        .walletInfo {
            flex-direction: column !important;

            .walletInfoBlock {
                border-right: none !important;
                width: 100% !important;
            }
        }

        .aggregator {
            width: calc(100% - 10px) !important;
        }
    }
</style>
