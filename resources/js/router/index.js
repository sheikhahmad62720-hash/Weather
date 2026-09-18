import { createRouter, createWebHistory } from 'vue-router';
import WeatherIndex from '../Pages/Weather/Index.vue';

export default createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'weather',
            component: WeatherIndex,
        },
    ],
});