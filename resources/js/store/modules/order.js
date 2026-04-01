import axios from "axios";

export default {
    namespaced: true,

    state: {
        orders: [],
        currentOrder: null,
        filters: {
            search: "",
            status: "",
            payment_status: "",
            customer_id: null,
            date_from: null,
            date_to: null,
        },
        pagination: {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1,
        },
        loading: false,
        error: null,
    },

    getters: {
        orders: (state) => state.orders,
        currentOrder: (state) => state.currentOrder,
        loading: (state) => state.loading,
        filters: (state) => state.filters,
        pagination: (state) => state.pagination,

        orderTotal: (state) => (order) => {
            if (!order || !order.details) return 0;
            return order.details.reduce(
                (sum, detail) => sum + parseFloat(detail.amount || 0),
                0
            );
        },

        ordersByStatus: (state) => (status) => {
            return state.orders.filter((order) => order.status === status);
        },

        statistics: (state) => {
            const stats = {
                total: state.orders.length,
                active: 0,
                inactive: 0,
                pending_payment: 0,
                partial_payment: 0,
                paid: 0,
                total_amount: 0,
            };

            state.orders.forEach((order) => {
                if (order.status === "active") stats.active++;
                if (order.status === "inactive") stats.inactive++;
                if (order.payment_status === "pending") stats.pending_payment++;
                if (order.payment_status === "partial") stats.partial_payment++;
                if (order.payment_status === "paid") stats.paid++;
                stats.total_amount += parseFloat(order.final_amount || 0);
            });

            return stats;
        },
    },

    mutations: {
        SET_ORDERS(state, orders) {
            state.orders = orders;
        },

        SET_CURRENT_ORDER(state, order) {
            state.currentOrder = order;
        },

        ADD_ORDER(state, order) {
            state.orders.unshift(order);
        },

        UPDATE_ORDER(state, updatedOrder) {
            const index = state.orders.findIndex(
                (o) => o.id === updatedOrder.id
            );
            if (index !== -1) {
                state.orders.splice(index, 1, updatedOrder);
            }
        },

        DELETE_ORDER(state, orderId) {
            state.orders = state.orders.filter((o) => o.id !== orderId);
        },

        SET_FILTERS(state, filters) {
            state.filters = { ...state.filters, ...filters };
        },

        RESET_FILTERS(state) {
            state.filters = {
                search: "",
                status: "",
                payment_status: "",
                customer_id: null,
                date_from: null,
                date_to: null,
            };
        },

        SET_PAGINATION(state, pagination) {
            state.pagination = { ...state.pagination, ...pagination };
        },

        SET_LOADING(state, loading) {
            state.loading = loading;
        },

        SET_ERROR(state, error) {
            state.error = error;
        },
    },

    actions: {
        // lấy danh sách orders

        async fetchOrders({ commit, state }, params = {}) {
            commit("SET_LOADING", true);
            commit("SET_ERROR", null);

            try {
                const response = await axios.get("/orders", {
                    params: {
                        page: state.pagination.current_page,
                        per_page: state.pagination.per_page,
                        ...state.filters,
                        ...params,
                    },
                });

                commit("SET_ORDERS", response.data.data);
                commit("SET_PAGINATION", {
                    current_page: response.data.current_page,
                    per_page: response.data.per_page,
                    total: response.data.total,
                    last_page: response.data.last_page,
                });

                return response.data;
            } catch (error) {
                commit(
                    "SET_ERROR",
                    error.response?.data?.message || "Có lỗi xảy ra"
                );
                throw error;
            } finally {
                commit("SET_LOADING", false);
            }
        },

        async fetchOrder({ commit }, orderId) {
            commit("SET_LOADING", true);
            try {
                const response = await axios.get(`/orders/${orderId}`);
                commit("SET_CURRENT_ORDER", response.data.data);
                return response.data.data;
            } catch (error) {
                commit(
                    "SET_ERROR",
                    error.response?.data?.message || "Có lỗi xảy ra"
                );
                throw error;
            } finally {
                commit("SET_LOADING", false);
            }
        },

        async deleteOrder({ commit, dispatch }, orderId) {
            try {
                await axios.delete(`/orders/${orderId}`);
                commit("DELETE_ORDER", orderId);
                dispatch("notification/success", "Xóa đơn hàng thành công", {
                    root: true,
                });
            } catch (error) {
                dispatch(
                    "notification/error",
                    error.response?.data?.message || "Có lỗi xảy ra",
                    { root: true }
                );
                throw error;
            }
        },

        updateFilters({ commit, dispatch }, filters) {
            commit("SET_FILTERS", filters);
            commit("SET_PAGINATION", { current_page: 1 });
            dispatch("fetchOrders");
        },

        resetFilters({ commit, dispatch }) {
            commit("RESET_FILTERS");
            commit("SET_PAGINATION", { current_page: 1 });
            dispatch("fetchOrders");
        },

        changePage({ commit, dispatch }, page) {
            commit("SET_PAGINATION", { current_page: page });
            dispatch("fetchOrders");
        },
    },
};
