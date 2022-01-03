<template>
    <div>
        <div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="page-title-box">
						<h4 class="page-title">Withdraws & Payments</h4>
						<div class="page-title-right">
							<form class="float-sm-end mt-3 mt-sm-0">
								<div class="row g-2">
									<div class="col-md-auto">
										<div class="btn-group">
											<router-link tag="button" to="/admin/wallet_ignored" class="btn btn-danger">Suspicious Queue</router-link>
										</div>
									</div>
								</div> 
							</form>
						</div>
					</div>
				</div>
			</div>
        </div>
        <div class="row" v-if="!loading">
			<!-- start col -->	
			<div v-if="withdraws" class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <template v-if="withdraws.withdraws.length === 0">
							<div class="text-center">
								<unicon name="clock" fill="#4b5970" />
							</div>
                            <div class="text-center">Nothing here</div>
                        </template>
                        <div class="row" v-else>
                            <div v-for="withdraw in withdraws.withdraws" :class="`col-sm-6 col-md-6 col-lg-4 ${withdraw.user.vipLevel === 5 ? 'order-1' : 'order-2'}`">
                                <div class="card" :style="withdraw.user.vipLevel === 5 ? 'border: 1px solid #00fffb' : ''">
                                    <div class="card-body p-3">
                                        <div class="d-flex">
                                            <img :src="withdraw.user.avatar" class="me-3 avatar-lg rounded">
                                            <div class="media-body">
                                                <button class="btn btn-danger btn-sm" @click="ignoreWithdraw(withdraw.withdraw._id)" style="position: absolute; right: 15px;">-</button>
                                                <h5 class="mt-1 mb-0">{{ withdraw.user.name }}</h5>
                                                <h6 class="font-weight-normal mt-1 mb-1">
                                                    <router-link :to="`/admin/user/${withdraw.user._id}`">View profile</router-link>
                                                </h6>
                                                <p class="text-muted">
                                                    <strong>Balance:</strong>
                                                    <br>
                                                    {{ withdraw.user.balance.toFixed(withdraw.withdraw.currency.startsWith('local_') ? 2 : 8) }}
                                                    {{ currencies[withdraw.withdraw.currency].name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row mt-2 border-top pt-2">
                                            <div class="col-12">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h5 class="mt-2 pt-1 mb-0 font-size-16">Withdraw</h5>
                                                        <h6 class="font-weight-normal mt-0">
                                                            {{ withdraw.withdraw.sum }}
                                                            {{ currencies[withdraw.withdraw.currency].name }}
                                                            {{ new Date(withdraw.withdraw.created_at).toLocaleString() }}
                                                            {{ withdraw.withdraw.withdraw_meta }}
                                                        </h6>
                                                        <h6 class="font-weight-normal mt-1" v-if="withdraw.withdraw.type">{{ withdraw.withdraw.type }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h6 class="mt-2 pt-1 mb-0 font-size-16"><strong>Address</strong></h6>
                                                        <h7 class="font-weight-normal mt-0">
                                                            {{ currencies[withdraw.withdraw.currency].name }} | {{ withdraw.withdraw.address }} <br>
                                                            Bank Name: {{ withdraw.withdraw.bankaccount_name }} | IFSC: {{ withdraw.withdraw.bankaccount_ifsc }}
                                                        </h7>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <multiaccounts :id="withdraw.user._id"></multiaccounts>
                                            </div>
                                        </div>
                                        <div class="row mt-3 text-center">
                                            <div class="col">
                                                <button @click="acceptWithdraw(withdraw.withdraw._id)" type="button" class="btn btn-primary btn-sm btn-block mr-1">Mark as accepted</button>
                                            </div>
                                            <div class="col">
                                                <button @click="declineWithdraw(withdraw.withdraw._id)" type="button" class="btn btn-white btn-sm btn-block">Decline</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
			<!-- start col -->	
			<div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body">
						<h5>Deposits</h5>
                        <table id="deposits" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
									<th>Amount</th>
									<th>Currency</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end col -->
			<!-- start col -->	
			<div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body">
						<h5>Withdrawals</h5>
                        <table id="withdrawals" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
									<th>Amount</th>
									<th>Currency</th>
									<th>Mode</th>
									<th>Address</th>
									<th>Bank Account Name</th>
									<th>Bank IFSC</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
		<div v-else class="text-center mt-2">
			<div class="spinner-border m-2" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                loading: true,
				withdraws: null
            }
        },
        computed: {
            ...mapGetters(['currencies'])
        },
        created() {
            axios.post('/admin/wallet/info').then(({ data }) => {
				this.withdraws = data;
				setTimeout(() => {
					$('#deposits').DataTable({
						"autoWidth": false,
						"order": [[ 0, "desc" ]], 
						processing: true,
						pageLength: 10,
						serverSide: true,
						destroy: true,
						ajax: "/admin/wallet/deposits",
						columns: [
							{ data: 'updated_at',
								render: function ( data, type, row ) {
									return new Date(data).toLocaleString();
								}
							},
							{ data: 'user',
								"render": function (data, type, row, meta) {
									return "<a href='/admin/user/"+row.user+"'>"+row.username+"</a>"; 
								}
							},
							{ data: 'amount',
								render: function ( data, type, row ) {
									return parseFloat(row.sum).toFixed(row.currency.startsWith('local_') ? 2 : 8) + '<br>' + '₹' + parseFloat(row.usd).toFixed(2);
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
						ajax: "/admin/wallet/withdrawals",
						columns: [
							{ data: 'updated_at',
								render: function ( data, type, row ) {
									return new Date(data).toLocaleString();
								}
							},
							{ data: 'user',
								"render": function (data, type, row, meta) {
									return "<a href='/admin/user/"+row.user+"'>"+row.username+"</a>"; 
								}
							},
							{ data: 'sum',
								render: function ( data, type, row ) {
									return parseFloat(row.sum).toFixed(row.currency.startsWith('local_') ? 2 : 8) + '<br>' + '₹' + parseFloat(row.usd).toFixed(2);
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
							},
							{ data: 'bankaccount_name',
								render: function ( data, type, row ) {
									return data;
								}
							},
							{ data: 'bankaccount_ifsc',
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
				}, 100);
				this.loading = false;
				window.currencies = this.currencies;
			});
        }, 
        methods: {
            ignoreWithdraw(id) {
                axios.post('/admin/wallet/ignore', { id: id }).then(() => this.$router.go());
            },
            acceptWithdraw(id) {
                axios.post('/admin/wallet/accept', { id: id }).then(() => this.$router.go());
            },
            declineWithdraw(id) {
                axios.post('/admin/wallet/decline', { id: id, reason: prompt('Decline reason') }).then(() => this.$router.go());
            }
        }
    }
</script>

<style lang="scss">

	#deposits_wrapper, #withdrawals_wrapper
	{
		overflow:auto;
	}

</style>