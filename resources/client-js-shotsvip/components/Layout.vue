<template>
    <website-layout v-if="!$route.fullPath.startsWith('/admin')"></website-layout>
</template>
<style>
    .preloader {
        display: none;
    }
    
</style>
<script>
    import { mapGetters } from 'vuex';
    import Bus from "../bus";

    export default {
        created() {
            this.$store.dispatch('changeLocale', this.$store.state.locale);
            this.$store.dispatch('switchTheme', this.$store.state.theme);
            this.$store.dispatch('update');
            this.$store.dispatch('updateData');

            this.reconnectToWS();

            if(typeof URLSearchParams === 'function') {
                const params = new URLSearchParams(window.location.search);
                if(params.has('c')) this.setCookie('c', params.get('c'));
            }
        },
        computed: {
            ...mapGetters(['user', 'isGuest', 'currencies', 'currency'])
        },
        methods: {
            reconnectToWS() {
                window.Echo.connector.disconnect();

                window.Echo = new window.LaravelEcho({
                    broadcaster: 'socket.io',
                    host: `${window.location.hostname}:2091`,
                    auth: {
                        headers: {
                            Authorization: `Bearer ${this.isGuest ? null : this.user.token}`
                        }
                    }
                });

                window.Echo.connector.socket.on('connect', () => Bus.$emit('ws:connect'));
                window.Echo.connector.socket.on('disconnect', () => Bus.$emit('ws:disconnect'));

                Echo.private(`App.User.${this.isGuest ? 'Guest' : this.user.user._id}`)
                    .listen('BalanceModification', (e) => Bus.$emit('event:balanceModification', e))
                    .listen('UserNotification', (e) => this.$toast.success(e.message, e.title))
					.listen('TemporaryNotice', (e) => { 
						this.$router.push('/');
						setTimeout(() => {
							this.$toast.success(e.message, e.title); 
						}, 800);
					})
                    .listen('BonusBattleWrongBet', (e) => { 
                        this.$router.push('/bonusbattle/' + e.bbid + '/' + e.bbgame + '?wrongbet' );
                    })

                    .listen('BonusBattleStarted', (e) => { 
                        this.$router.push('/bonusbattle/' + e.bbid + '/' + e.bbgame);
                    });



                Echo.channel('Everyone').listen('ChatMessage', (e) => Bus.$emit('event:chatMessage', e))
                    .listen('ChatRemoveMessages', (e) => Bus.$emit('event:chatRemoveMessages', e))
                    .listen('NewQuiz', (e) => Bus.$emit('event:chatNewQuiz', e))
                    .listen('QuizAnswered', (e) => Bus.$emit('event:chatQuizAnswered', e))
                    .listen('LiveFeedGame', (e) => Bus.$emit('event:liveGame', e))
                    .listen('PublicUserNotification', (e) => this.$toast.success(e.message, e.title))
					.listen('MaintenanceNotice', (e) => window.location.reload())
                    .listen('MultiplayerBettingStateChange', (e) => Bus.$emit('event:multiplayerBettingStateChange', e))
                    .listen('MultiplayerBetCancellation', (e) => Bus.$emit('event:multiplayerBetCancellation', e))
                    .listen('MultiplayerGameFinished', (e) => Bus.$emit('event:multiplayerGameFinished', e))
                    .listen('MultiplayerGameBet', (e) => Bus.$emit('event:multiplayerGameBet', e))
                    .listen('MultiplayerTimerStart', (e) => Bus.$emit('event:multiplayerTimerStart', e))
                    .listen('MultiplayerDataUpdate', (e) => Bus.$emit('event:multiplayerDataUpdate', e))
					.listen('ChallengesNew', (e) => { 
						this.$toast.success(this.$i18n.t('challenges.new', { game: e.challenge.game_name, reward: e.challenge.sum, currency: this.currencies[e.challenge.currency].name })); 
						Bus.$emit('event:ChallengesNew', e)
					})
                    .listen('BonusBattleNew', (e) => { 
                        this.$toast.success(this.$i18n.t('bonusbattle.new', { game: e.bonusbattle.g_name, reward: e.bonusbattle.stake, currency: this.currencies[e.bonusbattle.currency].name })); 
                        Bus.$emit('event:BonusBattleNew', e)
                    })
                    .listen('BonusBattleUpdate', (e) => { 
                        Bus.$emit('event:BonusBattleUpdate', e)
                    })
					.listen('ChallengesRemove', (e) => { 
						Bus.$emit('event:ChallengesRemove', e)
					});
					

                if(!this.isGuest) Echo.channel(`App.User.${this.user.user._id}`).notification((notification) => Bus.$emit('event:notification', notification));
            }
        },
        watch: {
            locale() {
                this.$router.replace({ params: { lang: this.locale } }).catch(() => {});
            },
            isGuest() {
                this.reconnectToWS();

                if(this.$route.meta.requiresAuth) this.$router.push('/');
                else if(this.$route.meta.requiresAccess) {
                    const access = {
                        user: 0,
                        moderator: 1,
                        admin: 2
                    };
                    if(this.isGuest || access[this.user.user.access ?? 'user'] < access[this.$route.meta.requiresAccess]) this.$router.push('/');
                }
            }
        }
    }
</script>
