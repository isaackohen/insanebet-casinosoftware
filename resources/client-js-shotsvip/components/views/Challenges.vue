		<template>
            <div class="gameCategory">
			<div class="header">
				{{ $t('general.sidebar.challenges') }}
			</div>
			<div class="container-fluid" style="max-width: 1420px; margin: 0px auto;">
		        <div class="row mt-3">
		            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" v-for="challenge in challenges">
		                <div class="card">
                             <div v-if="challenge.completed === 1">
                                    <div class="unavailable">
                                        <div class="slanting">
                                            <div class="unavailableContent" v-html="$t('challenges.completed')"></div>
                                        </div>
                                    </div>
                            </div>
		                    <div class="card-body"> 
		                        <div :class="`badge bg-primaryBadge float-end`">{{ challenge.sum }}$ <icon :icon="currencies[challenge.currency].icon" :style="{ color: currencies[challenge.currency].style }"></icon></div>
		                        <h4><a href="javascript:void(0)" class="cardHeader">{{ challenge.game_name }}</a></h4>
		                        <div class="text-muted mb-4">
                       			 <img :class="`card-gameThumbnail`" @click="$router.push(`/casino/${challenge.game}`)" :src="`https://games.cdn4.dk/games${challenge.game_image}?q=99&auto=enhance&w=220&h=147&fit=crop&sharp=5&usm=5`">
                       			 	<hr>
	                     			<div class="priceHeader">Challenge</div>
		                     		<div class="reward">Get rewarded if you hit <strong>{{ challenge.multiplier }}×</strong> multiplier with a <strong>{{ challenge.minbet }}$</strong> min. bet.
		                     		<hr>
		                     		<div class="priceHeader">Reward</div>
		                     		<div class="reward">{{ challenge.sum }}$ <icon :icon="currencies[challenge.currency].icon" :style="{ color: currencies[challenge.currency].style }"></icon> <small>(+ VIP Bonus)</small></div>
		                     		<hr>
		    		                <div><small>Winners: {{ challenge.expired }}<span v-if="challenge.maxwinners >= 0">/{{ challenge.maxwinners }}</span></small></div>
		                            <div>
		                                <div v-if="+new Date(challenge.expires) !== carbonMinValue"><small>Expires: {{ new Date(challenge.expires).toLocaleString() }}</small></div>
		                                <div v-else>Never expires</div>
		                            </div>
		                            <div v-if="challenge.vip">Challenge for VIP Users</div>
		                        </div>
		                    </div>
		                    <hr>
		                    <div class="card-body">
		                        <div class="row align-items-center">
		                            <div class="col-sm-auto">
		                                <ul class="list-inline mb-0">
		                                    <li class="list-inline-item pr-2">
		                                        <button class="btn btn-primary btn-sm" @click="$router.push(`/casino/${challenge.game}`)">
		                                            Play
		                                        </button>
		                                    </li>
		                                </ul>
		                            </div>
		                            <div class="col">

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
    window.flatpickr = require('flatpickr').default;
	import Bus from '../../bus';

    export default {
        data() {
            return {
                challenges: [],
                amount: 0.00000000,
                maxwinners: 0,
                challenge: '',
                carbonMinValue: -62135596800
            }
        },
        computed: {
            ...mapGetters(['currencies'])
        },
        mounted() {
            axios.post('/api/challenges/getChallenges').then(({ data }) => this.challenges = data);

            flatpickr('#expires', {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true
            });
			
			Bus.$on('event:ChallengesNew', (e) => { 
				this.challenges.push(e.challenge)
			});
			
			Bus.$on('event:ChallengesRemove', (e) => { 
				this.challenges = this.challenges.filter((challenge) => !e.ids.includes(challenge._id))
			});
			
        },
		methods: {

        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";

	.progress {
        @include themed() {
        background-color: t('secondary');
        }
    }
	.btn.show-more {
		padding: 8px 20px;
	}
	
    .warning {
        width: 100%;
        text-align: center;
        font-size: 1.1em;
        margin-top: 15px;
        margin-bottom: 15px;

     }

	.bg-primaryBadge {
	 @include themed() {
        background-color: t('body');
        transition: all 0.3s ease;
        opacity: 0.9;
        color: t('text');
        font-size: 0.82em;
        letter-spacing: 0.1em;
        padding: 5px 12px 5px 12px;
        border-radius: 12px;

        &:hover {
        	opacity: 1;
        }
      }
	}

     .card {
        @include themed() {
        padding: 5px;
        margin: 5px;
        border-radius: 11px;
        background-color: rgba(t('sidebar'), .8);


            .unavailable {
                z-index: 4;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                @include blur(t('sidebar'), 0.7, 0.85, 10px);
                border-radius: 5px;
                .slanting {
                    transform: skewY(-5deg) translateY(-50%);
                    padding: 25px;
                    position: absolute;
                    top: 45%;
                    background: rgba(t('text'), 0.05);
                    width: 100%;
                    .unavailableContent {
                        font-size: 15px;
                        transform: skewY(5deg);
                        text-align: center;
                    }
                }
            }


        		.border-top {
        			border-top: 1px solid darken(t('text'), 2.5%) !important;
        		}

        		.priceHeader {
                	color: t('text');
                    font-weight: 500;
           		}

                .cardHeader {
                	color: t('text');
                    text-transform: uppercase;
                    font-weight: 500;
                    font-size: 0.95em;
                    letter-spacing: 0.05em;
                }

                .text-muted {
                	color: darken(t('text'), 2.5%);
                }
    	}



     }


     .card-gameThumbnail {
     	display: block;
        min-width: 100px;
        max-width: 220px;
     	border-radius: 11px;
     	object-fit: cover;
  		margin-left: auto;
  		margin-right: auto;
  		width: 60%;
  		cursor: pointer;
     }

    .gameCategory {
        @include themed() {
            .header {
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                border-bottom: 2px solid t('border');
                margin-top: -15px;
                padding: 35px 45px;
                font-size: 1.8em;
                position: sticky;
                top: 73px;
                z-index: 555;
            }
        }
    }
	
	.game_poster_external-provider {
		bottom: 15px !important;
		text-transform: capitalize;
	}
</style>
