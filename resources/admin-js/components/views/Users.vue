<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Users</h4>
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
                                    <th>Username</th>
                                    <th>Created at</th>
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
    require('datatables.net');

    export default {
        data() {
            return {
                users: null
            }
        },
        created() {
			$(document).ready(function () {
				$('#datatable').DataTable({
					"autoWidth": false,
					processing: true,
					pageLength: 10,
					serverSide: true,
					destroy: true,
					ajax: "/admin/users/get",
					columns: [
						{ data: 'name',
							"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								$(nTd).html("<a style='color:#6b768d' href='/admin/user/"+oData._id+"'><img alt src="+oData.avatar+" style='width: 32px; height: 32px; margin-right: 5px;'>"+oData.name+"</a>");
							}
						},
						{ data: 'created_at',
							render: function ( data, type, row ) {
								return new Date(data).toLocaleString();
							}
						},
						{
							"render": function (data, type, row, meta) {
								return "<a href='/admin/user/"+row._id+"'>View & Edit</a>"; 
							}
				 
						}
					 ],
					"language": {
						"paginate": {
							"previous": "< ",
							"next": " >"
						},
						"processing": "<div class='text-center mt-2'><div class='spinner-border m-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>"
					},
					"drawCallback": function () {
						$('.dataTables_paginate > .pagination').addClass('pagination-rounded');
					}
				});
			});
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
	
	div#datatable_paginate {
		margin: 0;
		white-space: nowrap;
		text-align: right;
	}
</style>
