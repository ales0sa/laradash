<template>
<span class="p-float-label p-mt-3">

        <InputNumber 
         v-model="val.value"   :id="input.columnname"
        v-if="input.type == 'number'"/>
        <InputNumber mode="currency" 
        currency="ARS"
         v-model="val.value"   :id="input.columnname"
        v-else-if="input.type == 'money'"/>

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
