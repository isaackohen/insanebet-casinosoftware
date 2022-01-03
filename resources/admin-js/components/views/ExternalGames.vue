<template>
	<div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">External Games</h4>
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
									<h5 class="mt-0 mb-1 fw-bold">Update Games List from API</h5>
									<p class="text-muted mb-2">
										Please note: this function will flush the gamelist and then re-populate it. If any error occurs, use the 'Restore Default List' function.
									</p>
    		    					<label for='unlockButtonsState'>
      									<input id='unlockButtonsState' type='checkbox' v-model='unlockButtonsState' /> Unlock Buttons
    								</label>
									<button :disabled='unlockButtons' class="btn btn-primary" @click="updateGames">Update</button>
									<button :disabled='unlockButtons' class="btn btn-secondary" @click="restoreGamesList">Restore Default List</button>
								</div>
							</div>
						</div>
						
					</div> <!-- end card body-->
				</div> <!-- end card -->
			</div>
			<!-- end col-12 -->
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
									<th>Provider</th>
									<th data-orderable="false">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">External Providers</h4>
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
									<h5 class="mt-0 mb-1 fw-bold">Update Providers from API</h5>
									<p class="text-muted mb-2">
										This option allows you to update the current up-to-date list of providers for external games. <br>
										Please note: GGR shown here is pricing since last time you've updated and may be incorrect.
										<br>For reliable GGR pricing, either update provider list or login API backoffice.
									</p>
									<label for='unlockButtonsState'>
      									<input id='unlockButtonsState' type='checkbox' v-model='unlockButtonsState' /> Unlock Buttons
    								</label>
									<button :disabled='unlockButtons' class="btn btn-primary" @click="update">Update</button>								
								</div>
							</div>
						</div>
						
					</div> <!-- end card body-->
				</div> <!-- end card -->
			</div>
			<!-- end col-12 -->
		</div>
		<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable-providers" class="table dt-responsive nowrap table-responsive">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th data-orderable="false">GGR</th>
									<th>Games</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        created() {
		
			$(document).ready(function () {
				$('#datatable').DataTable({
					responsive: true,
					autoWidth: false,
					processing: true,
					serverSide: true,
					destroy: true,
					ajax: "/admin/extgames/games/get",
					columns: [
						{ data: 'name',
							"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								$(nTd).html("<a style='color:#6b768d' href='/admin/extgame/"+oData._id+"'><img alt src='https://games.cdn4.dk/games"+oData.image+"?w=32&mask=ellipse&h=32&fm=png&q=50' style='width: 32px; height: 32px; margin-right: 5px;'>"+oData.name+"</a>");
							}
						},
						{ data: 'category' },
						{ data: 'provider' },
						{
							"render": function (data, type, row, meta) {
								if(row.isDisabled) {
									return "<div onclick='toggleExtGame(`"+row._id+"`)' class='form-switch mt-1'><input type='checkbox' name='color-scheme-mode' value='light' id='light-mode-check' class='form-check-input'></div>"; 
								} else {
									return "<div onclick='toggleExtGame(`"+row._id+"`)' class='form-switch mt-1'><input type='checkbox'oh  name='color-scheme-mode' value='light' id='light-mode-check' checked='checked' class='form-check-input'></div>"; 
								}
							}
				 
						},
						{
							"render": function (data, type, row, meta) {
								return "<a href='/admin/extgame/"+row._id+"'>View</a>"; 
							}
				 
						}
					 ],
					"pagingType": "full_numbers",
					"language": {
						"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
					},
					"drawCallback": function () {
						$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
					}
				});
				
				$('#datatable-providers').DataTable({
					responsive: true,
					autoWidth: false,
					processing: true,
					serverSide: true,
					destroy: true,
					ajax: "/admin/extgames/providers/get",
					columns: [
						{ data: 'provider',
							"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								$(nTd).html("<td><img alt src='"+oData.image+"' style='max-width: 100px; filter: brightness(0.35); margin-right: 35px;'>"+oData.provider+"</td>");
							}
						},
						{ data: 'ggr',
							render: function ( data, type, row ) {
								return data + '%';
							}
						},
						{ data: 'games' }
					 ],
					 "pagingType": "full_numbers",
					"language": {
						"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
					},
					"drawCallback": function () {
						$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
					}
				});
				
			});
			window.toggleExtGame = function toggleExtGame(id) {
				axios.post('/admin/extToggle', { id: id });
			}
        },
        data() {
            return {
                externalGames: null,
                unlockButtonsState: false
            }
        },
          computed: {
  			unlockButtons: function(){
    		return !this.unlockButtonsState;
    		},
    	},
		methods: {
            update() {
                axios.post('/admin/extgames/updateProviders').then(() => this.$toast.success('Success'));
            },
            updateGames() {
                axios.post('/admin/extgames/updateGames').then(() => this.$toast.success('Gameslist seems updated. Run clear cache command from main dashboard to ensure immediate effect.'));
            },
            restoreGamesList() {
                axios.post('/admin/extgames/restoreGamesList').then(() => this.$toast.success('Gamelist restored.'));
            }
        }
    }
</script>


<style lang="scss">

	#datatable_wrapper
	{
		overflow:auto;
	}
	
	.fw-bold {
		letter-spacing: .1em;
		text-transform: uppercase;
	}
	
	#datatable {
        color: #3e3e3e;
    }
	
	div#datatable-providers_paginate {
		margin: 0;
		white-space: nowrap;
		text-align: right;
	}
</style>