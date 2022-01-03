<template>
    <div class="wager-classic wager-selector">
        <input type="text" v-model.lazy="bet" v-money="money" :placeholder="$t('general.wager')" :disabled="gameInstance.game && gameInstance.game.extendedState === 'in-progress'">
        <div class="wager-input-controls">
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) / 2).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))"><i class="fas fa-slash"></i></div>
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) * 2).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))"><i class="fas fa-asterisk"></i></div>
        </div>
        <div class="wager-controls">
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) + (usd ? 0.01 : (currency.startsWith('local_') ? 0.01 : 0.001))).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))">+{{ usd ? 0.01 : (currency.startsWith('local_') ? 0.01 : 0.001) }}</div>
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) + (usd ? 0.50 : (currency.startsWith('local_') ? 0.50 : 0.10))).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))">+{{ usd ? 0.50 : (currency.startsWith('local_') ? 0.50 : 0.10) }}</div>
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) + (usd ? 5.00 : (currency.startsWith('local_') ? 5.00 : 0.25))).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))">+{{ usd ? 5.00 : (currency.startsWith('local_') ? 5.00 : 0.25) }}</div>
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) + (usd ? 50.00 : (currency.startsWith('local_') ? 50.00 : 0.50))).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))">+{{ usd ? 50.00 : (currency.startsWith('local_') ? 50.00 : 0.50) }}</div>
            <div class="control" @click="gameInstance.game && gameInstance.game.extendedState === 'in-progress' ? null : bet = (parseFloat(bet) + (usd ? 100.00 : (currency.startsWith('local_') ? 100.00 : 1.00))).toFixed(usd ? 2 : (currency.startsWith('local_') ? 2 : 8))">+{{ usd ? 100.00 : (currency.startsWith('local_') ? 100.00 : 1.00) }}</div>
        </div>
    </div>
</template>

<script>
    import Bus from '../../../bus';
    import { mapGetters } from 'vuex';

    export default {
        props: {
            data: {
                type: Object
            }
        },
        watch: {
            bet() {
                const instance = this.gameInstance;
                instance.bet = parseFloat(this.bet);
                this.$store.dispatch('setGameInstance', instance);

                Bus.$emit('sidebar:update', { type: 'bet', value: this.bet });
            },
            currency() {
                this.setPrecision();
            },
			usd() {
                this.setPrecision();
            }
        },
        data() {
            return {
                bet: 0.00000000,
                money: {
                    decimal: '.',
                    thousands: '',
                    prefix: '',
                    suffix: '',
                    precision: 8,
                    masked: false
                }
            }
        },
        computed: {
            ...mapGetters(['isGuest', 'gameInstance', 'currency', 'usd', 'currencies'])
        },
        methods: {
            setPrecision() {
                this.bet = this.usd ? ((this.currencies[this.currency].min_bet * this.currencies[this.currency].price) * 1.0005).toFixed(2) : (this.currency.startsWith('local_') ? ((this.currencies[this.currency].min_bet * this.currencies[this.currency].price) * 1.0005).toFixed(2) : (this.currencies[this.currency].min_bet).toFixed(8));
                this.money.precision = this.usd ? 2 : (this.currency.startsWith('local_') ? 2 : 8);
            }
        },
        mounted() {
            this.setPrecision();
        }
    }
</script>
