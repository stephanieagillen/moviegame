import VueRouter from 'vue-router';

let routes = [
    {
        path: '/games/:id',
        name: 'gameIndex',
        component: require('./components/game.vue')
    },
        {
        path: '/',
        name: 'home',
        component: require('./components/games.vue')
    }
];

export default new VueRouter({
    routes
});