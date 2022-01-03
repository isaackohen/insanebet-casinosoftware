<template>
    <div class="container-fluid" v-if="info">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<h4 class="page-title">External Game</h4>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xl-8">
			<div class="card">
				<div class="card-body">
					<h6 class="mt-0 header-title"> {{ info.game.name }}</h6>

					<div class="text-muted mt-3">
						<p>Description</p>
						<ul class="ps-4 mb-4">
							<li>{{ info.game.desc }}</li>
						</ul>
						<p>Games Played</p>
						<ul class="ps-4 mb-4">
							<li>{{ info.count }}</li>
						</ul>
						<p>Uid</p>
						<ul class="ps-4 mb-4">
							<li>{{ info.game._id }}</li>
						</ul>
						<div class="row">
							<div class="col-lg-3 col-md-6">
								<div class="mt-4">
									<p class="mb-2 text-uppercase fs-13 fw-bold"> Category</p>
									<h5 class="fs-16">{{ info.game.category }}</h5>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mt-4">
									<p class="mb-2 text-uppercase fs-13 fw-bold"> Provider</p>
									<h5 class="fs-16">{{ info.game.provider }}</h5>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mt-4">
									<p class="mb-2 text-uppercase fs-13 fw-bold"> GGR provider</p>
									<h5 class="fs-16">{{ info.ggr }}%</h5>
								</div>
							</div>

							<div class="col-lg-3 col-md-6">
								<div class="mt-4">
									<p class="mb-2 text-uppercase fs-13 fw-bold"> Status</p>
									<h5 class="fs-16">{{ info.game.d === 1 ? 'Disabled' : 'Enabled' }}</h5>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
			</div>
			<!-- end card -->
		<div class="col-xl-4">
			<div class="card">
				<div class="card-body">
					<h6 class="mt-0 header-title">Image</h6>

					<ul class="list-unstyled activity-widget">
						<li class="activity-list">
							<div class="d-flex">
								<img class="card-img-top img-fluid" :src="'https://games.cdn4.dk/games'+info.game.image" alt="Card image cap">
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
    </div>
	<div v-else>
		<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>
	</div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import OverviewModal from "../modals/OverviewModal";
	import OverviewDataModal from "../modals/OverviewDataModal";

    export default {
        data() {
            return {
                info: null
            }
        },
        computed: {
            ...mapGetters(['currencies'])
        },
        created() {
            axios.post('/admin/extgames/game', { id: this.$route.params.id }).then(({ data }) => {
                this.info = data;
            });
        }
    }
</script>
