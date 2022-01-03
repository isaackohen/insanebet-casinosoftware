<template>
    <div class="gameCategory">
		<template v-if="!gamesLoading">
        <div class="header">
            {{ $t('general.sidebar.' + $route.params.category) }}
        </div>
			<div class="container-lg mt-1" v-if="$route.params.category === 'freespins'">
				<p v-html="$t('bonus.freespins')"></p>
				<p v-if="!isGuest">You have <span class="highlight-secondary">{{ user.user.freespins }}</span> free spins.</p>
				<hr> 
			</div>
			<div class="warning" v-if="Object.keys(categoryGames).length === 0">
				{{ $t('general.sidebar.no_games') }}
			</div>
			<games :categories="categoryGames"></games>
		</template>
		<div v-else class="games-load">
			<loader></loader>
		</div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                categoryGames: {},
				gamesData: {},
				gamesLoading: true,
				page: 0
            }
        },
		watch: {
            gamesData() {
                this.categoryGames = {};
                this.load();
            }
        },
        computed: {
            ...mapGetters(['isGuest'])
        },
        created() {
			var payload = {
				subcategory: [
					this.$route.params.category
				],
				page: this.page
			};
			axios.post('/api/data/games', payload).then(({ data }) => {
                this.gamesData = data[0].games;
				if(this.gamesData.length === 0) {
					this.$router.push('/404');
					return;
				}
				this.gamesLoading = false;
            });
			this.load();
        },
		methods: {
            load() {
				let duplicates = [];
				_.forEach(this.gamesData, (game) => {
					if (game.cat.includes(this.$route.params.category)) {
						if (duplicates.includes(game.id)) return;
						duplicates.push(game.id);

						if (!this.categoryGames[game.cat]) this.categoryGames[game.cat] = [game];
						else this.categoryGames[game.cat].push(game);
					}
				});
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";
	.highlight-secondary {
        @include themed() {
        	font-weight: 500;
        	color: t('secondary');
        }
        
	}
</style>
