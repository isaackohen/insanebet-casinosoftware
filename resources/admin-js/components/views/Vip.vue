<template>
    <div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Vip</h4>
				</div>
			</div>
		</div>
		<div class="text-center mt-2" v-if="!info">
			<div class="spinner-border m-2" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
        <div v-else class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Setting</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
								<tr v-if="info.vip">
									<td>Vip Level </td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.level" disabled="disabled"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Level Name</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.level_name" id="level_name" v-model="level_name"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Wagered Up</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.start" id="start" v-model="start"></td>
                                </tr>
                                <tr v-if="info.vip">
									<td>Challenges Bonus</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.challenges_bonus" id="challenges_bonus" v-model="challenges_bonus"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Promocode Bonus</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.promocode_bonus" id="promocode_bonus" v-model="promocode_bonus"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Rake Percent</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.rake_percent" id="rake_percent" v-model="rake_percent"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Faucet Bonus</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.faucet_bonus" id="faucet_bonus" v-model="faucet_bonus"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Free Spins Bonus</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.fs_bonus" id="fs_bonus" v-model="fs_bonus"></td>
                                </tr>
								<tr v-if="info.vip">
									<td>Super Spins Bonus</td>
                                    <td><input class="form-control form-control-sm" :placeholder="info.vip.fs_superspin" id="fs_superspin" v-model="fs_superspin"></td>
                                </tr>
                            </tbody>
                        </table>
						<div class="float-sm-end mt-3 mt-sm-0">
							<div class="row g-2">
								<div class="col-md-auto">
									<div class="btn-group">
										<button @click="save" class="btn btn-primary">Save</button>
									</div>
								</div>
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
                info: null,
				start: null,
				level_name: null,
				challenges_bonus: null,
				promocode_bonus: null,
				rake_percent: null,
				faucet_bonus: null,
				fs_bonus: null,
				fs_superspin: null
            }
        },
        methods: {
            save() {
				axios.post('/admin/vips/save', {
					id: this.info.vip._id,
                    level_name: this.level_name,
                    start: this.start,
					challenges_bonus: this.challenges_bonus,
					promocode_bonus: this.promocode_bonus,
					rake_percent: this.rake_percent,
					faucet_bonus: this.faucet_bonus,
					fs_bonus: this.fs_bonus,
					fs_superspin: this.fs_superspin
                }).then(() => this.$router.go()).catch(() => this.$toast.error('Error'));
            }
        },
        created() {
            axios.post('/admin/vips/vip', { id: this.$route.params.id }).then(({ data }) => {
                this.info = data;
				this.start = data.vip.start;
				this.level_name = data.vip.level_name
				this.challenges_bonus = data.vip.challenges_bonus ?? 0;
				this.promocode_bonus = data.vip.promocode_bonus ?? 0;
				this.rake_percent = data.vip.rake_percent ?? 0;
				this.faucet_bonus = data.vip.faucet_bonus ?? 0;
				this.fs_bonus = data.vip.fs_bonus ?? 0;
				this.fs_superspin = data.vip.fs_superspin ?? 0;
            });
        }
    }
</script>
