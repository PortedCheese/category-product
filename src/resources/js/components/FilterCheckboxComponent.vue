<template>
    <div class="form-group">
        <label>
            {{ filterData.title }}
        </label>

        <input type="hidden"
               v-for="item in chosenItems"
               :name="item.inputName"
               :value="item.value">

        <input type="text"
               v-if="items.length > limit"
               aria-label="Поиск"
               class="form-control mb-2"
               v-model="search">

        <div class="custom-control custom-checkbox"
             v-for="(item, index) in visibleItems">
            <input type="checkbox"
                   :id="item.inputId + postfix"
                   :value="item.value"
                   :checked="item.checked"
                   v-model="checkedValues"
                   class="custom-control-input">
            <label :for="item.inputId + postfix" class="custom-control-label">
                {{ item.value }}
            </label>
        </div>

        <div v-if="! showHidden" class="d-flex justify-content-between">
            <a href="#" @click.prevent="showHidden = true" v-if="hiddenItems > 0">
                Еще {{ hiddenItems }}
            </a>
            <span v-if="checkedValues.length" class="text-muted">
                Выбрано {{ checkedValues.length }}
            </span>
        </div>
        <div v-if="showHidden" class="d-flex justify-content-between">
            <a href="#" @click.prevent="showHidden = false">
                Меньше
            </a>
            <span v-if="checkedValues.length" class="text-muted">
                Выбрано {{ checkedValues.length }}
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FilterCheckboxComponent",

        props: {
            filterData: {
                type: Object,
                required: true
            },

            modal: {
                type: Boolean,
                default: false
            },

            limit: {
                type: Number,
                default: 3
            }
        },

        data() {
            return {
                items: [],
                postfix: "",
                checkedValues: [],
                search: "",
                showHidden: false,
            }
        },

        computed: {
            visibleItems() {
                if (this.showHidden) {
                    return this.filtered;
                }
                else {
                    return this.filtered.slice(0, this.limit);
                }
            },

            filtered() {
                return this.items.filter(item => {
                    return item.value.toLowerCase().includes(this.search.toLowerCase())
                })
            },

            chosenItems() {
                let items = [];
                for (let item in this.items) {
                    if (this.items.hasOwnProperty(item)) {
                        if (this.checkedValues.includes(this.items[item].value)) {
                            items.push(Object.assign({}, this.items[item]));
                        }
                    }
                }
                return items;
            },

            hiddenItems() {
                if (this.showHidden) {
                    return 0;
                }
                else {
                    return this.filtered.length - this.limit;
                }
            }
        },

        created() {
            this.items = this.filterData.vueValues;
            this.postfix = this.modal ? "-modal" : "-sidebar";
            for (let item in this.items) {
                if (this.items[item].checked) {
                    this.checkedValues.push(this.items[item].value);
                }
            }
        },

        methods: {
            changeItems() {
                for (let element in this.items) {
                    if (this.items.hasOwnProperty(element)) {
                        if (this.checkedValues.includes(this.items[element].value)) {
                            this.items[element].checked = true;
                        }
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>