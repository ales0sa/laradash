<template>
        <span class="p-float-label p-mt-3">
            
        <Calendar :id="input.columnname" v-model="val.value" 
            v-if="input.type == 'date'"
        />

        <InputMask v-model="val.value" mask="99:99"
            placeholder="HH:MM" 
            slotChar="_"
            v-else-if="input.type == 'time'"
            />

        <Calendar :id="input.columnname" v-model="val.value" 
            v-if="input.type == 'datetime'"
        />

        <label :for="input.columnname">{{ input.label[lang()] }}</label>


    </span>
</template>
<script>

    export default {
        props: {
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
                columna: null,
                cuit: null,
                disabled: false,
                domicilioFiscal: {},
            }
        },
        created() {
            //this.val.value = '00:00:00'
            //this.val = this.value.value
        },
        mounted () {},
      watch: {

      },
        methods: {

            lang() {
                return document.documentElement.lang
            }
        },
        computed: {
        }
    }

</script>
<style lang="css" scoped>
.form-label {
    margin-bottom: .5rem;
    font-weight: bold;
}
</style>
