<template>
    <div :class="'p-field  p-col-12 p-md-' + input.gridcols" v-if="input.type !== 'bigInteger' && input.visible_edit == 1" >



        <InputTextDash  :value="value" :input="input" v-if="layout[input.type] == 'basic'" ></InputTextDash>


        <InputSelect :relations="relations" :value="value" :input="input" v-if="layout[input.type] == 'select'"></InputSelect>


        <InputRadio :relations="relations" :value="value" :input="input" 
        v-if="layout[input.type] == 'radio'"></InputRadio>


        <InputCheckbox :relations="relations" :value="value" :input="input" v-if="layout[input.type] == 'checkbox'"></InputCheckbox>

        <InputBoolean v-model="value"  v-if="layout[input.type] == 'boolean'"
        :relations="relations" :value="value" :input="input" />

        <InputDate :value="value" :input="input" v-if="layout[input.type] == 'date'" ></InputDate>

        <Editor v-model="value.value" :value="value" :input="input" v-if="layout[input.type] == 'textarea'" editorStyle="height: 320px; margin-bottom: 20px;"/> 


        <SubForm :relations="relations" :value="value" :subForm="subForm" :input="input" v-if="layout[input.type] == 'subForm'"></SubForm>
        
        <InputImage v-if="layout[input.type] == 'file'" mode="basic" name="demo[]" 
                    v-model="value.value" :value="value" :input="input"
                    @myUploader="myUploader"
        />

        <div  v-if="layout[input.type] == 'VueComponent'" >  

            <CustomComponent :input="input" />

        </div>

    </div> 
</template>
<script>
    import InputTextDash from './InputText'
    import InputDate from './InputDate'
    import InputSelect from './InputSelect'
    import InputRadio from './InputRadio'
    import InputBoolean from './InputBoolean'
    import InputCheckbox from './InputCheckbox'
    import InputImage from './InputImage'
    import SubForm     from './SubForm'
    import CustomComponent     from './CustomComponent'

    export default {
        props: {
            lang: {type: String,
                default: 'es'
            },
            input: {
                type: Object,
                default: {}
            },
            relations: {
                default: {}
            },
            subForm: {
             //   type: Object,
             //   default: {}
            },
            value: {
                type: Object,
                default: {}
            },
        },
        components: {
            InputTextDash,
            InputSelect,
            InputRadio,
            InputDate,
            InputBoolean,
            InputCheckbox,
            InputImage,
            SubForm,
            CustomComponent
        },
        data(){
            return{

                layout: {
                    "text": 'basic',
                    "textarea": 'textarea',
                    "email": 'basic',
                    "url": 'basic',
                    "tel": 'basic',
                    "number": 'basic',
                    "money": 'basic',
                    "password": 'basic',
                    "boolean": 'boolean',
                    "date": 'date',
                    "time": 'date',
                    "datetime": 'date',
                    "week": 'date',
                    "select": 'select',
                    "radio": 'radio',
                    "checkbox": 'checkbox',
                    "file": 'file',
                    "subForm": 'subForm',
                    "VueComponent": "VueComponent"
                }
            }
        },
        created() {
        },
        mounted () {



        },
        watch: {


        },
        methods: {
        myUploader(event) {
            //event.files == files to upload
            console.log(event, 'rec. en layout')
            this.$emit('myUploader', event);
        }

        },
        computed: {
        }
    }

</script>
<style lang="scss" scoped>

</style>

