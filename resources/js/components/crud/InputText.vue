<template>
<span class="p-float-label">

        <InputNumber mode="currency" 
        currency="ARS"
         v-model="val.value"   :id="input.columnname"
        v-if="input.type == 'number' || input.type == 'money'"/>

        <InputText type="text"   v-model="val.value"   :id="input.columnname" style="width: 100%" v-else/>
        <label :for="input.columnname">{{ input.label[lang()] }}</label>

</span>
</template>
<script>

    export default {
        props: {
            submitted: { type: Boolean, default: false },
            input: {
                type: Object,
                default: {}
            },
            value: {
                type: Object,
                default: {}
            }
        },
        components: {},
        data(){
            return{
                val: this.value,
                validationErrors: this.$parent.validationErrors
            }
        },
        created() {
        },
        mounted () {},
        watch: {},
        methods: {
            lang() {
                return this.$parent.lang
            }
        },
        computed: {
        }
    }

</script>
<style lang="scss" scoped>

</style>
