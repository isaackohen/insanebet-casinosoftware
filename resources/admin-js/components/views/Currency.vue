<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Coins</h4>
				</div>
			</div>
		</div>
        <div class="container-fluid" v-if="data">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-xl-10 col-lg-9">
                                <div class="mt-4 mt-lg-0">
                                    <h5 class="mt-0 mb-1 fw-bold">Global Withdraw Settings</h5>
                                    <p>The "count"-settings are automatically reset, you should not alter this unless you want to manually reset the limit.</p>
                                </div>
                            </div>
                        </div>
                        
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div>
            <!-- end col-12 -->
        </div>
        
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="extraSetting in extraSettings">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-muted">{{ extraSetting.name }}</a></h5>
                        <div class="text-muted">
                            <div class="form-group mt-2">
                                <input @input="changeExtra(extraSetting.name, $event.target.value)" :value="extraSetting.value" type="text" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div v-if="availableCurrencies.length === 0">
                                Loading...
                            </div>
                            <div v-for="currency in availableCurrencies" class="d-flex align-items-center mt-2 mb-2">
                                <div>
                                    {{ currency.displayName }}
                                </div>
                                <div class="ms-auto">
                                    <select @change="toggleCurrency(currency.walletId, $event.target.value)">
                                        <option value="disabled">Disabled</option>
										<option :selected="currencies['cg_' + currency.walletId]" value="cg" v-if="Object.values(data.coins).filter((e) => e.walletId === currency.walletId && e.id.startsWith('cg_')).length > 0">СhainGateway</option>
										<option :selected="currencies['np_' + currency.walletId]" value="np" v-if="Object.values(data.coins).filter((e) => e.walletId === currency.walletId && e.id.startsWith('np_')).length > 0">NowPayments</option>
                                        <option :selected="currencies['native_' + currency.walletId]" value="native" v-if="Object.values(data.coins).filter((e) => e.walletId === currency.walletId && e.id.startsWith('native_')).length > 0">Native</option>
                                        <option :selected="currencies['bg_' + currency.walletId]" value="bg" v-if="Object.values(data.coins).filter((e) => e.walletId === currency.walletId && e.id.startsWith('bg_')).length > 0">BitGo</option>
                                        <option :selected="currencies['local_' + currency.walletId]" value="local" v-if="Object.values(data.coins).filter((e) => e.walletId === currency.walletId && e.id.startsWith('local_')).length > 0">Local</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row" v-if="data.foundEmpty">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Send deposits to address</h5>
                            <small>For native nodes only</small>
                            <div class="row mt-2">
                                <div class="col-12 col-lg-2">
                                    <select class="form-control" id="cs_currency">
                                        <option v-for="currency in availableCurrencies.filter(e => e.id.startsWith('native_'))" v-if="currency.balance" :value="currency.id">{{ currency.name }}</option>
                                    </select>
                                </div>
                                <div class="col-8 mt-2 mt-lg-0">
                                    <input class="form-control" v-model="address" placeholder="Address">
                                    <input class="form-control mt-2" v-model="amount" placeholder="Amount">
                                </div>
                                <div class="col-3 col-lg-2 mt-2 mt-lg-0">
                                    <button class="btn btn-danger btn-block" @click="send">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4" v-for="currency in currencies" v-if="currency.balance">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ currency.name }}</h5>
                            <div class="text-danger" v-if="balance[currency.id] && !balance[currency.id].status"><strong>Node is offline</strong></div>
                            <div v-else>
                                <div v-if="balance[currency.id] && balance[currency.id].deposit > 0">
                                    <strong>Deposit wallet balance:</strong>
                                    <div class="spinner-grow spinner-grow-sm" v-if="!balance[currency.id]"></div>
                                    <span v-else>{{ balance[currency.id].deposit }}</span>
                                </div>
                                <div v-if="balance[currency.id] && balance[currency.id].withdraw > 0">
                                    <strong>Auto-withdraw wallet balance:</strong>
                                    <div class="spinner-grow spinner-grow-sm" v-if="!balance[currency.id]"></div>
                                    <span v-else>{{ balance[currency.id].withdraw }}</span>
                                </div>
                            </div>
                            <div class="mt-2" v-if="data.options && data.options[currency.id]">
                                <div class="form-group mt-2" v-for="option in data.options[currency.id]">
                                    <label data-toggle="tooltip" data-placement="top" :title="option.id">{{ option.name }}
										<template v-if="option.id != 'icon' && option.id != 'demo' && option.id != 'withdraw_address' && option.id != 'fund_address' && option.id != 'contract_address'">
											<br><small :id="currency.id + '_' + option.id">{{ (option.value * currencies[currency.id].price).toFixed(3) }}$</small>
										</template>
									</label>
                                    <input :disabled="option.readOnly" :value="option.value" @input="change(currency.id, option.id, $event.target.value)" type="text" class="form-control">
                                </div>
                            </div>
							<div v-else class="text-center mt-2" v-if="!data.options">
								<div class="spinner-border m-2" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
                            <div v-if="data.options && currency.id === 'native_eth'">
                                <hr>
                                <div class="form-group mt-2">
                                    <label>Send deposits to this address</label>
                                    <input v-model="ethDepositAddr" type="text" class="form-control">
                                </div>
                                <hr>
                                <button class="btn btn-primary" :disabled="ethDepositAddr.length < 40" @click="sendEthDeposits">Send deposits</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['currencies'])
        },
        created() {
            axios.post('/admin/currencyExtraSettings').then(({ data }) => this.extraSettings = data); 

            axios.post('/admin/currencySettings').then(({ data }) => {
                let duplicates = [];
                _.forEach(data.coins, (e) => {
                    if(e.balance && !duplicates.includes(e.walletId)) {
                        duplicates.push(e.walletId);
                        this.availableCurrencies.push(e);
                    }
                });

                this.data = data;
            });

            axios.post('/admin/currencyBalance').then(({ data }) => this.balance = data);
        },
        data() {
            return {
                data: {},
                balance: {},
                extraSettings: [],
                address: '',
                amount: 0.00000000,
                availableCurrencies: [],
                ethDepositAddr: ''
            }
        },
        methods: {
            sendEthDeposits() {
                this.$toast.success('Success');
                axios.post('/admin/ethereumNativeSendDeposits', { toAddr: this.ethDepositAddr });
            },
            toggleCurrency(walletId, type) {
                axios.post('/admin/toggleCurrency', {
                    walletId: walletId,
                    type: type
                }).then(() => this.$store.dispatch('updateData'));
            },
            changeExtra(key, value) {
                axios.post('/admin/settings/edit', { key: key, value: value.length === 0 ? '0' : value });
            },
            send() {
                axios.post('/admin/wallet/transfer', {
                    'currency': $('#cs_currency').val(),
                    'amount': this.amount,
                    'address': this.address
                }).then(() => {
                    this.$toast.success('Success');
                }).catch(() => {
                    this.$toast.error('Error');
                })
            },
            change(cId, oId, value) {
                axios.post('/admin/currencyOption', { currency: cId, option: oId, value: value });
				$('#' + cId + '_' + oId).html((value * this.currencies[cId].price).toFixed(3) + '$');
            },
            autogen() {
                if($('#autogen').hasClass('disabled')) return;
                $('#autogen').attr('disabled', 'disabled').addClass('disabled').html('Generating...');

                const request = new XMLHttpRequest();
                request.responseType = 'blob';

                request.addEventListener('readystatechange', function(e) {
                    if(request.readyState === 4) window.location.reload();
                });

                request.open('get', '/admin/wallet/autoSetup');
                request.send();
            }
        }
    }
</script>
