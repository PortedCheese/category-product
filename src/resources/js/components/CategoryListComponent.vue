<template>
    <div class="row">
        <div class="col-12">
            <nested-draggable :items="changeable" v-on:changed="checkMove" />

            <button class="btn btn-success position-fixed fixed-bottom mx-auto mb-3"
                    v-if="weightChanged"
                    @click="changeOrder"
                    :disabled="loading"
                    :class="weightChanged ? 'animated bounceIn' : ''">
                Сохранить структуру
            </button>
        </div>
    </div>
</template>

<script>
    import nestedDraggable from "./CategoryItemComponent";
    export default {
        name: "admin-category-nested",
        components: {
            nestedDraggable
        },
        props: {
            structure: {
                type: Array,
                required: true,
            },
            updateUrl: {
                type: String,
                required: true,
            }
        },
        data() {
            return {
                changeable: [],
                weightChanged: false,
                loading: false,
            };
        },
        created() {
            this.changeable = this.structure;
        },
        methods: {
            checkMove() {
                this.weightChanged = true;
            },

            changeOrder() {
                this.loading = true;
                axios
                    .put(this.updateUrl, {
                        items: this.changeable
                    })
                    .then(response => {
                        let result = response.data;
                        this.weightChanged = false;
                        Swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: result,
                            showConfirmButton: false,
                            timer: 2000
                        })
                    })
                    .catch(error => {
                        let data = error.response.data;
                        let message = "Упс что то пошло не так";
                        if (data.hasOwnProperty("message")) {
                            message = data.message;
                        }
                        console.log(data);
                        Swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: message,
                            showConfirmButton: false,
                            timer: 2000
                        })
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            }
        }
    };
</script>
<style scoped></style>