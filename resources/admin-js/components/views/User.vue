<template>
    <div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">User</h4>
				</div>
			</div>
		</div>
		<div class="text-center mt-2" v-if="!info">
			<div class="spinner-border m-2" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
        <div v-else class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mt-3">
							<img :src="info.user.avatar" alt="" class="avatar-lg rounded-circle">
                            <h5 class="mt-2 mb-0">{{ info.user.name }}</h5>
                            <template v-if="info.user.name_history.length > 1">
                                <h6 class="font-weight-normal mt-2 mb-0">Also known as:</h6>
                                <h6 class="text-muted font-weight-normal" v-for="history in info.user.name_history">
                                    {{ new Date(history['time']).toLocaleString() }} - {{ history['name'] }}
                                </h6>
                            </template>

                            <button type="button" :class="`btn ${ info.user.ban ? 'btn-primary' : 'btn-danger' } btn-sm mr-1 mt-1`" @click="ban(info.user._id)">
                                {{ info.user.ban ? 'Unban' : 'Ban' }}
                            </button>
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <h4 class="mb-3 font-size-15">Info</h4>
                            <div class="table-responsive">
                                <table id="userinfo" class="table table-borderless mb-0 text-muted">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Games</th>
                                            <td>{{ info.games }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Netto Deposit & Withdraw</th>
                                            <td v-if="txstats">{{ (txstats.deposit_total - txstats.withdraw_total).toFixed(2) }}$</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Register IP</th>
                                            <td>{{ info.user.register_ip }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Latest login IP</th>
                                            <td>{{ info.user.login_ip }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Free Spins</th>
                                            <td>{{ info.user.freespins }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Created at</th>
                                            <th>{{ new Date(info.user.created_at).toLocaleString() }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Last activity</th>
                                            <th>{{ new Date(info.user.latest_activity).toLocaleString() }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Referrer</th>
                                            <th v-html="!info.user.referral ? '-' : '<a href=\'/admin/user/'+info.user.referral+'\'></a>'">View profile</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">2FA status</th>
                                            <th>{{ info.user.tfa_enabled ? 'Enabled' : 'Disabled' }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Access Level</th>
                                            <th>
                                                <select id="access" v-model="userAccess" @change="changeAccess">
                                                    <option value="user">User</option>
                                                    <option value="moderator">Moderator</option>
                                                    <option value="admin">Administrator</option>
                                                </select>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Accounts</th>
                                            <th>
                                                <multiaccounts :id="info.user._id"></multiaccounts>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <table id="userstat" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Games</th>
                                    <th>Wins</th>
                                    <th>Losses</th>
                                    <th>Wagered</th>
                                    <th>Deposited</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ info.games }}</td>
                                    <td>{{ info.wins }}</td>
                                    <td>{{ info.losses }}</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
                                </tr>
                                <tr v-for="currency in currencies" v-if="currency.name">
                                    <td>{{ currency.name }}</td>
                                    <td>{{ info.currencies[currency.id].games }}</td>
                                    <td>{{ info.currencies[currency.id].wins }}</td>
                                    <td>{{ info.currencies[currency.id].losses }}</td>
                                    <td>{{ info.currencies[currency.id].wagered.toFixed(currency.id.startsWith('local_') ? 2 : 8) }} {{ currency.name }}</td>
                                    <td>{{ info.currencies[currency.id].deposited.toFixed(currency.id.startsWith('local_') ? 2 : 8) }} {{ currency.name }}</td>
                                    <td><input class="form-control form-control-sm" :placeholder="currency.name" :value="info.currencies[currency.id].balance.toFixed(currency.id.startsWith('local_') ? 2 : 8)" @input="changeBalance(currency.walletId, $event.target.value)"></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
						<h5>General statistics</h5>
						<br>
                        <table id="txstats1" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Promocode Gain ($)</th>
                                    <th>Weekly Bonus</th>
                                    <th>Freespins Used</th>
                                    <th>Faucet Gain</th>
                                    <th>Challenges Gain</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ txstats.depositbonus }}</td>
                                    <td>{{ txstats.deposit_total }}</td>
                                    <td>{{ txstats.deposit_count }}</td>
                                    <td>{{ txstats.withdraw_count }}</td>
                                    <td>{{ txstats.withdraw_total }}</td>
                                </tr>
                            </tbody>
                        </table>     
                        <table id="txstats2" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Deposit Bonus</th>
                                    <th>Deposits (Total $)</th>
                                    <th>Deposits (Total Times)</th>
                                    <th>Withdraws (Total $)</th>
                                    <th>Withdraws (Total Times)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ txstats.depositbonus }}</td>
                                    <td>{{ txstats.deposit_total }}</td>
                                    <td>{{ txstats.deposit_count }}</td>
                                    <td>{{ txstats.withdraw_count }}</td>
                                    <td>{{ txstats.withdraw_total }}</td>
                                </tr>
                            </tbody>
                        </table>  						
                        <hr>
                    </div>
                </div>
            </div>
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
						<h5>Games</h5>
						<br>
							<table id="games" class="table dt-responsive nowrap table-responsive">
								<thead>
									<tr>
										<th>Game</th>
										<th>Date</th>
										<th>Wager</th>
										<th>Income</th>
										<th>Status</th>
										<th>Data</th>
										<th>Link</th>
									</tr>
								</thead>
							</table>
							<hr>
							<h5>Transactions</h5>
							<br>
							<table id="transactions" class="table dt-responsive nowrap table-responsive">
								<thead>
									<tr>
										<th>Date</th>
										<th style="width: 80%">Transaction</th>
									</tr>
								</thead>
							</table>
							<h5>Deposits</h5>
							<br>
							<table id="deposits" class="table dt-responsive nowrap table-responsive">
								<thead>
									<tr>
										<th>Date</th>
										<th>Amount</th>
										<th>Currency</th>
										<th>Ledger</th>
									</tr>
								</thead>
							</table>
							<h5>Withdrawals</h5>
							<br>
							<table id="withdrawals" class="table dt-responsive nowrap table-responsive">
								<thead>
									<tr>
										<th>Date</th>
										<th>Amount</th>
										<th>Currency</th>
										<th>Withdraw Mode</th>
										<th>Ledger</th>
									</tr>
								</thead>
							</table>
					</div>
				</div>
			</div>
        </div>
    </div>
</template>

<script>
    require('datatables.net');
    import { mapGetters } from 'vuex';
    import OverviewModal from "../modals/OverviewModal";
	import OverviewDataModal from "../modals/OverviewDataModal";

    export default {
        data() {
            return {
                info: null,
                txstats: null,
                userAccess: null
            }
        },
        computed: {
            ...mapGetters(['currencies'])
        },
        methods: {
            changeBalance(id, balance) {
                axios.post('/admin/user/balance', {
                    id: this.info.user._id,
                    balance: balance,
                    currency: id
                });
            },
            viewGame(id, game) {
				setTimeout(() => {
                        OverviewModal.methods.open(id, game);
				}, 100);
            },
			viewDataGame(game_id, game_data) {
				setTimeout(() => {
                        OverviewDataModal.methods.open(game_id, game_data);
				}, 100);
            },
            changeAccess() {
                axios.post('/admin/user/role', { id: this.info.user._id, role: this.userAccess });
            },
            ban(id) {
                axios.post('/admin/user/ban', { id: id }).then(() => this.$router.go());
            }
        },
        created() {
            axios.post('/admin/user/get', { id: this.$route.params.id }).then(({ data }) => {
                this.info = data;
                this.userAccess = this.info.user.access;

                setTimeout(() => {
                    $('#datatable').DataTable({
                        destroy: true,
                        "order": [[1, "asc"]],
                        "language": {
                            "paginate": {
                                "previous": "< ",
                                "next": " >"
                            }
                        },
                        "drawCallback": function() {
                            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                        }
                    });

                    $('#transactions').DataTable({
						"autoWidth": false,
						processing: true,
						pageLength: 10,
						serverSide: true,
						destroy: true,
						searching: false,
						ajax: "/admin/user/transactions/" + data.user._id,
						columns: [
							{ data: 'created_at',
								render: function ( data, type, row ) {
									return new Date(data).toLocaleString();
								}
							},
							{
								"render": function (data, type, row, meta) {
									return "<div>Message: "+(row.data.message ? row.data.message : `-`)+"</div><div>Game: "+(row.data.game ? row.data.game : `-`)+"</div><div>Amount: "+row.amount.toFixed(row.currency.startsWith(`local_`) ? 2 : 8)+" "+currencies[row.currency].name+" (Before: "+row.old.toFixed(row.currency.startsWith(`local_`) ? 2 : 8)+", Now: "+row.new.toFixed(row.currency.startsWith(`local_`) ? 2 : 8)+")</div>"; 
								}
							}
						 ],
						"language": {
							"paginate": {
								"previous": "< ",
								"next": " >"
							},
							"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
						},
						"drawCallback": function () {
							$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
						}
                    });
					
					$('#games').DataTable({
						"autoWidth": false,
						processing: true,
						pageLength: 10,
						serverSide: true,
						destroy: true,
						ajax: "/admin/user/games/" + data.user._id,
						columns: [
							{ data: 'game' },
							{ data: 'created_at',
								render: function ( data, type, row ) {
									return new Date(data).toLocaleString();
								}
							},
							{ data: 'wager',
								render: function ( data, type, row ) {
									return data.toFixed(row.currency.startsWith('local_') ? 2 : 8) + ' ' + currencies[row.currency].name + '<br>' + '$' + (data * currencies[row.currency].price).toFixed(2);
								}
							},
							{ data: 'profit',
								render: function ( data, type, row ) {
									return data.toFixed(row.currency.startsWith('local_') ? 2 : 8) + ' ' + currencies[row.currency].name + '<br>' + '$' + (data * currencies[row.currency].price).toFixed(2);
								}
							},
							{ data: 'status',
								render: function ( data, type, row ) {
									return data;
								}
							},
							{
								"render": function (data, type, row, meta) {
									if(row.data.length !== 0) {
										return "<a href='javascript:void(0)' onclick='viewDataGame(`"+row._id+"`, `"+JSON.stringify(row.data)+"`)'>View Data</a>"; 
									} else {
										return "<a href='javascript:void(0)' onclick='viewDataGame(`"+row._id+"`, `[]`)'>View Data</a>"; 
									}
								}
					 
							},
							{
								"render": function (data, type, row, meta) {
									if(row.status !== 'in-progress' && row.status !== 'cancelled') {
										return "<a href='javascript:void(0)' onclick='viewGame(`"+row._id+"`, `"+row.game+"`)'>View</a>"; 
									}
								}
					 
							}
						 ],
						"language": {
							"paginate": {
								"previous": "< ",
								"next": " >"
							},
							"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
						},
						"drawCallback": function () {
							$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
						}
					});
					
					$('#deposits').DataTable({
						"autoWidth": false,
						"order": [[ 0, "desc" ]], 
						processing: true,
						pageLength: 10,
						serverSide: true,
						destroy: true,
						ajax: "/admin/user/deposits/" + data.user._id,
						columns: [
							{ data: 'updated_at',
								render: function ( data, type, row ) {
									return new Date(data).toLocaleString();
								}
							},
							{ data: 'amount',
								render: function ( data, type, row ) {
									return parseFloat(row.sum).toFixed(row.currency.startsWith('local_') ? 2 : 8) + '<br>' + '$' + parseFloat(row.usd).toFixed(2);
								}
							},
							{ data: 'currency',
								render: function ( data, type, row ) {
									if(currencies[data] === undefined){
										return data;
									} else {
										return currencies[data].name;
									}
								}
							},
							{ data: 'ledger',
								render: function ( data, type, row ) {
									return data;
								}
							}
						 ],
						"language": {
							"paginate": {
								"previous": "< ",
								"next": " >"
							},
							"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
						},
						"drawCallback": function () {
							$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
						}
					});
					$('#withdrawals').DataTable({
						"autoWidth": false,
						"order": [[ 0, "desc" ]], 
						processing: true,
						pageLength: 10,
						serverSide: true,
						destroy: true,
						ajax: "/admin/user/withdrawals/" + data.user._id,
						columns: [
							{ data: 'updated_at',
								render: function ( data, type, row ) {
									return new Date(data).toLocaleString();
								}
							},
							{ data: 'sum',
								render: function ( data, type, row ) {
									return parseFloat(row.sum).toFixed(row.currency.startsWith('local_') ? 2 : 8) + '<br>' + '$' + parseFloat(row.usd).toFixed(2);
								}
							},
							{ data: 'currency',
								render: function ( data, type, row ) {
									return currencies[data].name;
								}
							},
							{ data: 'withdraw_method',
								render: function ( data, type, row ) {
									return data;
								}
							},
							{ data: 'address',
								render: function ( data, type, row ) {
									return data;
								}
							}
						 ],
						"language": {
							"paginate": {
								"previous": "< ",
								"next": " >"
							},
							"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
						},
						"drawCallback": function () {
							$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
						}
					});
					
					$('#txstats1').DataTable({searching: false, paging: false});
					$('#txstats2').DataTable({searching: false, paging: false});
					$('#userstat').DataTable({searching: false, paging: false});
                }, 100);
            });
            axios.post('/admin/txstats', { id: this.$route.params.id }).then(({ data }) => {
					this.txstats = data;
			});
			window.viewDataGame = this.viewDataGame;
			window.viewGame = this.viewGame;
			window.currencies = this.currencies;
        }
    }
</script>

<style lang="scss">

	#datatable_wrapper, #games_wrapper, #transactions_wrapper, #txstats1_wrapper, #txstats2_wrapper, #userstat_wrapper, #deposits_wrapper, #withdrawals_wrapper
	{
		overflow:auto;
	}

    #transactions {
        color: #6b768d !important;
    }
</style>
