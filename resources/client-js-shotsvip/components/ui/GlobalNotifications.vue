<template>
    <transition-group name="fade" mode="out-in">
        <div class="globalNotification" v-for="notification in notifications" :key="notification._id">
            <div class="icon"><icon :icon="notification.icon"></icon></div>
            <div class="text" v-html="notification.text"></div>
        </div>
    </transition-group>
</template>

<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        created() {
            Bus.$on('ws:pingStatus', (status) => this.redis = status);

            Bus.$on('ws:connect', () => this.ws = true);
            Bus.$on('ws:disconnect', () => this.ws = false);

            if(window.Echo.connector.socket.connected) this.ws = true;

            setTimeout(() => this.pageIsLoaded = true, 5000);

            setInterval(() => {
                this.signalIconAnim++;
                if(this.signalIconAnim >= 5) this.signalIconAnim = 0;
            }, 1000);
        },
        data() {
            return {
                ws: false,
                redis: true,
                pageIsLoaded: false,

                signalIconAnim: 0
            }
        },
        computed: {
            ...mapGetters(['notifications'])
        }
    }
</script>

<style scoped lang="scss">
    .notificationSpace {
        height: 53px;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s ease;
        opacity: 1;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>
