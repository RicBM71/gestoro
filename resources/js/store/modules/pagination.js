/*
|--------------------------------------------------------------------------
| Mutation Types
|--------------------------------------------------------------------------
*/
export const SET_PAGINATION = 'SET_PAGINATION';
export const UNSET_PAGINATION = 'UNSET_PAGINATION';

/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/
const initialState = {
    model: null,
    search: null,
    descending: null,
    page: 1,
    rowsPerPage: 10,
    sortBy: null
};

/*
|--------------------------------------------------------------------------
| Mutations
|--------------------------------------------------------------------------
*/
const mutations = {
	[SET_PAGINATION](state, payload) {
        state.model = payload.pagination.model;
        state.search = payload.pagination.search;
		state.descending = payload.pagination.descending;
        state.page = payload.pagination.page;
        state.rowsPerPage = payload.pagination.rowsPerPage;
        state.sortBy = payload.pagination.sortBy;
	},
	[UNSET_PAGINATION](state, payload) {
        state.model = null;
        state.search = null;
		state.descending = null;
        state.page = 1;
        state.rowsPerPage = 5;
        state.sortBy = null;
	}
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const actions = {
	setPagination: (context, pagination) => {
		context.commit(SET_PAGINATION, {pagination})
	},
	unsetPagination: (context) => {
		context.commit(UNSET_PAGINATION);
	}
};

/*
|--------------------------------------------------------------------------
| Getters
|--------------------------------------------------------------------------
*/
const getters = {
    getPagination: (state) =>{
        return state
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
