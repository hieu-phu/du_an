// resources/js/store/modules/reference.js

import axios from "axios";

export default {
  namespaced: true,

  state: {
    customers: [],
    users: [],
    products: [],
    currencies: [],
    companies: [],
    loaded: {
      customers: false,
      users: false,
      products: false,
      currencies: false,
      companies: false,
    },
  },

  getters: {
    // Luôn trả về mảng, tránh lỗi undefined.map
    customers: (state) => state.customers || [],
    users: (state) => state.users || [],
    products: (state) => state.products || [],
    currencies: (state) => state.currencies || [],
    companies: (state) => state.companies || [],

    customerById: (state) => (id) => state.customers.find((c) => c.id === id),
    userById: (state) => (id) => state.users.find((u) => u.id === id),
    productById: (state) => (id) => state.products.find((p) => p.id === id),
    currencyById: (state) => (id) => state.currencies.find((c) => c.id === id),
    companyById: (state) => (id) => state.companies.find((c) => c.id === id),
  },

  mutations: {
    SET_CUSTOMERS(state, payload) {
      state.customers = payload;
      state.loaded.customers = true;
    },
    SET_USERS(state, payload) {
      state.users = payload;
      state.loaded.users = true;
    },
    SET_PRODUCTS(state, payload) {
      state.products = payload;
      state.loaded.products = true;
    },
    SET_CURRENCIES(state, payload) {
      state.currencies = payload;
      state.loaded.currencies = true;
    },
    SET_COMPANIES(state, payload) {
      state.companies = payload;
      state.loaded.companies = true;
    },
  },

  actions: {
    async fetchCustomers({ commit }) {
      const { data } = await axios.get("/ban-hang/customers/all");
      commit("SET_CUSTOMERS", data.data || data); // chấp nhận cả 2 kiểu trả về
    },

    async fetchUsers({ commit }) {
      return axios.get("/ban-hang/users/all").then(({ data }) => {
        commit("SET_USERS", data.data || data);
      });
    },

    async fetchProducts({ commit }) {
      const { data } = await axios.get("/ban-hang/products/all");
      commit("SET_PRODUCTS", data.data || data);
    },

    async fetchCurrencies({ commit }) {
      const { data } = await axios.get("/ban-hang/currencies/all");
      commit("SET_CURRENCIES", data.data || data);
    },

    async fetchCompanies({ commit }) {
      const { data } = await axios.get("/ban-hang/companies/all");
      commit("SET_COMPANIES", data.data || data);
    },

    async fetchAll({ dispatch }) {
      await Promise.allSettled([
        dispatch("fetchCustomers"),
        dispatch("fetchUsers"),
        dispatch("fetchProducts"),
        dispatch("fetchCurrencies"),
        dispatch("fetchCompanies"),
      ]);
    },
  },
};