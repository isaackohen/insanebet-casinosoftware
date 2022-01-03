<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'rakeback',
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
                            }
                        },
                        template: `
                            <div>
                                <div class="font-weight-bold mt-2" style="font-size: 1.05em">{{ $t('rakeback.title') }}</div>
                                <div class="vipDesc" v-html="$t('rakeback.description')"></div>
                                <div class="bonus-image mt-1">
                                    <div :class="'btn btn-primary mt-2 ' + (user.user.rakeback < 0.25 ? 'disabled' : '')" @click="collect()">CLAIM {{ user.user.rakeback ?? 0 }}₹</div>
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

    .xmodal.rakeback {
        width: 400px;

        .vipDesc {
            @include themed() {
                color: rgba(t('text'), 0.8);
                font-size: 0.9em;
            }
        }

        .bonus-image {
            background-image: url('/img/misc/bonus.svg');
            background-size: cover;
            position: relative;
            height: 120px;

            .btn {
                position: absolute;
                width: 50%;
                right: 10px;
                bottom: 10px;
                font-size: 0.9em;
                text-transform: uppercase;
                font-weight: 600;
            }
        }

        .progress {
            position: absolute;
            bottom: 10px;
            left: 10px;
            width: 40%;
            height: 14px;
            border-radius: 3px;

            @include themed() {
                background: darken(t('sidebar'), 3%);

                .progress-bar {
                    height: 14px;
                    background: t('secondary');
                    font-size: 0.65em;
                }
            }
        }
    }
</style>
