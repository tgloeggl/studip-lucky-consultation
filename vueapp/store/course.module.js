import ApiService from "@/common/api.service";


const state = {
   cid: null,
   pools: null,
   dates: null,
   mydates: null,
   waitinglist: null,
   currentUser: {},
   infotext: null
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

    mydates(state) {
        return state.mydates;
    },

    waitinglist(state) {
        return state.waitinglist;
    },

    currentUser(state) {
        return state.currentUser
    },

    infotext(state) {
        return state.infotext
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
    },

    async loadWaitingList({ commit, dispatch}) {
        return ApiService.get('course/' + state.cid + '/waitinglist')
            .then(({ data }) => {
                commit('setWaitingList', data.data);
            });
    },

    async addToWaitingList({ commit, dispatch}, id) {
        return ApiService.put('course/' + state.cid + '/waitinglist/' + id)
            .then(({ data }) => {
                commit('setWaitingList', data.data);
            });
    },

    async removeFromWaitingList({ commit, dispatch}, id) {
        return ApiService.delete('course/' + state.cid + '/waitinglist/' + id)
            .then(({ data }) => {
                commit('setWaitingList', data.data);
            });
    },

    async addPool({ dispatch, commit, state }, pool) {
        return ApiService.post('course/' + state.cid + '/pools', pool)
            .then(({ data }) => {
                commit('setPools', data.data);
            });
    },

    async editPool({ dispatch, commit, state }, pool) {
        return ApiService.put('course/' + state.cid + '/pools', pool)
            .then(({ data }) => {
                commit('setPools', data.data);
            });
    },

    async deletePool({ dispatch, commit, state }, id) {
        return ApiService.delete('course/' + state.cid + '/pools/' + id)
            .then(({ data }) => {
                commit('setPools', data.data);
            });
    },

    async loadPools({ dispatch, commit, state }, pool) {
        return ApiService.get('course/' + state.cid + '/pools', pool)
            .then(({ data }) => {
                commit('setPools', data.data);
            });
    },

    async addDate({ dispatch, commit, state }, date) {
        return ApiService.post('course/' + state.cid + '/dates', date)
            .then(({ data }) => {
                commit('setDates', data.data);
            });
    },

    async editDate({ dispatch, commit, state }, date) {
        return ApiService.put('course/' + state.cid + '/dates', date)
            .then(({ data }) => {
                commit('setDates', data.data);
            });
    },

    async deleteDate({ dispatch, commit, state }, id) {
        return ApiService.delete('course/' + state.cid + '/dates/' + id)
            .then(({ data }) => {
                commit('setDates', data.data);
            });
    },

    async deleteUserFromDate({ dispatch, commit, state }, id) {
        return ApiService.delete('course/' + state.cid + '/dates/' + id + '/user')
            .then(({ data }) => {
                commit('setDates', data.data);
            });
    },

    async loadDates({ dispatch, commit, state }, date) {
        return ApiService.get('course/' + state.cid + '/dates', date)
            .then(({ data }) => {
                commit('setDates', data.data);
            });
    },

    async loadMyDates({ dispatch, commit, state }) {
        return ApiService.get('course/' + state.cid + '/mydates')
            .then(({ data }) => {
                commit('setMyDates', data.data);
            });
    },

    async loadInfotext({ dispatch, commit }) {
        return ApiService.get('course/' + state.cid + '/infotext')
            .then(({ data }) => {
                commit('setInfotext', data.infotext);
            });
    },

    async updateInfotext({ dispatch, commit }, infotext) {
        commit('setInfotext', infotext);

        return ApiService.put('course/' + state.cid + '/infotext', {
                infotext: infotext
            }).then(({ data }) => {
                commit('setInfotext', data.infotext);
            });
    }
}

const mutations = {
    updateCID(state, cid) {
        state.cid = cid;
    },

    setCurrentUser(state, data) {
        state.currentUser = data;
    },

    setPools(state, pools) {
        state.pools = pools;
    },

    setDates(state, dates) {
        state.dates = dates;
    },

    setMyDates(state, mydates) {
        state.mydates = mydates;
    },

    setWaitingList(state, waitinglist) {
        state.waitinglist = waitinglist;
    },

    setInfotext(state, infotext) {
        state.infotext = infotext;
    },
}


export default {
    state,
    getters,
    mutations,
    actions
}
