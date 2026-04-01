import { createStore } from "vuex";
import order from "./modules/order";
import reference from "./modules/reference";
import orderForm from "./modules/orderForm";

const store = createStore({
    modules: {
        order,
        reference,
        orderForm
    },

    strict: process.env.NODE_ENV !== "production",
});

export default store;
