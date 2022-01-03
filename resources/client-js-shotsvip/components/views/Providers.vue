<template>
    <div class="gameCategory">
		<template v-if="!providersLoading">
			<div class="header">
				{{ $t('general.sidebar.providers') }}
			</div>
			<div class="container-fluid" style="max-width: 1420px; margin: 0px auto;">
				<div class="index_cat"> </div>
			   <div class="games">
				  <div v-for="(provider, index) in Providers" class="game_poster_external game-provider game_type-external hvr-float-shadow provider-background">
					<div :class="`game_poster_external-image game_tp-image`" :style="`background: url('${provider.img + (theme === 'dark' ? '?duotone=FFFFFF,b6b6b6&w=150' : '?duotone=00000,b6b6b6&w=150')}') 50% no-repeat !important;`" @click="$router.push(`/game/provider/${provider.name}`)">
					</div>
					<div class="game_poster_external-provider">
						{{ provider.name }}
					</div>
					<div @click="$router.push(`/game/provider/${provider.name}`)" class="game_poster_external-label"><p><b style="text-transform: capitalize;">{{ provider.name }}</b></p></div> 
				  </div>
			   </div>
			</div>
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
				Providers: [],
				providersLoading: true,
				pageLoading: true,
				page: 0,
				depth: 30,
				count: 0
            }
        },
        computed: {
            ...mapGetters(['isGuest', 'theme'])
        },
        created() {
			var payload = {
					page: this.page
			};
			axios.post('/api/data/providers', payload).then(({ data }) => {
				this.page += 1;
                this.Providers = data[0].providers;
				this.count = data[0].count;
				if(this.Providers.length === 0) {
					this.$router.push('/404');
					return;
				}
				this.providersLoading = false;
				this.pageLoading = false;
            });
        },
		methods: {
			pageLoad() {
				this.pageLoading = true;
				var payload = {
						page: this.page
				};
				axios.post('/api/data/providers', payload).then(({ data }) => {
					this.page += 1;
					this.Providers = this.Providers.concat(data[0].providers);
					this.count = data[0].count;
					this.providersLoading = false;
					this.pageLoading = false;
				});
			}
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";
	
	.provider-background {
		@include themed() {
			background: rgba(t('sidebar'), .8) !important;
		}
	}
	
</style>
