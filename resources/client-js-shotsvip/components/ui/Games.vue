<template>
    <div class="container-fluid" style="max-width: 1420px; margin: 0px auto;">
        <template v-for="(cat, key) in categories">
            <div v-if="key != 'freespins'" class="index_cat">
                <i class="fab fa-gripfire"></i> {{ $t('general.sidebar.' + key) }}
            </div>

            <div class="games">
                <div v-for="game in cat" :key="game.id" :class="`frame-loader game_poster_${game.type} game-${game.id} game_type-${game.type} img-hover-zoom--zoom-n-rotate`">
                    <div :class="`game_poster_${game.type}-image game_tp-image`" v-if="game.ext"  @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        <img :src="`${cdnBase}${game.icon}${cdnParameters}`" alt :class="`game_poster_${game.type}-image`">
                    </div>

                    <div :class="`game_poster_${game.type}-image game_tp-image`" v-if="!game.ext" @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        <img :src="`${cdnBase}/provablyfair/${game.id}.png${cdnParameters}`" alt :class="`game_poster_${game.type}-image`">

                        <div class="unavailable" v-if="game.d">
                            <div class="slanting">
                                <div class="content">
                                    {{ $t(game.isPlaceholder ? 'general.coming_soon' : 'general.not_available') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div :class="`game_poster_${game.type}-houseEdge`" v-if="game.houseEdge" @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        {{ game.houseEdge }}% House Edge
                    </div>

                    <div :class="`game_poster_${game.type}-label`" @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        <p><b>{{ game.name }}</b>
                        <br>
                        <small v-if="game.ext">by {{ game.p }}</small>
                        <small v-if="!game.ext">in-house provably fair</small> 
                    </p>
                    </div>

                    <div :class="`game_poster_${game.type}-footer`" @click="toggleFavoriteGame(game.id)" v-if="!isGuest">
                        <template v-if="!favMarkLoading">
                            <i :class="`fa${!user.user.favoriteGames || !user.user.favoriteGames.includes(game.id) ? 'l' : 's'} fa-heart`"></i>
                            {{ $t(!user.user.favoriteGames || !user.user.favoriteGames.includes(game.id) ? 'general.sidebar.mark_as_favorite' : 'general.sidebar.remove_from_favorite') }}
                        </template>
                        <loader v-else></loader>
                    </div>
                     <div :class="`game_poster_${game.type}-textbelowposter`"><i class="far fa-scrubber"></i> {{ game.p }}</div>
                </div>
            </div>
        </template>
    </div>
</template>

<script type="text/javascript">
    import { mapGetters } from 'vuex';

    export default {
        props: ['categories'],
        computed: {
            ...mapGetters(['cdnBase', 'cdnParameters', 'user', 'isGuest'])
        },
        data() {
            return {
                favMarkLoading: false
            }
        },
        methods: {
            toggleFavoriteGame(id) {
                if(this.favMarkLoading) return;
                this.favMarkLoading = true;
                axios.post('/api/user/markGameAsFavorite', { id: id }).then(() => {
                    this.$store.dispatch('update');
                    this.favMarkLoading = false;
                }).catch(() => this.favMarkLoading = false);
            }
        }
    }
</script>
