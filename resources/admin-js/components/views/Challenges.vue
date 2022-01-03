<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Challenges</h4>
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
										<button class="btn btn-danger" onclick="axios.post('/admin/challenges/remove_inactive').then(() => window.location.reload())">Cleanup</button>
									</div>
								</div>
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="challenge in challenges">
                <div class="card">
                    <div class="card-body" :set="data = getData(challenge)">
                        <div :class="`badge bg-danger float-end`">{{ challenge.sum.toFixed(2) }}$ {{ currencies[challenge.currency].name }}</div>
                        <h4><a href="javascript:void(0)" class="text-dark">{{ challenge.game_name }}</a></h4>
                        <div class="text-muted mb-4">
                            <div>Multiplier Goal: {{ challenge.multiplier }}</div>
                            <div>Min. Bet ($): {{ challenge.minbet }}$</div>
                            <div>Reward ($): {{ challenge.sum.toFixed(2) }}$ {{ currencies[challenge.currency].name }}</div>
                            <div>Created: {{ new Date(challenge.created_at).toLocaleString() }}</div>
                            <div>Max Winners: {{ challenge.expired }}<span v-if="challenge.maxwinners >= 0">/{{ challenge.maxwinners }}</span></div>
                            <div>
                                <div v-if="+new Date(challenge.expires) !== carbonMinValue">Expires: {{ new Date(challenge.expires).toLocaleString() }}</div>
                                <div v-else>Never expires</div>
                            </div>
                            <div v-if="challenge.vip">VIP Challenge</div>
                        </div>
                    </div>
                    <div class="card-body border-top">
                        <div class="row align-items-center">
                            <div class="col-sm-auto">
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item pr-2">
                                        <a @click="remove(challenge._id)" href="javascript:void(0)" class="text-muted d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove">
                                            <unicon name="trash-alt" fill="#6b768d"></unicon> Remove
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
                        <h5 class="modal-title">Create Challenge</h5>
                    </div>
                    <div class="modal-body p-2">
                        <form class="needs-validation" name="event-form" id="form-event" novalidate="">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label">Game</label>
                                        <input class="form-control" placeholder="Game ID (get this from gamelist)" type="text" id="game" v-model="game">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Multiplier Goal</label>
                                        <input class="form-control" placeholder="Multiplier Goal" type="text" id="multiplier" v-model="multiplier">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Max. winners</label>
                                        <input class="form-control" placeholder="Max. winners" type="text" id="maxwinners" v-model="maxwinners">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Expire Date</label>
                                        <input class="form-control flatpickr-input" placeholder="Expiration Date" type="text" id="expires" readonly>
                                        <small><a href="javascript:void(0)" onclick="$('#expires').val('%unlimited%')">Expires Never (only when won)</a></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Min. bet in USD</label>
                                        <input class="form-control flatpickr-input" placeholder="Min. bet in USD" type="text" id="minbet" v-model="minbet">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Reward in USD</label>
                                        <input class="form-control flatpickr-input" placeholder="Reward in USD" type="text" id="sum" v-model="amount">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Credited Currency</label>
                                        <select class="form-control" id="currency">
                                            <option v-for="currency in currencies" v-if="currency.balance" :value="currency.id">{{ currency.name }}</option>
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
            axios.post('/admin/challenges/get').then(({ data }) => this.challenges = data);

            flatpickr('#expires', {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true
            });
        },
        data() {
            return {
                challenges: [],
                amount: 0.00000000,
                maxwinners: 0,
                challenge: '',
                carbonMinValue: -62135596800
            }
        },
        methods: {
            remove(id) {
                axios.post('/admin/challenges/remove', { id: id }).then(() => this.challenges = this.challenges.filter((e) => e._id !== id));
            },
            create() {
                axios.post('/admin/challenges/create', {
                    game: this.game,
                    multiplier: this.multiplier,
                    maxwinners: this.maxwinners,
                    minbet: this.minbet,
                    multiplier: this.multiplier,
                    expires: $('#expires').val(),
                    sum: this.amount,
                    currency: $('#currency').val()
                }).then(() => this.$router.go()).catch(() => this.$toast.error('Error'));
            },
            getData(challenge) {
                let color = 'success', percent = 100;
                if(challenge.maxwinners === challenge.expired || (+new Date(challenge.expires) !== this.carbonMinValue && +new Date(challenge.expires) < +new Date())) {
                    percent = 100;
                    color = 'danger';
                } else {
                    if(challenge.maxwinners !== -1) percent = (challenge.expired & challenge.maxwinners) * 100;
                    else if(+new Date(challenge.expires) !== this.carbonMinValue) percent = (+new Date() / +new Date(challenge.expires)) * 100;
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