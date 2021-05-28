<template>
    <div :class="'p-field  p-col-12 p-md-' + input.gridcols" 
        v-if="input.type !== 'bigInteger' && input.visible_edit == 1" >


        <InputTextDash  :value="value" :input="input" v-if="layout[input.type] == 'basic'" ></InputTextDash>


        <InputSelect :relations="relations" :value="value" :input="input" v-if="layout[input.type] == 'select'"></InputSelect>


        <InputRadio :relations="relations" :value="value" :input="input" 
        v-if="layout[input.type] == 'radio'"></InputRadio>


        <InputCheckbox :relations="relations" :value="value" :input="input" v-if="layout[input.type] == 'checkbox'"></InputCheckbox>

        <InputBoolean v-model="value"  v-if="layout[input.type] == 'boolean'"
        :relations="relations" :value="value" :input="input" />

        <InputDate :value="value" :input="input" v-if="layout[input.type] == 'datetime'"
         ></InputDate>


        <Editor v-model="value.value" :value="value" 
            :input="input" v-if="layout[input.type] == 'textarea'"
            editorStyle="height: 320px; margin-bottom: 20px;"> 
            	<template #toolbar>
                    <span style="font-weight: bolder; text-transform: uppercase;"> {{ input.label[labellang()] }}</span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                    </span>
                </template>
        </Editor>


        <SubForm :relations="relations" :value="value" :subForm="subForm" :input="input" v-if="layout[input.type] == 'subForm'"></SubForm>
        
        <InputImage v-if="layout[input.type] == 'file'" mode="basic" name="demo[]" 
                    v-model="value.value" :value="value" :input="input"
                    :tablename="this.$parent.tablename"
                    @myUploader="myUploader"
        />

        <InputColor  :value="value" :input="input" v-if="layout[input.type] == 'color'" ></InputColor>

        <div  v-if="layout[input.type] == 'VueComponent'" >  

            
            <CustomComponent :value="this.$parent.content[input.referencecolumn]" :input="input" @setValue="setValue"/>

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
    import InputColor from './InputColor'
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
            InputColor,
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
                    "datetime": 'datetime',
                    "time": 'datetime',
                    "date": 'datetime',
                    "select": 'select',
                    "radio": 'radio',
                    "checkbox": 'checkbox',
                    "file": 'file',
                    "subForm": 'subForm',
                    "VueComponent": "VueComponent",
                    "color": "color"
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
            labellang(){
                return document.documentElement.lang
            },
            myUploader(event, colnam) {
                //event.files == files to upload
                console.log(event, colnam,'rec. en layout')
                this.$emit('myUploader', event, colnam);
            },
            setValue(data){
                console.log('get in input layout')
                this.$emit('setValue', data)
            }
        
        },
        computed: {
        }
    }

</script>
<style lang="scss" scoped>

</style>

