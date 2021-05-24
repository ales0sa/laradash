import Vue from 'vue';
/*
import ConfirmationService from 'primevue/confirmationservice';
Vue.use(ConfirmationService);
import ConfirmDialog from 'primevue/confirmdialog';
Vue.component(ConfirmDialog)
*/
import Router from 'vue-router'
import Dashboard from './components/Dashboard.vue';
import Crud from './components/providers/providers.vue';
import CrudCreate from './components/crud/index.vue';
import CrudGenerator from './components/crud-generator/cruds.vue';
import CrudEdit from './components/crud-generator/index.vue';
import CrudUsers from './components/users/index.vue';
import CrudGrupos from './components/users/groups.vue';
import WebConfig from './components/company-data/CompanyDataFormComponent.vue';

import CrudDB from './components/crud-generator/fromdb.vue';
import Laralogs from './components/laralogs.vue';

import CustomComponent from './components/crud/CustomComponent.vue';
import CustomView from './components/CustomView.vue';

import error403 from './components/errors/403.vue';

Vue.use(Router);


export default new Router({
	routes: [
		{
			path: '/',
			name: 'dashboard',
			component: Dashboard
		},
		{
			path: '/custom/:vc/:id?',
			name: 'VueComp',
			component: CustomView,
			props: true
        },
		{
			path: '/company-data',
			name: 'webconfig',
			component: WebConfig
		},
		{
			path: '/laralogs',
			name: 'laralogs',
			component: Laralogs
		},
		{
			path: '/crud/:table',
			name: 'crud',
			component: Crud,
			props: true
		},
		{
			path: '/crud/:table/create',
			name: 'crudcreate',
			component: CrudCreate,
			props: true
		},
		{
			path: '/crud-generator/',
			name: 'crudgenerator',
			component: CrudGenerator,
			props: true
		},
		{
			path: '/crud-generator/createfromdb',
			name: 'crdb',
			component: CrudDB,

		},
		{
			path: '/crud-generator/:file',
			name: 'cre',
			component: CrudEdit,
			props: true
		},
		{
			path: '/crud-generator/create',
			name: 'cren',
			component: CrudEdit,

		},

		{
			path: '/crud/:table/:id/edit',
			name: 'ced',
			component: CrudCreate,

		},
		{
			path: '/users',
			name: 'users',
			component: CrudUsers,

		},
		{
			path: '/grupos',
			name: 'grupos',
			component: CrudGrupos,
			props: true
		},
		{
			path: '/403',
			name: '403',
			component: error403,
			props: true
		},
	],
	scrollBehavior() {
		return {x: 0, y: 0};
	}
});
