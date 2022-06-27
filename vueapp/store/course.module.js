import ApiService from "@/common/api.service";


const state = {
   cid: null,
   pools: null,
   dates: null,
   currentUser: {}
}

const getters = {
    cid(state) {
        return state.cid;
    },

    pools(state) {
        return state.pools;
    },

    dates(state) {
        return state.dates;
    },

    currentUser(state) {
        return state.currentUser
    }
}


const actions = {
    setCID ({ commit }, cid) {
        commit('updateCID', cid);
    },


    async loadCurrentUser({ commit, dispatch}) {
        return ApiService.get('user')
            .then(({ data }) => {
                commit('setCurrentUser', data.data);
            });
    }
}

const mutations = {
    updateCID(state, cid) {
        state.cid = cid;
    },

    setCurrentUser(state, data) {
        state.currentUser = data;
    }
}


export default {
    state,
    getters,
    mutations,
    actions
}
