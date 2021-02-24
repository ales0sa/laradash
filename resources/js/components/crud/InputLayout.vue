<template>
    <div :class="'p-field p-col-12 p-md-' + input.gridcols">


        <InputFontAwesome  :value="value" :input="input" v-if="layout[input.type] == 'icon' && lang == 'es' " ></InputFontAwesome>


        <InputTextDash  :value="value" :input="input" v-if="layout[input.type] == 'basic'" ></InputTextDash>


        <InputSelect :relations="relations" :value="value" :input="input" v-if="layout[input.type] == 'select'"></InputSelect>


        <InputDate :value="value" :input="input" v-if="layout[input.type] == 'date'" ></InputDate>


        <Editor v-model="value.value" :value="value" :input="input" v-if="layout[input.type] == 'textarea'" 
        placeholder="..."
        editorStyle="height: 320px"/> 



    <FileUpload  v-if="layout[input.type] == 'file'  && lang == 'es'" 
    mode="advanced" 
    name="file[]" 
    :chooseLabel="chslab"
    cancelLabel=" Limpiar "
    :fileLimit="1"

    accept="image/*" :maxFileSize="5e+6"  :customUpload="true" @uploader="myUploader" :auto="true" >

        <template #empty>
            

            <div v-if="value.value == '' || !value.value"> 

                <p> Arrastre aquí la imagen a subir. </p> 

            </div>
            <div v-else>
                
                <img :src="value.value.replace('public/','storage/')" height="150px" />

            </div>

        </template>



    </FileUpload>


    </div>

</template>
<script>
    import InputTextDash from './InputText'
    import InputDate from './InputDate'
    import InputSelect from './InputSelect'
    import InputFontAwesome from './InputFontAwesome'

    export default {
        props: {
            lang: {type: String,
                default: 'es'
            },
            input: {
                type: Object,
                default: () => {}
            },
            relations: {
                default: () => {}
            },
            value: {
                type: Object,
                default: () => {}
            },
        },
        components: {
            InputTextDash,
            InputSelect,
            InputFontAwesome,
            InputDate
        },
        data(){
            return{

                layout: {
                    "text": 'basic',
                    "icon": "icon",
                    "textarea": 'textarea',
                    "email": 'basic',
                    "url": 'basic',
                    "tel": 'basic',
                    "number": 'basic',
                    "money": 'basic',
                    "password": 'basic',
                    "true_or_false": 'select',
                    "date": 'date',
                    "time": 'date',
                    "datetime": 'date',
                    "week": 'date',
                    "select": 'select',
                    "radio": 'select',
                    "checkbox": 'select',
                    "select2": 'select',
                    "select2multiple": 'select',
                    "file": 'file',
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

            this.$emit('input', event.files);
        }

        },
        computed: {

            chslab() {
                if(this.value.value == '' || !this.value.value){
                    return "Seleccionar"
                }else{
                    return "Cambiar"
                }
            }
        }
    }

</script>
