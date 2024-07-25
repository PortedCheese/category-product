<template>
    <a :href="favoriteUrl" :class="baseClass" class="favorite-state">
        <span class="favorite-state__cover">
            <svg class="favorite-state__ico">
                <use xlink:href="#heart-border"></use>
            </svg>
            <span v-if="count > 0" class="badge badge-primary favorite-state__count">{{ count }}</span>
        </span>
        <span class="favorite-state__title">
            Избранное
        </span>
    </a>
</template>

<script>
import categoryProductEventBus from './categoryProductEventBus'

    export default {
        name: "FavoriteStateComponent",

        props: {
            baseClass: {
                type: String,
                required: false,
                default: "nav-link"
            },

            favoriteItems: {
                type: Array,
                required: true
            },

            favoriteUrl: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                count: 0,
            }
        },

        created() {
            this.count = this.favoriteItems.length;
        },

        mounted() {
            categoryProductEventBus.$on("change-favorite", this.changeFavoriteData);
        },

        methods: {
            changeFavoriteData(favorite) {
                this.count = favorite.length;
            }
        }
    }
</script>

<style scoped>

</style>