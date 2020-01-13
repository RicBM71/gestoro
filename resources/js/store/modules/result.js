/*
|--------------------------------------------------------------------------
| Mutation Types
|--------------------------------------------------------------------------
*/
export const SET_RESULT = 'SET_RESULT';
export const UNSET_RESULT = 'UNSET_RESULT';

/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/
const initialState = {
    lineas: []
};

/*
|--------------------------------------------------------------------------
| Mutations
|--------------------------------------------------------------------------
*/
const mutations = {
	[SET_RESULT](state, payload) {
        state.lineas = payload.result;
	},
	[UNSET_RESULT](state, payload) {
        state.lineas = [];
	}
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const actions = {
	setResult: (context, result) => {
		context.commit(SET_RESULT, {result})
	},
	unsetResult: (context) => {
		context.commit(UNSET_RESULT);
	}
};

/*
|--------------------------------------------------------------------------
| Getters
|--------------------------------------------------------------------------
*/
const getters = {
    getLineasIndex: (state) =>{
        return state.lineas
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
