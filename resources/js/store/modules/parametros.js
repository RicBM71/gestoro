/*
|--------------------------------------------------------------------------
| Mutation Types
|--------------------------------------------------------------------------
*/
export const SET_PARAMETROS = 'SET_PARAMETROS';
export const UNSET_PARAMETROS = 'UNSET_PARAMETROS';

/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/
const initialState = {
    parametros: {
        modelo: ""
    },
};

/*
|--------------------------------------------------------------------------
| Mutations
|--------------------------------------------------------------------------
*/
const mutations = {
	[SET_PARAMETROS](state, payload) {
        state.parametros = payload.parametros;
	},
	[UNSET_PARAMETROS](state, payload) {
        state.parametros = {};
	}
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const actions = {
	setParametros: (context, parametros) => {
		context.commit(SET_PARAMETROS, {parametros})
	},
	unsetParametros: (context) => {
		context.commit(UNSET_PARAMETROS);
	}
};

/*
|--------------------------------------------------------------------------
| Getters
|--------------------------------------------------------------------------
*/
const getters = {
    getParamSeleccion: (state) =>{
        return state.parametros
    },
    getParamModelo: (state) =>{
        return state.modelo
    }

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
