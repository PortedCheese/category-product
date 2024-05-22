<template>
    <div class="modal fade" id="editSpecModal" tabindex="-1" aria-labelledby="editSpecModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSpecModalLabel">Редактировать характеристику {{ specification.title }}</h5>
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
                            <label for="value">Значение</label>
                            <input type="text"
                                   id="value"
                                   name="value"
                                   v-model="currentValue"
                                   class="form-control">
                        </div>
                        <div class="form-group" v-if="showCode">
                            <label for="code">Код</label>
                            <input type="text"
                                   id="code"
                                   name="code"
                                   v-model="currentCode"
                                   class="form-control">
                          </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button"
                            class="btn btn-primary"
                            @click="updateValue"
                            :disabled="! currentValue.length || loading || disableEditValue()">
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
        props: {
          currentValues:{
            required: true,
          },
        },

        data() {
            return {
                specification: {},
                errors: [],
                newValue: "",
                newCode: "",
                loading: false,
                currentValue: "",
                currentCode: "",
                showCode: "",
            }
        },

        created() {
            this.$parent.$on("new-edit-show", this.initEditable);
        },

        methods: {
            disableEditValue(val = this.currentValue, code = this.currentCode){
                let spec;
                let disable = false;
                for (spec of this.currentValues){
                    if (spec.specification_id === this.specification.specification_id && val === spec.value &&
                        (code === spec.code || (!code && !spec.code))){
                        disable = true;
                        break;
                    }
                }
                return disable;
            },
            disabledFire(disableValue){
                Swal.fire({
                    type: "error",
                    title: "Упс...",
                    text: "Дублирующее значение!",
                    footer: disableValue
                })
            },
            initEditable(specification) {
                this.specification = specification;
                $("#editSpecModal").modal("show");
                this.currentValue = this.specification.value;
                this.currentCode = this.specification.code ?  this.specification.code: "";
                this.showCode = this.specification.specification.type === 'color' ? this.specification.specification.type: false;
            },
            updateValue() {
                this.loading = true;
                this.errors = [];
                if (this.disableEditValue()){
                    this.disabledFire(this.currentValue);
                    this.loading = false;
                    return false;
                }
                axios
                    .put(this.specification.updateUrl, {
                        value: this.currentValue,
                        code: this.currentCode
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