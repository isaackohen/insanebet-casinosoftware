<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'vip',
                    component: {
                        computed: {
                            ...mapGetters(['user', 'currencies'])
                        },
                        data() {
                            return {
                                vips: {},
								userdata: [],
								percent: 0,
								loading: true
                            }
                        },
						created() {
							axios.post('/api/user/vip').then(({ data }) => {
								var vipsdata = data.vips;
								vipsdata.sort(function(a, b) {
									var keyA = parseFloat(a.level),
									keyB = parseFloat(b.level);
									if (keyA < keyB) return -1;
									if (keyA > keyB) return 1;
									return 0;
								});
								this.vips = vipsdata;
								this.userdata = data.user;
								this.loading = false;
								this.percent = ((data.user.vip_progress / data.vips[data.user.viplevel + 1].start) * 100).toFixed(2);
								setTimeout(() => {
									this.load();
								}, 600);
							});
						},
						methods: {
							load() {
								$('.expandableBlockHeader').on('click', function() {
									$(this).parent().find('.expandableBlockContent').slideToggle('fast');
									$(this).find('i:last-child').toggleClass('fa-angle-left').toggleClass('fa-angle-up');
								});
							}
						},
                        template: `
                            <div>
                                <img class="vip-logo" src="/img/misc/vip-logo.svg" alt>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" :style="{ width: percent + '%' }">{{ percent < 8 ? '' : percent + '%' }}</div>
                                </div>
								<div style="height:200px;" v-if="loading">
									<loader></loader>
								</div>
								<template v-else>
                                <div class="vipProgress">
                                    <div>
                                        {{ $t('vip.rank.'+(userdata.viplevel)) }}
                                    </div>
                                    <div>
										<img class="vipicon-list" :src="'/img/misc/vipicons/' + (parseFloat(userdata.viplevel) + 1) + '.svg'">
                                        {{ $t('vip.rank.'+(userdata.viplevel + 1)) }}
                                    </div>
                                </div>
                                <div class="vipDesc mb-2">{{ $t('vip.description', { currency: currencies.vipClosest.toUpperCase() }) }}</div>
                                <div class="font-weight-bold" style="font-size: 1.05em">{{ $t('vip.benefits') }}</div>
                                <div class="vipDesc">{{ $t('vip.benefits_description') }}</div>
                                <div v-for="(vip, index) in vips" v-if="index > 0" class="expandableBlock">
                                    <div class="expandableBlockHeader">
                                        <img class="vipicon-list" :src="'/img/misc/vipicons/' + (userdata.viplevel + index) + '.svg'">
                                        {{ $t('vip.rank.'+(index)) }}
                                        <i class="fas fa-angle-left"></i>
                                    </div>
                                    <div class="expandableBlockContent" style="display: none;">
                                       <span> 
											<div v-if="vip.rake_percent > 0">
											{{ $t('vip.benefit_list.1', { text: vip.rake_percent }) }}
											</div>
											<div v-if="vip.promocode_bonus > 0">
											{{ $t('vip.benefit_list.2', { text: vip.promocode_bonus }) }}
											</div>
											<div v-if="vip.faucet_bonus > 0">
											{{ $t('vip.benefit_list.3', { text: vip.faucet_bonus }) }}
											</div>
											<div v-if="vip.fs_bonus > 0">
											{{ $t('vip.benefit_list.4', { text: vip.fs_bonus }) }}
											</div>
											<div v-if="vip.challenges_bonus > 0">
											{{ $t('vip.benefit_list.5', { text: vip.challenges_bonus }) }}
											</div>
									   </span>
                                    </div>
                                </div>
								</template>
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
    .xmodal {
        max-height: 80vh!important;
    }

    .vipicon-list {
        width: 14px;
        height: 14px;
        margin-right: 10px;
        img {
            width: 14px;
            height: 14px;
        }
    }

    .xmodal.vip {
        width: 550px;

        .vipTree {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            margin-bottom: -37px;
            padding-top: 30px;

            img {
                width: 100px;
                position: relative;
                margin-left: -10px;

                &:nth-child(1) {
                    margin-left: 0;
                }

                &:nth-child(2) {
                    margin-top: -40px;
                    z-index: 4;
                }

                &:nth-child(3) {
                    margin-top: -75px;
                    z-index: 5;
                }

                &:nth-child(4) {
                    margin-top: -40px;
                    z-index: 4;
                }
            }
        }

        .vip-logo {
            width: 40%;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            margin-top: 25px;
        }

        .vipDesc {
            @include themed() {
                color: rgba(t('text'), 0.8);
                font-size: 0.9em;
            }
        }

        .progress {
            height: 14px;
            border-radius: 3px;
            margin: 2.5em 0 0;

            @include themed() {
                background: darken(t('sidebar'), 3%);

                .progress-bar {
                    height: 14px;
                    background: t('secondary');
                    font-size: 0.65em;
                }
            }
        }

        .vipProgress {
            display: flex;
            flex-direction: row;
            margin-top: 10px;
            margin-bottom: 2.5em;

            div {
                display: inline-flex;
                position: relative;
                svg {
                    width: 14px;
                    height: 14px;
                    position: absolute;
                    margin-left: -20px;
                    top: 50%;
                    transform: translateY(-50%);
                }
            }

            div:last-child {
                margin-left: auto;
            }
        }

        .expandableBlock {
            margin-top: 15px;

            @include themed() {
                .expandableBlockHeader {
                    background: darken(t('sidebar'), 0.5%);
                    border-radius: 0.25rem;
                    padding: 0.75rem 1.5rem;
                    font-size: 0.85em;
                    display: flex;
                    align-content: center;
                    cursor: pointer;

                    svg {
                        width: 14px;
                        height: 14px;
                        margin-right: 10px;
                        margin-top: 4px;
                    }

                    i {
                        position: absolute;
                        right: 35px;
                        margin-top: 5px;
                    }
                }

                .expandableBlockContent {
                    border: 1px solid darken(t('sidebar'), 1%);
                    background: darken(t('sidebar'), 1%);
                    border-top: none;
                    padding: 15px 23px;
                    opacity: 0.75;
                    transition: opacity 0.3s ease;

                    &:hover {
                        opacity: 1;
                    }

                    ul {
                        margin-bottom: 0;
                        list-style: none;
                        padding-left: 0;
                    }
                }
            }
        }
    }

    @media(max-width: 500px) {
        .xmodal.vip {
            width: 100vw !important;
            height: calc(100% - 55px);
            top: 75px;
            max-height: unset !important;
            margin: unset;
            filter: unset;
        }
    }
</style>
