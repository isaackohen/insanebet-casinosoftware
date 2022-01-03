<template>
    <div>
        <div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="page-title-box">
						<h4 class="page-title">Ignored Withdraws</h4>
						<div class="page-title-right">
							<form class="float-sm-end mt-3 mt-sm-0">
								<div class="row g-2">
									<div class="col-md-auto">
										<div class="btn-group">
											<router-link tag="button" to="/admin/wallet" class="btn btn-danger">Back</router-link>
										</div>
									</div>
								</div> 
							</form>
						</div>
					</div>
				</div>
			</div>
        </div>
        <div class="row" v-if="withdraws">
            <div class="col-12">
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
                                                <button class="btn btn-primary btn-sm" @click="unignoreWithdraw(withdraw.withdraw._id)" style="position: absolute; right: 15px;">-</button>
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
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h5 class="mt-2 pt-1 mb-0 font-size-16">Address</h5>
                                                        <h6 class="font-weight-normal mt-0">
                                                            <strong>{{ currencies[withdraw.withdraw.currency].name }}</strong> {{ withdraw.withdraw.address }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <multiaccounts :id="withdraw.user._id"></multiaccounts>
                                            </div>
                                        </div>
                                        <div class="row mt-3 text-center">
                                            <div class="col">
                                                <button @click="unignoreWithdraw(withdraw.withdraw._id)" type="button" class="btn btn-white btn-sm btn-block">Remove from this list</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        data() {
            return {
                withdraws: null
            }
        },
        computed: {
            ...mapGetters(['currencies'])
        },
        created() {
            axios.post('/admin/wallet/infoIgnored').then(({ data }) => this.withdraws = data);
        },
        methods: {
            unignoreWithdraw(id) {
                axios.post('/admin/wallet/unignore', { id: id }).then(() => this.$router.go());
            }
        }
    }
</script>
