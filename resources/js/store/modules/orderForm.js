// resources/js/store/modules/orderForm.js

import axios from "axios";
import { toast } from "vue3-toastify";
import { route } from "ziggy-js";
import { router } from "@inertiajs/vue3";

const getDefaultForm = () => ({
    customer_id: null,
    user_id: null,
    currency_id: null,
    order_code: "",
    order_date: new Date().toISOString().split("T")[0], // hôm nay
    exchange_rate: 1,
    status: "pending",
    payment_status: "pending",
    delivery_cost: 0,
    discount: 0,
    note: "",
    details: [],
    total_amount: 0,
});

const getDefaultDetail = () => ({
    product_id: null,
    quantity: 1,
    price: 0,
    vat: 0,
    discount: 0,
    amount: 0,
});

export default {
    namespaced: true,

    state: {
        formData: getDefaultForm(),
        validationErrors: {},
        loading: false,
        isEditMode: false,
    },

    getters: {
        formData: (state) => state.formData,
        validationErrors: (state) => state.validationErrors,
        loading: (state) => state.loading,
        isEditMode: (state) => state.isEditMode,

        detailsTotal: (state) => {
            return state.formData.details.reduce(
                (sum, d) => sum + (parseFloat(d.amount) || 0),
                0
            );
        },

        finalAmount: (state, getters) => {
            const subtotal = getters.detailsTotal;
            const delivery = parseFloat(state.formData.delivery_cost) || 0;
            const discount = parseFloat(state.formData.discount) || 0;
            return subtotal + delivery - discount;
        },

        isValid: (state, getters) => {
            return (
                state.formData.customer_id &&
                state.formData.order_date &&
                state.formData.details.length > 0 &&
                state.formData.details.every(
                    (d) => d.product_id && d.quantity > 0 && d.price >= 0
                )
            );
        },
    },

    mutations: {
        SET_FORM_DATA(state, data) {
            state.formData = { ...state.formData, ...data };
        },

        SET_DETAILS(state, details) {
            state.formData.details = details;
        },

        RESET_FORM(state) {
            state.formData = getDefaultForm();
            state.validationErrors = {};
            state.isEditMode = false;
        },

        SET_EDIT_MODE(state, bool) {
            state.isEditMode = bool;
        },

        ADD_DETAIL(state) {
            state.formData.details.push({ ...getDefaultDetail() });
        },

        REMOVE_DETAIL(state, index) {
            state.formData.details.splice(index, 1);
        },

        UPDATE_DETAIL(state, { index, field, value }) {
            if (state.formData.details[index]) {
                state.formData.details[index][field] = value;
            }
        },

        CALCULATE_DETAIL_AMOUNT(state, index) {
            const detail = state.formData.details[index];
            if (!detail) return;

            const qty = parseFloat(detail.quantity) || 0;
            const price = parseFloat(detail.price) || 0;
            const vat = parseFloat(detail.vat) || 0;
            const discount = parseFloat(detail.discount) || 0;

            const amount = qty * price * (1 + vat / 100) - discount;
            detail.amount = Math.max(0, amount);
        },

        CALCULATE_ORDER_TOTALS(state) {
            // Tính lại từng dòng
            state.formData.details.forEach((_, index) => {
                const detail = state.formData.details[index];
                const qty = parseFloat(detail.quantity) || 0;
                const price = parseFloat(detail.price) || 0;
                const vat = parseFloat(detail.vat) || 0;
                const discount = parseFloat(detail.discount) || 0;

                const amount = qty * price * (1 + vat / 100) - discount;
                detail.amount = Math.max(0, amount);
            });

            // Tính tổng cuối cùng
            const subtotal = state.formData.details.reduce(
                (sum, d) => sum + (parseFloat(d.amount) || 0),
                0
            );
            const delivery = parseFloat(state.formData.delivery_cost) || 0;
            const discount = parseFloat(state.formData.discount) || 0;

            const total = subtotal + delivery - discount;
            state.formData.total_amount = Math.max(0, total); // ← CẬP NHẬT TỔNG
        },

        SET_VALIDATION_ERRORS(state, errors) {
            state.validationErrors = errors;
        },

        CLEAR_VALIDATION_ERROR(state, field) {
            delete state.validationErrors[field];
        },

        SET_LOADING(state, loading) {
            state.loading = loading;
        },
    },

    actions: {
        reset({ commit }) {
            commit("RESET_FORM");
        },

        addDetail({ commit }) {
            commit("ADD_DETAIL");
        },

        removeDetail({ commit }, index) {
            commit("REMOVE_DETAIL", index);
        },

        updateField({ commit }, { field, value }) {
            commit("SET_FORM_DATA", { [field]: value });
        },

        updateDetail({ commit }, { index, field, value }) {
            commit("UPDATE_DETAIL", { index, field, value });
        },

        calculateDetailAmount(index) {
            commit("CALCULATE_DETAIL_AMOUNT", index);
            commit("CALCULATE_ORDER_TOTALS");
        },

        async loadOrder({ commit, dispatch }, orderId) {
            commit("SET_LOADING", true);
            try {
                const response = await axios.get(`/orders/${orderId}`);
                const order = response.data.data;

                // Map dữ liệu từ server vào form
                commit("SET_FORM_DATA", {
                    customer_id: order.customer_id,
                    user_id: order.user_id,
                    currency_id: order.currency_id || 1,
                    order_code: order.order_code,
                    order_date: order.order_date,
                    exchange_rate: order.exchange_rate || 1,
                    status: order.status,
                    payment_status: order.payment_status,
                    delivery_cost: order.delivery_cost || 0,
                    discount: order.discount || 0,
                    note: order.note || "",
                });

                // Map chi tiết
                const details = order.details.map((d) => ({
                    product_id: d.product_id,
                    quantity: d.quantity,
                    price: d.price,
                    vat: d.vat || 0,
                    discount: d.discount || 0,
                    amount: d.amount,
                }));

                commit("SET_DETAILS", details);
                commit("SET_EDIT_MODE", true);
            } catch (error) {
                console.error("Load order error:", error);
                throw error;
            } finally {
                commit("SET_LOADING", false);
            }
        },

        async submit({ state, commit }) {
            commit("SET_LOADING", true);
            commit("SET_VALIDATION_ERRORS", {});

            const url = state.isEditMode
                ? route("orders.update", state.formData.id) // ✅ Dùng route helper
                : route("orders.store");

            const method = state.isEditMode ? "put" : "post";

            // ✅ SỬA: Dùng router thay vì Inertia
            router[method](url, state.formData, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(
                        state.isEditMode
                            ? "Cập nhật đơn hàng thành công!"
                            : "Tạo đơn hàng thành công!"
                    );
                    commit("RESET_FORM");
                },
                onError: (errors) => {
                    commit("SET_VALIDATION_ERRORS", errors);
                    toast.error("Vui lòng kiểm tra lại thông tin!");
                },
                onFinish: () => {
                    commit("SET_LOADING", false);
                },
            });
        },
    },
};
