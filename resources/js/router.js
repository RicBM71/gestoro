import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import store from './store/index';
import routes from './routes';
import {api} from "./config";
//import { request } from 'https';

const router = new VueRouter({
	mode: 'history',
    routes,
    scrollBehavior(){
		return{x:0, y:0};
}
});



router.beforeEach(async (to, from, next) => {
	 next();
});

export default router;
