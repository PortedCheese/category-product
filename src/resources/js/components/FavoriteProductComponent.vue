<template>
    <div class="favorite-product">
        <button type="button"
                @click="changeProduct"
                :disabled="loading"
                :class="simple ? '' : 'favorite-product__btn_text'"
                class="btn btn-link favorite-product__btn">
            <svg class="favorite-product__ico" v-if="isActive">
                <use xlink:href="#heart-fill"></use>
            </svg>
            <svg class="favorite-product__ico favorite-product__ico_border" v-else>
                <use xlink:href="#heart-border"></use>
            </svg>
            <span v-if="! simple" class="favorite-product__text">{{ buttonText }}</span>
        </button>
    </div>
</template>

<script>
import categoryProductEventBus from './categoryProductEventBus'
    export default {
        name: "FavoriteProductComponent",

        props: {
            simple: {
                type: Boolean,
                default: false
            },
            addUrl: {
                type: String,
                required: true
            },
            removeUrl: {
                type: String,
                required: true
            },
            current: {
                type: Array,
                required: true
            },
            productId: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                loading: false,
                items: [],
            }
        },

        mounted() {
            categoryProductEventBus.$on("change-favorite", this.changeItems);
        },

        created() {
            this.items = this.current;
        },

        computed: {
            isActive() {
                let active = false;
                for (let index in this.items) {
                    if (this.items.hasOwnProperty(index) && this.items[index] === this.productId) {
                        active = true;
                    }
                }
                return active;
            },

            buttonText() {
                return this.isActive ? "В избранном" : "В избранное";
            }
        },

        methods: {
            changeProduct() {
                if (this.isActive) {
                    this.removeProduct();
                }
                else {
                    this.addProduct();
                }
            },

            changeItems(items) {
                this.items = items;
            },

            addProduct() {
                this.loading = true;
                axios
                    .post(this.addUrl, {})
                    .then(response => {
                        let data = response.data;
                        if (data.hasOwnProperty("items")) {
                            this.items = data["items"];
                            categoryProductEventBus.$emit("change-favorite", data["items"]);
                        }
                    })
                    .catch(error => {

                    })
                    .then(() => {
                        this.loading = false;
                    })
            },

            removeProduct() {
                this.loading = true;
                axios
                    .delete(this.removeUrl)
                    .then(response => {
                        let data = response.data;
                        if (data.hasOwnProperty("items")) {
                            this.items = data["items"];
                            categoryProductEventBus.$emit("change-favorite", data["items"]);
                        }
                    })
                    .catch(error => {

                    })
                    .then(() => {
                        this.loading = false;
                    })
            }
        }
    }
</script>

<style scoped>

</style>