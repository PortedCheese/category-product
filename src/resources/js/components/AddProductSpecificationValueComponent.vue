<template>
    <div class="card-header">
        <button type="button"
                :disabled="! available.length || loading"
                class="btn btn-success"
                data-toggle="modal"
                data-target="#newSpecModal">
            Добавить
        </button>

        <div class="modal fade"
             v-if="available.length"
             id="newSpecModal"
             tabindex="-1"
             aria-labelledby="newSpecModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newSpecModalLabel">Добавить характеристику</h5>
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
                                <label for="type">Характеристика</label>
                                <select name="type"
                                        id="type"
                                        v-model="chosenSpecId"
                                        class="form-control custom-select">
                                    <option value="">Выберите...</option>
                                    <option v-for="item in available" :value="item.id">
                                        {{ item.title }}
                                    </option>
                                </select>
                                <small v-if="chosenSpec" class="form-text text-muted">
                                    <span v-if="chosenSpec.filter">В фильтре</span>
                                    <span v-else>Не в фильтре</span>
                                </small>
                                <small v-else class="form-text text-muted">Выберите характеристику</small>
                            </div>
                            <div class="form-group" v-if="chosenSpec">
                                <div class="input-group mb-3" v-for="(item, index) in newValues" :key="index">
                                    <input type="text"
                                           name="text"
                                           v-model="item.text"
                                           class="form-control"
                                           placeholder="Значение"
                                           aria-label="Значение">
                                    <input v-if="chosenSpec.type"
                                           v-model="item.code"
                                           type="text"
                                           name="code"
                                           class="form-control"
                                           placeholder="Код"
                                           aria-label="Код">
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
                                           name="text"
                                           v-model="newValue"
                                           class="form-control"
                                           placeholder="Значение"
                                           aria-label="Значение">
                                    <input v-if="chosenSpec.type"
                                           v-model="newCode"
                                          type="text"
                                          name="code"
                                          class="form-control"
                                          placeholder="Код"
                                          aria-label="Код">
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
                                @click="postNewValues"
                                :disabled="! newValues.length || loading">
                            Добавить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AddProductSpecificationValueComponent",

        props: {
            available: {
              required: true,
              type: Array,
            },
            postUrl: {
                required: true,
                type: String
            }
        },

        data() {
            return {
                loading: false,
                chosenSpecId: "",
                newValues: [],
                newValue: "",
                newCode: "",
                errors: [],
            }
        },

        computed: {
            chosenSpec() {
                let choose = false;
                for (let item in this.available) {
                    if (this.available.hasOwnProperty(item)) {
                        let id = this.available[item].id;
                        if (id === this.chosenSpecId) {
                            choose = this.available[item];
                        }
                    }
                }
                return choose;
            },
            newSpecValues() {
                let values = [];
                for (let item in this.newValues) {
                    if (this.newValues.hasOwnProperty(item)) {
                        values.push({'value':this.newValues[item].text , 'code': this.newValues[item].code});
                    }
                }
                return values;
            }
        },

        methods: {
            // Добавить новое значение в список.
            addNewValue() {
                this.newValues.push({
                    text: this.newValue,
                    code: this.newCode,
                });
                this.newValue = "";
                this.newCode = "";
            },
            // Удалить значение.
            removeValue(index) {
                this.newValues.splice(index, 1);
            },
            // Отправить на сервер.
            postNewValues() {
                this.loading = true;
                this.errors = [];
                axios
                    .post(this.postUrl, {
                        values: this.newSpecValues,
                        id: this.chosenSpecId
                    })
                    .then(response => {
                        let data = response.data;
                        if (data.success) {
                            $("#newSpecModal").modal("hide");
                            Swal.fire({
                                position: "top-end",
                                type: "success",
                                title: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            this.newValues = [];
                            this.$emit("add-new-spec");
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
            },
        }
    }
</script>

<style scoped>

</style>