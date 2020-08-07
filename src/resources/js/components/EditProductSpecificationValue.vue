<template>
    <div class="modal fade" id="editSpecModal" tabindex="-1" aria-labelledby="editSpecModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSpecModalLabel">Редактировать характеристику</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="alert alert-danger" role="alert" v-if="Object.keys(errors).length">
                            <template v-for="field in errors">
                                <template v-for="error in field">
                                    <span>{{ error }}</span>
                                    <br>
                                </template>
                            </template>
                        </div>

                        <div class="form-group">
                            <div class="input-group mb-3" v-for="(item, index) in currentValues" :key="index">
                                <input type="text"
                                       v-model="item.text"
                                       class="form-control"
                                       placeholder="Значение"
                                       aria-label="Значение">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-danger"
                                            @click="removeValue(index)"
                                            :disabled="loading"
                                            type="button">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <input type="text"
                                       v-model="newValue"
                                       class="form-control"
                                       placeholder="Значение"
                                       aria-label="Значение">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success"
                                            :disabled="! newValue.length || loading"
                                            @click="addNewValue"
                                            type="button">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button"
                            class="btn btn-primary"
                            @click="updateValues"
                            :disabled="! currentValues.length || loading">
                        Обновить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EditProductSpecificationValue",

        data() {
            return {
                specification: {},
                errors: [],
                newValue: "",
                loading: false,
                currentValues: [],
            }
        },

        created() {
            this.$parent.$on("new-edit-show", this.initEditable);
        },

        computed: {
            newSpecValues() {
                let values = [];
                for (let item in this.currentValues) {
                    if (this.currentValues.hasOwnProperty(item)) {
                        values.push(this.currentValues[item].text);
                    }
                }
                return values;
            }
        },

        methods: {
            initEditable(specification) {
                this.specification = specification;
                this.currentValues = [];
                $("#editSpecModal").modal("show");
                if (specification.hasOwnProperty("values")) {
                    for (let item in this.specification.values) {
                        if (this.specification.values.hasOwnProperty(item)) {
                            this.currentValues.push({
                                text: this.specification.values[item],
                            })
                        }
                    }
                }
            },
            // Удалить значение.
            removeValue(index) {
                this.currentValues.splice(index, 1);
            },
            // Добавить новое значение в список.
            addNewValue() {
                this.currentValues.push({
                    text: this.newValue
                });
                this.newValue = "";
            },
            updateValues() {
                this.loading = true;
                this.errors = [];
                axios
                    .put(this.specification.updateUrl, {
                        values: this.newSpecValues
                    })
                    .then(response => {
                        let data = response.data;
                        if (data.success) {
                            $("#editSpecModal").modal("hide");
                            Swal.fire({
                                position: "top-end",
                                type: "success",
                                title: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            this.$emit("update-spec");
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
        }
    }
</script>

<style scoped>

</style>