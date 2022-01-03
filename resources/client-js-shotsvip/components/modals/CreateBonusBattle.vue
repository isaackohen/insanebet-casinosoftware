<script>
import Bus from '../../bus';
import { mapGetters } from 'vuex';

export default {
    methods: {
        open() {
            Bus.$emit('modal:new', {
                name: 'create_bonus_battle',
                title: 'bonusbattle.modal_title',
                component: {
                    data() {
                        return {
                            stake: '20',
                            stakeOptions: [
                              { text: '20$', value: '20' },
                              { text: '40$', value: '40' },
                              { text: '60$', value: '60' },
                              { text: '100$', value: '100' },
                              { text: '120$', value: '120' },
                              { text: '160$', value: '160' },
                              { text: '180$', value: '180' },
                              { text: '200$', value: '200' },
                              { text: '300$', value: '300' },
                              { text: '400$', value: '400' },
                              { text: '500$', value: '500' },
                              { text: '600$', value: '600' },
                              { text: '700$', value: '700' },
                              { text: '800$', value: '800' },
                              { text: '900$', value: '900' },
                              { text: '1000$', value: '1000' },
                              { text: '1200$', value: '1200' },
                              { text: '1800$', value: '1800' },
                              { text: '2000$', value: '2000' },
                              { text: '2400$', value: '2400' }
                            ],
                            players_max: '2',
                            playerSizes: [ 
                              { text: '2 Players', value: '2' },
                              { text: '3 Players', value: '3' },
                              { text: '4 Players', value: '4' }
                            ],

                            saving: false,
                            game: 'p0_p0-sweet-bonanza',
                            gameOptions: [
                              { text: 'Gates of Olympus', value: 'p0_p0-gates-of-olympus' },
                              { text: 'Sweet Bonanza', value: 'p0_p0-sweet-bonanza' },
                              { text: 'Sweet Bonanza Xmas', value: 'p0_p0-sweet-bonanza-xmas' },
                              { text: 'Fruit Party', value: 'p0_p0-fruit-party' },
                              { text: 'Dog House', value: 'p0_p0-the-dog-house' },
                              { text: 'Curse of Werewolf Megaways', value: 'p0_p0-curse-of-the-werewolf-megaways' },
                              
                            ]

                        }
                    },
                    computed: {
                        ...mapGetters(['user', 'currency'])
                    },
                    created() {
                    },
                    methods: {
                        save() {
                            this.saving = true;
                            axios.post('/api/bonusbuybattle/create', {
                                game: this.game,
                                players_max: this.players_max,
                                stake: this.stake, 
                                currency: this.currency
                                }).then(({ data }) => {
									Bus.$emit('modal:close');
									this.$toast.success(this.$i18n.t('bonusbattle.created_success'));
								}).catch((error) => {
                                    switch (error.response.data.code) {
                                        case 1:
                                            this.$toast.error(this.$i18n.t('bonusbattle.not_enough_balance'));
                                            break;
                                        case 2:
                                            this.$toast.error(this.$i18n.t('bonusbattle.incorrect'));
                                            break;
                                         case 3:
                                            this.$toast.error(this.$i18n.t('bonusbattle.incorrect'));
                                            break;
                                        case 4:
                                            this.$toast.error(this.$i18n.t('bonusbattle.incorrect'));
                                        break;
                                    }
                                 this.saving = false;
                                });


                        },
                        close() {
                            Bus.$emit('modal:close');
                        }
                    },
                    template: `
                        <div>
                                <div class="form-group mt-2 mb-2">
                                    <div class="label">{{ $t('bonusbattle.select_game') }}</div>
                                    <select class="form-select" v-model="game">
                                      <option v-for="option in gameOptions" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                </div>

                                <div class="form-group mt-2 mb-2">
                                    <div class="label">{{ $t('bonusbattle.player_size') }}</div>
                                    <select class="form-select" v-model="players_max">
                                      <option v-for="option in playerSizes" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                </div>

                                <div class="form-group mt-2 mb-2">
                                    <div class="label">{{ $t('bonusbattle.select_stake') }}</div>
                                    <select class="form-select" v-model="stake">
                                      <option v-for="option in stakeOptions" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                </div>

                            <div class="btn-group mt-2">
                                <button class="btn btn-primary mr-2" :disabled="saving" @click="save">Create</button>
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

    .xmodal.create_bonus_battle {
        min-width: 400px;
    }
</style>
