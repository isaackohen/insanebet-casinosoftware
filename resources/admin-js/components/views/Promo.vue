<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Promocodes</h4>
					<div class="page-title-right">
						<div class="float-sm-end mt-3 mt-sm-0">
							<div class="row g-2">
								<div class="col-md-auto">
									<div class="btn-group">
										<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">Create</button>
									</div>
								</div>
								<div class="col-md-auto">
									<div class="btn-group">
										<button class="btn btn-danger" onclick="axios.post('/admin/promocode/remove_inactive').then(() => window.location.reload())">Cleanup</button>
									</div>
								</div>
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="code in promo">
                <div class="card">
                    <div class="card-body" :set="data = getData(code)">
                        <div :class="`badge bg-danger float-end`"><span v-if="code.currency !== 'freespins'">{{ code.sum.toFixed(2) }}$ {{ currencies[code.currency].name }}</span><span v-else>{{ code.sum.toFixed(0) }} FREESPINS</span></div>
                        <h4><a href="javascript:void(0)" class="text-dark">{{ code.code }}</a></h4>
                        <div class="text-muted mb-4">
                            <div>Created: {{ new Date(code.created_at).toLocaleString() }}</div>
                            <div>Usages: {{ code.times_used }}<span v-if="code.usages >= 0">/{{ code.usages }}</span></div>
                            <div>
                                <div v-if="+new Date(code.expires) !== carbonMinValue">Expires: {{ new Date(code.expires).toLocaleString() }}</div>
                                <div v-else>Never expires</div>
                            </div>
                            <div v-if="code.vip">VIP promocode</div>
                        </div>
                    </div>
                    <div class="card-body border-top">
                        <div class="row align-items-center">
                            <div class="col-sm-auto">
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item pr-2">
                                        <a @click="remove(code._id)" href="javascript:void(0)" class="text-muted d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove">
											<unicon name="trash-alt" fill="#6b768d" /> Remove
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col">
                                <div class="progress" style="height: 5px;" data-toggle="tooltip" data-placement="top" title="" :data-original-title="`${data.percent}%`">
                                    <div :class="`progress-bar bg-${data.color}`" role="progressbar" :style="`width: ${data.percent}%;`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="create" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header p-2 border-bottom-0 d-block">
                        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-hidden="true"></button>
                        <h5 class="modal-title">Create Promocode</h5>
                    </div>
                    <div class="modal-body p-2">
                        <form class="needs-validation" name="event-form" id="form-event" novalidate="">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label">Code</label>
                                        <input class="form-control" placeholder="Code" type="text" id="code" v-model="code">
                                        <small><a href="javascript:void(0)" @click="code = '%random%'">Random</a></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Max. usages</label>
                                        <input class="form-control" placeholder="Max. usages" type="text" id="usages" v-model="usages">
                                        <small><a href="javascript:void(0)" @click="usages = '%infinite%';">Unlimited</a></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Expire Date</label>
                                        <input class="form-control flatpickr-input" placeholder="Time" type="text" id="expires" readonly>
                                        <small><a href="javascript:void(0)" onclick="$('#expires').val('%unlimited%')">Unlimited</a></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Amount (USD$ unless Free Spins selected)</label>
                                        <input class="form-control flatpickr-input" placeholder="Amount" type="text" id="sum" v-model="amount">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Credited Currency</label>
                                        <select class="form-control" id="currency">
                                            <option v-for="currency in currencies" v-if="currency.balance" :value="currency.id">{{ currency.name }}</option>
                                            <option value="freespins">Free Spins</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"></div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-light mr-1" id="close" data-bs-dismiss="modal">Close</button>
                                    <div class="btn btn-success" id="finish" @click="create">Create</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    window.flatpickr = require('flatpickr').default;

    export default {
        computed: {
            ...mapGetters(['currencies'])
        },
        mounted() {
            axios.post('/admin/promocode/get').then(({ data }) => this.promo = data);

            flatpickr('#expires', {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true
            });
        },
        data() {
            return {
                promo: [],

                amount: 0.00000000,
                usages: 0,
                code: '',

                carbonMinValue: -62135596800
            }
        },
        methods: {
            remove(id) {
                axios.post('/admin/promocode/remove', { id: id }).then(() => this.promo = this.promo.filter((e) => e._id !== id));
            },
            create() {
                axios.post('/admin/promocode/create', {
                    code: this.code,
                    usages: this.usages,
                    expires: $('#expires').val(),
                    sum: this.amount,
                    currency: $('#currency').val()
                }).then(() => this.$router.go()).catch(() => this.$toast.error('Error'));
            },
            getData(code) {
                let color = 'success', percent = 100;
                if(code.usages === code.times_used || (+new Date(code.expires) !== this.carbonMinValue && +new Date(code.expires) < +new Date())) {
                    percent = 100;
                    color = 'danger';
                } else {
                    if(code.usages !== -1) percent = (code.times_used & code.usages) * 100;
                    else if(+new Date(code.expires) !== this.carbonMinValue) percent = (+new Date() / +new Date(code.expires)) * 100;
                }

                return {
                    color: color,
                    percent: percent
                };
            }
        }
    }
</script>
<style lang="scss">
    .form-group {
        margin-top: 6px;
        margin-bottom: 3px;
    }
    .modal-title {
        letter-spacing: .1em;
        text-transform: uppercase;
    }

</style>