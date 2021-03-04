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
    aislar: null,
    flex_cortesia: null,
    lotes_abiertos: 0,
    whatsApp: null,
    mail_renova: false,
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
        state.aislar = payload.user.aislar_empresas;
        state.flex_cortesia= payload.user.flex_cortesia;
        state.whatsApp = payload.user.whatsApp;
        state.mail_renova = payload.user.mail_renova;
        state.lotes_abiertos= payload.user.lotes_abiertos;
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
        state.aislar=null;
        state.flex_cortesia=null;
        state.whatsApp=null;
        state.mail_renova=false,
        state.lotes_abiertos=0;
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
    hasUsers: (state) =>{
        return (state.permisos.indexOf('users') >= 0) ? true : false;
    },
    hasConsultas: (state) =>{
        return (state.permisos.indexOf('consultas') >= 0) ? true : false;
    },
    hasProcesos: (state) =>{
        return (state.permisos.indexOf('procesos') >= 0) ? true : false;
    },
    hasEdtCaj: (state) =>{
        return (state.permisos.indexOf('edtcaj') >= 0) ? true : false;
    },
    hasExcel: (state) =>{
        return (state.permisos.indexOf('excel') >= 0) ? true : false;
    },
    hasEdtCli: (state) =>{
        return (state.permisos.indexOf('edtcli') >= 0) ? true : false;
    },
    hasEdtFec: (state) =>{
        return (state.permisos.indexOf('edtfec') >= 0) ? true : false;
    },
    hasWhatsApp: (state) =>{
        return (state.permisos.indexOf('whatsapp') >= 0) ? true : false;
    },
    hasDelDep: (state) =>{
        return (state.permisos.indexOf('deldep') >= 0) ? true : false;
    },
    hasDelCob: (state) =>{
        return (state.permisos.indexOf('delcob') >= 0) ? true : false;
    },
    hasEdtAlb: (state) =>{
        return (state.permisos.indexOf('edtalb') >= 0) ? true : false;
    },
    hasEdtCom: (state) =>{
        return (state.permisos.indexOf('edtcom') >= 0) ? true : false;
    },
    flexCortesia: (state) =>{
        return state.flex_cortesia;
    },
    whatsApp: (state) =>{
        return state.whatsApp;
    },
    mail_renova:(state) =>{
        return state.mail_renova;
    },
    lotes: (state) =>{
        return state.lotes_abiertos;
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
