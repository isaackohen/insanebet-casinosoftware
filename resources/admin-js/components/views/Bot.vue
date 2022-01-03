<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Bot Settings</h4>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-xl-10 col-lg-9">
								<div class="mt-4 mt-lg-0">
									<h5 class="mt-0 mb-1 fw-bold">Bots options list</h5>
									<p class="text-muted mb-2">
										Launching bots to implement fake games, statistics. Optional bot settings (default values)
									</p>
									<button class="btn btn-primary" @click="start">Start</button>
								</div>
							</div>
						</div>
						
					</div> <!-- end card body-->
				</div> <!-- end card -->
			</div>
			<!-- end col-12 -->
		</div>
		
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="setting in settings">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-muted">{{ setting.name }}</a></h5>
                        <div class="text-muted">
                            <div class="form-group mt-2">
                                <input @input="change(setting.name, $event.target.value)" :value="setting.value" type="text" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() { 
            return {
                settings: []
            }
        },
        created() {
            axios.post('/admin/bot/settings').then(({ data }) => this.settings = data);
        },
        methods: {
            change(key, value) {
                axios.post('/admin/settings/edit', { key: key, value: value.length === 0 ? '0' : value });
            },
            start() {
                axios.post('/admin/bot/start').then(() => this.$toast.error('Success'));
            }
        }
    }
</script>
