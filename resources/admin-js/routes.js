import VueRouter from 'vue-router';

const routes = [
    { path: '*', component: require('./components/views/PageNotFound.vue').default },
    { path: '/admin', component: require('./components/views/Dashboard.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/promo', component: require('./components/views/Promo.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/challenges', component: require('./components/views/Challenges.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/settings', component: require('./components/views/Settings.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/notifications', component: require('./components/views/Notifications.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/users', component: require('./components/views/Users.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/user/:id', component: require('./components/views/User.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/wallet', component: require('./components/views/Wallet.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/wallet_ignored', component: require('./components/views/WalletIgnored.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/modules', component: require('./components/views/Modules.vue').default, meta: { requiresAccess: 'admin' } },  
    { path: '/admin/externalgames', component: require('./components/views/ExternalGames.vue').default, meta: { requiresAccess: 'admin' } },  
	{ path: '/admin/extgame/:id', component: require('./components/views/ExternalGame.vue').default, meta: { requiresAccess: 'admin' } },
	{ path: '/admin/vips', component: require('./components/views/Vips.vue').default, meta: { requiresAccess: 'admin' } },
	{ path: '/admin/vips/:id', component: require('./components/views/Vip.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/currency', component: require('./components/views/Currency.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/activity', component: require('./components/views/Activity.vue').default, meta: { requiresAccess: 'admin' } },
    { path: '/admin/bot', component: require('./components/views/Bot.vue').default, meta: { requiresAccess: 'admin' } }
];

const router = new VueRouter({
    routes,
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        else return { x: 0, y: 0 };
    }
});

router.beforeEach((to, from, next) => {
    const user = (JSON.parse(localStorage.getItem('vuex')) ?? []).user;

    $('.flatpickr-calendar, .modal-backdrop').remove();
    $('body').removeClass('modal-open');

    const redirect = () => {
        if(window.Layout.Previous && ((to.fullPath.startsWith('/admin') && from.fullPath.startsWith('/admin')) || (!to.fullPath.startsWith('/admin') && !from.fullPath.startsWith('/admin'))))
            return next();

        window.Layout.Previous = true;
        $('[data-rendered-layout]').remove();
        $('head').append($('<style>').attr('data-rendered-layout', 1).html(atob(window.Layout['Backend'])));
        next();
    };

    if(to.matched.some(record => record.meta.requiresAuth)) {
        if(!(JSON.parse(localStorage.getItem('vuex')) ?? []).user) {
            AuthModal.methods.open('auth');
            return false;
        } else redirect();
    }

    if(to.matched.some(record => record.meta.requiresAccess)) {
        const access = {
            user: 0,
            moderator: 1,
            admin: 2
        };

        if(!(!user || access[user.user.access ?? 'user'] < access[to.meta.requiresAccess])) redirect();
    } else redirect();
});

export default router;
