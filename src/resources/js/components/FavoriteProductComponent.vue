<template>
    <div class="favorite-product">
        <button type="button"
                @click="changeProduct"
                :disabled="loading"
                class="btn btn-link favorite-product__btn">
            <svg class="favorite-product__ico">
                <use xlink:href="#heart-fill" v-if="isActive"></use>
                <use xlink:href="#heart-border" v-else></use>
            </svg>
        </button>
    </div>
</template>

<script>
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

            addProduct() {
                this.loading = true;
                axios
                    .post(this.addUrl, {})
                    .then(response => {
                        let data = response.data;
                        if (data.hasOwnProperty("items")) {
                            this.items = data["items"];
                            this.$root.$emit("change-favorite", data["items"]);
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
                            this.$root.$emit("change-favorite", data["items"]);
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