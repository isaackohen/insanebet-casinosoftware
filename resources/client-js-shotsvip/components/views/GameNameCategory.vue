<template>
    <div class="gameCategory">
		<template v-if="!gamesLoading">
        <div class="header">
            {{ $t('general.sidebar.' + $route.params.category) }}
        </div>
			<div class="warning" v-if="Object.keys(categoryGames).length === 0">
				{{ $t('general.sidebar.no_games') }}
			</div>
			<games :categories="categoryGames"></games>
			<template v-if="!pageLoading">
				<template v-if="(page * depth < count)">
					<div class="divider">
						<div class="line"></div>
						<div class="divider-title">
							<div class="show-more_progress-track">
								<div :style="{ width: ((page * depth / count) * 100) + '%' }" class="show-more_progress-bar"></div>
							</div>
							<div class="show-more_text">Shown <span> {{ page * depth }} </span> from <span>{{ count }}</span> games</div>
							<button @click="pageLoad()" href="javascript:void(0)" class="btn show-more btn-primary"><i class="fas fa-random" aria-hidden="true"></i> Show more</button>				
						</div>
						<div class="line"></div>
					</div>
				</template>
			</template>
			<div v-else class="games-load">
			<loader></loader>
			</div>
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
				gamesData: [],
				gamesLoading: true,
				pageLoading: true,
				page: 0,
				depth: 35,
				count: 0
            }
        },
        computed: {
            ...mapGetters(['isGuest'])
        },
		watch: {
            gamesData() {
                this.categoryGames = {};
                this.load();
            }
        },
        created() {
			var payload = {
				text: this.$route.params.category,
				page: this.page
			};
			axios.post('/api/data/games', payload).then(({ data }) => {
				this.page += 1;
				this.gamesData = data[0].games;
				this.count = data[0].count;
				if(this.gamesData.length === 0) {
					this.$router.push('/404');
					return;
				}
				this.gamesLoading = false;
				this.pageLoading = false;
			});
			this.load();
        },
		methods: {
            load() {			
				let validateUrlCategory = true;
				let games = this.gamesData;
				let duplicates = [];
				_.forEach(games, (game) => {
					if (duplicates.includes(game.id)) return;
					duplicates.push(game.id);

					if (!this.categoryGames[this.$route.params.category]) this.categoryGames[this.$route.params.category] = [game];
					else this.categoryGames[this.$route.params.category].push(game);
				});
            }, 
			pageLoad() {
				this.pageLoading = true;
				var payload = {
					text: this.$route.params.category,
					page: this.page
				};
				axios.post('/api/data/games', payload).then(({ data }) => {
					this.page += 1;
					this.gamesData = this.gamesData.concat(data[0].games);
					this.count = data[0].count;
					this.pageLoading = false;
				});
				this.load();
			}
        }
    }
</script>
