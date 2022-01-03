<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        methods: {
                open() {
                    Bus.$emit('modal:new', {
                    name: 'promocode',
                    component: {
                        mounted() {
                            this.$store.dispatch('update'); 
                        },
                        computed: {
                            ...mapGetters(['user', 'currency', 'currencies', 'usd'])
                        },
                        methods: {
                            collect() {
                                axios.post('/api/promocode/rakeback').then(() => Bus.$emit('modal:close'));
                            },
							promocode() {
								$('#activate').on('click', () => {
									if($('#activate').hasClass('disabled')) return;
									$('#activate').addClass('disabled');

									axios.post('/api/promocode/activate', { code: $('#code').val() }).then(() => {
										$('#activate').removeClass('disabled');
										this.$toast.success(this.$i18n.t('bonus.promo.success'));
									}).catch((code) => {
										if(code.response.data.code === 1) this.$toast.error(this.$i18n.t('bonus.promo.invalid'));
										if(code.response.data.code === 2) this.$toast.error(this.$i18n.t('bonus.promo.expired_time'));
										if(code.response.data.code === 3) this.$toast.error(this.$i18n.t('bonus.promo.expired_usages'));
										if(code.response.data.code === 4) this.$toast.error(this.$i18n.t('bonus.promo.used'));
										if(code.response.data.code === 5) this.$toast.error(this.$i18n.t('general.error.promo_limit'));
										if(code.response.data.code === 7) this.$toast.error(this.$i18n.t('general.error.vip_only_promocode'));
										if(code.response.data.code === 8) this.$toast.error(this.$i18n.t('general.error.promo_balance_limit'));

										$('#activate').removeClass('disabled');
									});
								});
							} 
                        },
                        template: `
                            <div>
								<div class="bonus-side-menu-container">
									<div class="header text-center">
										<div class="title">{{ $t('bonus.promo.title') }}</div>
										<div class="description">{{ $t('bonus.promo.description') }}</div>
									</div>
									<div class="bonusContent">
										<div class="mt-2">
											<input id="code" type="text" :placeholder="$t('bonus.promo.placeholder')">
										</div>
										<button @click="promocode()" id="activate" class="btn btn-primary mt-2">{{ $t('bonus.promo.activate') }}</button>
									</div>
								</div>
                            </div>
                        `
                    }
                });
            } 
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.promocode {
        width: 400px;
		
		.bonus-side-menu-container {
		    position: relative;
			display: flex;
			flex-direction: column;
			justify-content: flex-end;
			height: 100%;
			min-height: 185px;
			
			.header {
				@include themed() {
					background: t('body');
				}
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
			
			.bonusContent {
				padding-left: 30px;
				padding-right: 30px;
				display: flex;
				flex-direction: column;
			}
			
		}
		
    }
</style>
