<script>
import Bus from '../../bus';
import { mapGetters } from 'vuex';

export default {
    methods: {
        open() {
            Bus.$emit('modal:new', {
                name: 'join_bonus_battle',
                title: 'general.head.bonusbattle_create',
                component: {
                    data() {
                        return {
                            currency: null,
                            game: null,
                            players_max: null,
                            stake: null,
                            saving: false
                        }
                    },
                    computed: {
                        ...mapGetters(['user'])
                    },
                    created() {

                    },
                    methods: {
                        save() {
                            this.saving = true;
                            axios.post('/api/bonusbuybattle/join', {
                                game: this.game,
                                players_max: this.players_max,
                                stake: this.stake,
                                currency: this.currency


                            }).then(() => {
                                Bus.$emit('modal:close');
                                this.$store.dispatch('update');
                            }).catch(() => {
                                this.saving = false;
                            });
                        },
                        close() {
                            Bus.$emit('modal:close');
                        }
                    },
                    template: `
                        <div>
                                <div class="mt-2 mb-2">
                                    Are you sure you want to join this Bonus Battle?
                                </div>

                            <div class="btn-group">
                                <button class="btn btn-primary mr-2" :disabled="saving" @click="save">Yes</button>
                                <button class="btn btn-secondary" @click="close">{{ $t('general.cancel') }}</button>
                            </div>
                        </div>`
                }
            });
        }
    }
}
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.join_bonus_battle {
        min-width: 400px;
    }
</style>
