import ApiService from "@/common/api.service";


const state = {
   cid: null
}

const getters = {
    cid(state) {
        return state.cid;
    }
}


const actions = {
    setCID ({ commit }, cid) {
        commit('updateCID', cid);
    }
}

const mutations = {
    updateCID(state, cid) {
        state.cid = cid;
    }
}


export default {
    state,
    getters,
    mutations,
    actions
}
