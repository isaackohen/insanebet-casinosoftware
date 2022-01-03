<template>
    <div>
		<modal></modal>
        <div id="wrapper">
			<div class="navbar-custom">
			   <div class="container-fluid">
					<ul class="list-unstyled topnav-menu float-end mb-0">
						<li  @click="toggleRightBar" class="dropdown notification-list">
							<a href="javascript:void(0);" class="nav-link right-bar-toggle">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
							</a>
						</li>
				  </ul>
				  <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
					 <li>
						<button @click="toggleBarMobile" class="button-menu-mobile disable-btn">
						   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
							  <line x1="3" y1="12" x2="21" y2="12"></line>
							  <line x1="3" y1="6" x2="21" y2="6"></line>
							  <line x1="3" y1="18" x2="21" y2="18"></line>
						   </svg>
						</button>
					 </li>
				  </ul>
				  <div class="logo-box">
					<a href="" class="logo logo-dark">
						<span class="logo-sm">
							<router-link to="/admin" class="navbar-brand mr-0 mr-md-2 logo" style="font-size: 1.8em; cursor: pointer">
								M
							</router-link>
						</span>
						<span class="logo-lg">
							<router-link to="/admin" class="navbar-brand mr-0 mr-md-2 logo" style="font-size: 1.8em; cursor: pointer">
								Management
							</router-link>
						</span>
					</a>
					<a href="" class="logo logo-light">
						<span class="logo-sm">
							<router-link to="/admin" class="navbar-brand mr-0 mr-md-2 logo" style="font-size: 1.8em; cursor: pointer">
								M
							</router-link>
						</span>
						<span class="logo-lg">
							<router-link to="/admin" class="navbar-brand mr-0 mr-md-2 logo" style="font-size: 1.8em; cursor: pointer">
								Management
							</router-link>
						</span>
					</a>
				</div>
				  <div class="clearfix"></div>
			   </div>
			</div>
			<div class="left-side-menu">
                <div class="h-100" data-simplebar> 
						<div id="sidebar-menu" class="slimscroll-menu">
							<ul id="side-menu">
										<li>
											<a onclick="window.open(window.location.origin)">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
												<span> Back </span>
											</a>
										</li>
									   <li class="menu-title mt-2">Control</li>
										<li>
											<router-link to="/admin">
												<feather type="activity"></feather>
												<span> Dashboard</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/wallet">
												<feather type="clock"></feather>
												<span class="badge bg-danger float-end" v-if="info">
													{{ info.withdraws }}
												</span>
												<span> Ledger</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/promo">
												<feather type="percent"></feather>
												<span> Promocodes</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/challenges">
												<feather type="trello"></feather>
												<span> Challenges</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/vips">
												<feather type="award"></feather>
												<span> Vips</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/users">
												<feather type="users"></feather>
												<span> Users</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/notifications">
												<feather type="bell"></feather>
												<span> Notifications</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/currency">
												<feather type="disc"></feather>
												<span> Currency</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/activity">
												<feather type="alert-triangle"></feather>
												<span> Activity</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/modules">
												<feather type="git-merge"></feather>
												<span> Inhouse Games</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/externalgames">
												<feather type="zap"></feather>
												<span> External Games</span>
											</router-link>
										</li>                            
										<li>
											<router-link to="/admin/bot">
												<feather type="shuffle"></feather>
												<span> Bot Settings</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/settings">
												<feather type="settings"></feather>
												<span> Settings</span>
											</router-link>
										</li>
										<li class="menu-title">Server</li>
										<li>
											<a href="javascript:void(0)" onclick="window.open('/admin/logs', '_blank');">
												<feather type="align-left"></feather>
												<span class="badge bg-danger float-end" style="background: #fd0c31" v-if="info">
													{{ info.logs.critical }}
												</span>
												<span class="badge bg-danger float-end" style="position: relative; right: 5px" v-if="info">
													{{ info.logs.error }}
												</span>
												<span> Logs</span>
											</a>
										</li> 
										<li>
											<a href="javascript:void(0)">
												<feather type="hash"></feather>
												<span class="badge bg-success float-end" v-if="info">
													{{ info.version }}
												</span>
												<span> Version</span>
											</a>
										</li>
							</ul>
						</div>
                    <div class="clearfix"></div>
                </div>
            </div>



            <div class="content-page">
             <loader v-if="!loadedContent"></loader>
                    <template v-else>
                 <div class="content">
                    <router-view :key="$route.fullPath" class="container-fluid"></router-view>
                </div>
                 </template>

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                {{ new Date().getFullYear() }} &copy;
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
		
		<div class="right-bar">
            <div data-simplebar="init" class="h-100"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden;"><div class="simplebar-content" style="padding: 0px;">
    
                <h6 class="fw-medium px-3 m-0 py-2 text-uppercase bg-light">
                    <span class="d-block py-1">Site Options</span>
                </h6>

                <div v-if="info" class="p-3">
                    <div class="alert alert-warning" role="alert">
                        <strong>Basic settings</strong> for quick interaction with the site.
                    </div>
                    <h6 class="fw-medium mt-4 mb-2 pb-1">Maintenance mode</h6>
                    <div class="form-switch mb-1">
                        <input @click="toggleMaintenance()" type="checkbox" class="form-check-input" name="color-scheme-mode" value="light" id="light-mode-check" :checked="info.maintenance">
                        <label class="form-check-label" for="light-mode-check">Toggle Off/On</label>
                    </div>

                    <h6 class="fw-medium mt-4 mb-2 pb-1">Cache</h6>
                    <div class="d-block mb-1">
                        <button @click="clearCache()" class="btn btn-outline-primary">Clear Cache</button>
                        <button @click="optimizeCache()" class="btn btn-outline-primary">Cache</button>
                    </div>
                </div> 

            </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 1161px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: hidden;"><div class="simplebar-scrollbar" style="height: 0px; transform: translate3d(0px, 0px, 0px); display: none;"></div></div></div> <!-- end slimscroll-menu-->
        </div>

        <div class="rightbar-overlay" @click="toggleRightBar"></div>
		
    </div>
