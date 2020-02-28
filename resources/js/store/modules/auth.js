/*
|--------------------------------------------------------------------------
| Mutation Types
|--------------------------------------------------------------------------
*/
export const SET_USER = 'SET_USER';
export const UNSET_USER = 'UNSET_USER';

/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/
const initialState = {
    id: null,
	name: null,
    username: null,
    avatar: null,
    empresa_id: null,
    empresa_nombre: null,
    roles: [],
    permisos: [],
    parametros: Object,
    img_fondo: null,
    stock: null,
    aislar: null
};

/*
|--------------------------------------------------------------------------
| Mutations
|--------------------------------------------------------------------------
*/
const mutations = {
	[SET_USER](state, payload) {
        state.id = payload.user.id;
		state.name = payload.user.name;
        state.username = payload.user.username;
        state.avatar = payload.user.avatar;
        state.empresa_id = payload.user.empresa_id;
        state.empresa_nombre = payload.user.empresa_nombre;
        state.roles = payload.user.roles;
        state.permisos = payload.user.permisos;
        state.parametros = payload.user.parametros;
        state.img_fondo = payload.user.img_fondo;
        state.aislar = payload.user.aislar_empresas
	},
	[UNSET_USER](state, payload) {
        state.id = null;
        state.name = null;
        state.username = null;
        state.avatar = null;
        state.empresa_id=null;
        state.empresa_nombre=null;
        state.roles = [];
        state.permisos = [];
        state.parametros = {};
        state.img_fondo=null;
        state.aislar=null
	}
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const actions = {
	setAuthUser: (context, user) => {
		context.commit(SET_USER, {user})
	},
	unsetAuthUser: (context) => {
		context.commit(UNSET_USER);
	}
};

/*
|--------------------------------------------------------------------------
| Getters
|--------------------------------------------------------------------------
*/
const getters = {
    userId: (state) =>{
        return state.id
    },
    userName: (state) =>{
        return state.username
    },
    userAvatar: (state) =>{
        return state.avatar
    },
    imgFondo: (state) =>{
        return state.img_fondo
    },
    parametros: (state) =>{
        return state.parametros
    },
    aislar: (state) =>{
        return state.aislar
    },
    empresaActiva: (state) =>{
        return state.empresa_id
    },
	isLoggedIn: (state) => {
		return !!(state.name && state.username);
    },
    getRoles: (state) =>{
        return state.roles
    },
    isRoot: (state) =>{
        return (state.roles.indexOf('Root') >= 0) ? true : false;
    },
    isAdmin: (state) =>{
        return (state.roles.indexOf('Admin') >= 0) ? true : false;
    },
    isSupervisor: (state) =>{
        return (state.roles.indexOf('Supervisor') >= 0 || state.roles.indexOf('Admin') >= 0) ? true : false;
    },
    isGestor: (state) =>{
        return (state.roles.indexOf('Gestor') >= 0) ? true : false;
    },
    hasAddCom: (state) =>{
        return (state.permisos.indexOf('addcom') >= 0) ? true : false;
    },
    hasAddVen: (state) =>{
        return (state.permisos.indexOf('addven') >= 0) ? true : false;
    },
    hasReaCompras: (state) =>{
        return (state.permisos.indexOf('reacom') >= 0) ? true : false;
    },
    hasLimEfe: (state) =>{
        return (state.permisos.indexOf('salefe') >= 0) ? true : false;
    },
    hasBorraCompras: (state) =>{
        return (state.permisos.indexOf('delcom') >= 0) ? true : false;
    },
    hasLiquidar: (state) =>{
        return (state.permisos.indexOf('liquidar') >= 0) ? true : false;
    },
    hasEditPro: (state) =>{
        return (state.permisos.indexOf('edtpro') >= 0) ? true : false;
    },
    hasAuthTras: (state) =>{
        return (state.permisos.indexOf('authtras') >= 0) ? true : false;
    },
    hasFactura: (state) =>{
        return (state.permisos.indexOf('factura') >= 0) ? true : false;
    },
    hasScan: (state) =>{
        return (state.permisos.indexOf('scan') >= 0) ? true : false;
    },
    hasEdtFac: (state) =>{
        return (state.permisos.indexOf('edtfac') >= 0) ? true : false;
    },
    hasDesLoc: (state) =>{
        return (state.permisos.indexOf('desloc') >= 0) ? true : false;
    },
};

/*
|--------------------------------------------------------------------------
| Export the module
|--------------------------------------------------------------------------
*/
export default {
	state: initialState,
	mutations,
	actions,
	getters
}
