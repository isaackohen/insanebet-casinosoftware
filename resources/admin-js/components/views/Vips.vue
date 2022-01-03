<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Vips</h4>
					<div class="page-title-right">
						<div class="float-sm-end mt-3 mt-sm-0">
							<div class="row g-2">
								<div class="col-md-auto">
									<div class="btn-group">
										<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">Create</button>
									</div>
								</div>
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Vip lvl</th>
									<th>Vip Name</th>
                                    <th>Wagered Up</th>
									<th>Updated at</th>
									<th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
								<tr v-if="vips" v-for="vip in vips" :key="vip._id">
                                    <td>{{ vip.level }}</td>
                                    <td>{{ vip.level_name }}</td>
									<td>{{ vip.start }}</td>
									<td>{{ new Date(vip.updated_at).toLocaleString() }}</td>
									<td>
										<button style="padding: 0.15rem 0.3rem;" @click="remove(vip._id)" class="btn btn-primary"><unicon name="trash-alt" fill="white"></unicon></button>
										<router-link tag="button" :to="'/admin/vips/'+vip._id" style="margin-left: 10px; padding: 0.15rem 0.3rem;" class="btn btn-primary"><unicon name="edit" fill="white"></unicon></router-link>
									</td>
								</tr>
                                <div v-else>This may take a while...</div>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
		<div class="modal fade" id="create" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header p-2 border-bottom-0 d-block">
                        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-hidden="true"></button>
                        <h5 class="modal-title">Create Challenge</h5>
                    </div>
                    <div class="modal-body p-2">
                        <form class="needs-validation" name="event-form" id="form-event" novalidate="">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="control-label">Vip LVL</label>
                                        <input class="form-control" placeholder="Unique vip lvl" type="text" id="level" v-model="level">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Vip Name</label>
                                        <input class="form-control" placeholder="Vip name" type="text" id="level_name" v-model="level_name">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Wagered up</label>
                                        <input class="form-control" placeholder="Amount of wager required to get a level" type="text" id="start" v-model="start">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"></div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-light mr-1" id="close" data-bs-dismiss="modal">Close</button>
                                    <div class="btn btn-success" id="finish" @click="create">Create</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    require('datatables.net');
    export default {
        data() {
            return {
                vips: null
            }
        },
        created() {
            axios.post('/admin/vips/get').then(({data}) => {
				var vipsdata = data;
				vipsdata.sort(function(a, b) {
					var keyA = parseFloat(a.level),
					keyB = parseFloat(b.level);
					if (keyA < keyB) return -1;
					if (keyA > keyB) return 1;
					return 0;
				});
				this.vips = vipsdata;
                setTimeout(() => {
                    $('#datatable').DataTable({
                        destroy: true,
                        "language": {
                            "paginate": {
                                "previous": "Previous ",
                                "next": " Next"
                            }
                        },
                        "drawCallback": function () {
                            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                        }
                    });
                }, 1000);
            });
        },
		methods: {
            remove(id) {
                axios.post('/admin/vips/remove', { id: id }).then(() => this.vips = this.vips.filter((e) => e._id !== id));
            },
            create() {
                axios.post('/admin/vips/create', {
                    level: this.level,
                    level_name: this.level_name,
                    start: this.start,
                }).then(() => this.$router.go()).catch(() => this.$toast.error('Error'));
            }
        }
    }
</script>

<style lang="scss">

    #datatable {
        color: #3e3e3e;
    }
	
	#datatable_wrapper
	{
		overflow:auto;
	}
</style>