</template>

<script>
	let el = document.getElementsByTagName('body')[0];
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
            	loadedContent: null,
                info: null,
				mobileMenu: false
            }
        },
        computed: {
            ...mapGetters(['games'])
        },
        mounted() {
            axios.post('/admin/info').then(({ data }) => this.info = data);
        },
        created() {
        	this.loadedContent = true;
        },
        methods: {
            clearCache() {
                axios.post('/admin/clearCache');
                this.$store.dispatch('updateData');
            },       
            toggleGame(id) {
                axios.post('/admin/toggle', { name: id });
                this.$store.dispatch('updateData');
            },
            toggleRightBar() {
                const active = $('.rightbar-overlay').hasClass('active');
                $('.rightbar-overlay').toggle().toggleClass('active');
                $('.right-bar').css({'right': active ? '-270px' : 0});
            },
			toggleBarMobile() {
				this.mobileMenu = !this.mobileMenu;
				this.mobileMenu ? (el.classList.add('sidebar-enable')) : (el.classList.remove('sidebar-enable')); 
			},
			toggleMaintenance() {
                axios.post('/admin/maintenance').then(() => {
					axios.post('/admin/info').then(({ data }) => this.info = data);
				});
            }
        }
    }
</script>

<style>

.navbar-custom .button-menu-mobile {
    color: black !important;
}

.navbar-custom .topnav-menu .nav-link svg {
    height: 20px !important;
    width: 20px !important;
    color: rgb(86 86 86 / 60%) !important;
    fill: rgb(62 62 62 / 12%) !important;
}

.left-side-menu {
	overflow: scroll;
	z-index: 12 !important;
}

.left-side-menu::-webkit-scrollbar {
  width: 1mm;
}

.left-side-menu::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
}

</style>