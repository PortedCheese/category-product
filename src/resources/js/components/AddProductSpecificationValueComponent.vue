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
                                <div v-if="availableValues[chosenSpecId]" class="input-group mb-3">
                                    <select class="form-control custom-select"
                                            name="valuesList"
                                            :id="chosenSpecId"
                                            v-model="chosenValue"
                                            v-on:change="fillNewValue">
                                        <option value="" selected>Выберите из существующих...</option>
                                        <option v-for="(option, index) in availableValues[chosenSpecId]"
                                                :value="option.value"
                                                :disabled="disableCurrentValue(option.value, option.code)"
                                        >
                                          {{ option.value }} {{ option.code ? "["+option.code+"]": "" }}
                                        </option>
                                    </select>
                                </div>
                                <div class="input-group mb-3" v-for="(item, index) in newValues" :key="index">
                                    <input type="text"
                                           name="text"
                                           v-model="item.text"
                                           v-on:input="disableCurrentValue(item.text,item.code) ? disabledFire(item.text) : true"
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
                                           v-on:input="disableCurrentValue(newValue,newCode) ? disabledFire(newValue) : true"
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
            currentValues:{
              required: true,
            },
            availableValues: {
              required: true,
              type: Object,
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
                chosenValue: "",
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
            disabledFire(disableValue){
                Swal.fire({
                    type: "error",
                    title: "Упс...",
                    text: "Дублирующее значение!",
                    footer: disableValue
                })
            },
            disableCurrentValue(optionValue, optionCode){
                let val;
                let disable = false;
                for (val of this.currentValues){
                    if (val.specification_id === this.chosenSpecId & val.value === optionValue) {
                      disable = true;
                      break;
                    }
                }
                return disable;
            },
            // Добавить новое значение из селекта
            fillNewValue: function (){
                let duplicate = false;
                let val;
                for(val of this.newValues){
                    if (val.text === this.chosenValue) {
                      duplicate = true;
                      break;
                    }
                }

                if (! duplicate){
                    for (val of this.availableValues[this.chosenSpecId]){
                        if (val.value == this.chosenValue) {
                            this.newValues.push({
                                text: val.value,
                                code: val.code,
                            });
                        }
                    }
                }

            },
            // Добавить новое значение в список.
            addNewValue() {
                if (! this.disableCurrentValue(this.newValue, this.newCode)){
                    this.newValues.push({
                        text: this.newValue,
                        code: this.newCode,
                    });
                    this.newValue = "";
                    this.newCode = "";
                }
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