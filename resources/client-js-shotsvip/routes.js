import VueRouter from 'vue-router';
import AuthModal from "./components/modals/AuthModal";

const routes = [
    { path: '*', component: require('./components/views/PageNotFound.vue').default },
    { path: '/', component: require('./components/views/Index.vue').default },

    { path: '/password/reset/:user/:token', component: require('./components/views/Index.vue').default },
    { path: '/fairness', component: require('./components/views/Fairness.vue').default },
    { path: '/partner', component: require('./components/views/Affiliate.vue').default },
    { path: '/game/category/:category', component: require('./components/views/GameCategory.vue').default },
    { path: '/game/provider/:category', component: require('./components/views/GameProviderCategory.vue').default },
    { path: '/game/subcategory/:category', component: require('./components/views/GameSubCategory.vue').default },
	{ path: '/game/namecategory/:category', component: require('./components/views/GameNameCategory.vue').default },
    { path: '/challenges', component: require('./components/views/Challenges.vue').default },
    { path: '/bonusbattle', component: require('./components/views/BonusBattle.vue').default },
    { path: '/bonusbattle/:roomid/:id', component: require('./components/views/BonusBattleCasinolauncher.vue').default, prop: true },
	{ path: '/bonusbattle/:roomid', component: require('./components/views/BonusBattle.vue').default, prop: true },
	{ path: '/providers', component: require('./components/views/Providers.vue').default },
    { path: '/game/:id', component: require('./components/views/Game.vue').default, prop: true },
    { path: '/casino/:id', component: require('./components/views/CasinoGame.vue').default, prop: true },
    { path: '/profile/:tag', component: require('./components/views/Profile.vue').default, props: true },
    { path: '/wallet', component: require('./components/views/Wallet.vue').default, meta: { requiresAuth: true } }
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
        $('head').append($('<style>').attr('data-rendered-layout', 1).html(atob(window.Layout['Frontend'])));
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
