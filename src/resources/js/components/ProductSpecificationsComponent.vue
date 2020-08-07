<template>
    <div class="col-12">
        <div class="card">
            <add-new :available="this.available"
                     v-on:add-new-spec="getList"
                     :post-url="postUrl"></add-new>
            <div class="card-body">
                <div class="alert alert-danger" role="alert" v-if="Object.keys(errors).length">
                    <template v-for="field in errors">
                        <template v-for="error in field">
                            <span>{{ error }}</span>
                            <br>
                        </template>
                    </template>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Значения</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="specification in specifications">
                            <td>{{ specification.title }}</td>
                            <td>
                                <ul class="list-unstyled">
                                    <li v-for="value in specification.values">
                                        {{ value }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" @click="editSpecification(specification)" class="btn btn-primary">
                                        <i class="far fa-edit"></i>
                                    </button>
                                    <button type="button" @click="deleteSpecification(specification)" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <edit-form v-on:update-spec="getList"></edit-form>
    </div>
</template>

<script>
    import AddNewSpecValue from './AddProductSpecificationValueComponent'
    import EditSpecValue from './EditProductSpecificationValue'
    export default {
        name: "ProductSpecificationsComponent",

        components: {
            "add-new": AddNewSpecValue,
            "edit-form": EditSpecValue
        },

        props: {
            getUrl: {
                required: true,
                type: String
            },
            postUrl: {
                required: true,
                type: String
            }
        },

        data() {
            return {
                loading: false,
                available: [],
                specifications: [],
                errors: [],
            }
        },

        created() {
            this.getList();
        },

        methods: {
            // Получить список текущих характеристик.
            getList() {
                this.loading = true;
                axios
                    .get(this.getUrl)
                    .then(response => {
                        let data = response.data;
                        this.available = data.available;
                        this.specifications = data.items;
                    })
                    .catch(error => {
                        let data = error.response.data;
                        Swal.fire({
                            type: "error",
                            title: "Усп...",
                            text: "Что то пошло не так",
                            footer: data.message
                        })
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            },

            editSpecification(specification) {
                this.$emit("new-edit-show", specification);
            },

            deleteSpecification(specification) {
                Swal.fire({
                    title: "Вы уверены?",
                    text: "Это действие будет невозможно отменить",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Да, удалить!",
                    cancelButtonText: "Отмена",
                }).then((result) => {
                    if (result.value) {
                        this.loading = true;
                        this.errors = [];
                        axios
                            .delete(specification.deleteUrl)
                            .then(response => {
                                let data = response.data;
                                if (data.success) {
                                    this.getList();
                                    Swal.fire({
                                        position: "top-end",
                                        type: "success",
                                        title: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                }
                            })
                            .catch(error => {
                                let data = error.response.data;
                                if (data.hasOwnProperty("errors")) {
                                    this.errors = data.errors;
                                }
                                else {
                                    Swal.fire({
                                        type: "error",
                                        title: "Упс...",
                                        text: "Что то пошло не так",
                                        footer: data.message
                                    })
                                }
                            })
                            .finally(() => {
                                this.loading = false;
                            })
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>