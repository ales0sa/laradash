<template>




    <div class="">
        <ConfirmDialog></ConfirmDialog>



        <Toast position="top-right" />


        <div class="col-md-12">
            <div class="row justify-content-center" v-if="loaded == 0">
                <h3><center><i class="fas fa-sync fa-spin"></i><br>Cargando</center></h3>
            </div>
            <div class="row justify-content-center" v-if="loaded == 2">
                <h3>Guardando</h3>

            <center>
                <ProgressSpinner style="width:50px;height:50px" strokeWidth="8" fill="#EEEEEE" animationDuration=".5s"/>
            </center>

                
            </div>
            <div class="row justify-content-center" v-if="loaded == 3">
                <Message severity="success">Se guardo correctamente.</Message>
            </div>
        </div>

        <div class="p-col-12" v-if="loaded == 1">

<!-------

<SelectButton v-model="selectedlang" :options="languages" dataKey="value" >
    <template #option="slotProps">
        <span :class="'flag flag-' + slotProps.option.flag.toLowerCase()" />

    </template>
</SelectButton> -------->


            <div class="card">
                <div class="card-header">
                   <h5 style="font-weight: bolder; text-transform: uppercase;"> {{ table.name[selectedlang.value] }}  </h5>
                </div>
                <div class="card-body pb-0">

                    <div class="p-fluid p-grid p-formgrid">



                        <InputLayout :relations="relations" 
                                     :value="content[input.columnname]"
                                     :input="input" v-for="(input, inputk) in inputs"
                                     :key="inputk"
                                     :lang="selectedlang.value"
                                     :subForm="subForm"
                                     @myUploader="addFile"
                                     >
                        </InputLayout>



                    </div>
                </div>
            </div>


                    <div class="layout-config" >



            <Button type="button" @click="$router.go(-1)" class="p-button layout-back-button " id="layout-back-button"  icon="pi pi-chevron-left" label="Volver" style="top: 0px;" v-if="!table.singlepage"/>

            <Button type="button" @click="sendForm()" class="p-button-success layout-config-button" id="layout-config-button"  icon="pi pi-save" label="Guardar" />


        </div>


<!---
            <div class="d-sm-flex align-items-center justify-content-between mt-4">

                <Button type="button" @click="sendForm()" class="p-button-success" icon="pi pi-save" label="Guardar" />
            </div>--->
        </div>
    </div>
</template>
<script>
    import axios from 'axios'
    import ConfirmDialog from 'primevue/confirmdialog';
    import Toast from 'primevue/toast';
    import CrudService from './../../service/CrudService';
    import Message from 'primevue/message';
    import InputLayout from './InputLayout'
    var publicPATH = document.head.querySelector('meta[name="public-path"]').content;
    export default {

        props: {
            formName: {
                type: String,
                default: 'Form'
            },
            urlData: {
                type: String,
                default: ''
            },
            urlBack: {
                type: String,
                default: ''
            },
            urlAction: {
                type: String,
                default: ''
            }
        },
        components:  { 
            ConfirmDialog,
            InputLayout,
            Message
        },
        data(){
            return{
                languages: {},

                selectedlang: { flag: 'es', key: 'Español', value: 'es' },
                messages: [],
                tablename: '',
                table: {},
                inputs: [],
                inputs2: [],
                content: {},
                subForm: {},
                edit: null,
                files:  [],
                file: null,
                loaded: 0
            }
        },
        created() {

        console.log('recreated crud/index.vue')

        this.CrudService = new CrudService();

            this.tablename = this.$route.params.table

            this.edit = this.$route.params.id
            //this.CrudService.getTable(this.tablename).then(data => this.inputs = data.inputs);
            this.$nextTick(() => {
                
                let url = this.tablename

                if(this.edit){

                    url = url + '/data/' + this.edit
                }
                
                this.CrudService.getTable(url).then((response) => {

                    //console.log(response)
                    this.languages = response.languages
                    this.tablename = response.tablename
                    this.table     = response.table
                    this.inputs    = response.inputs
                    this.subForm   = response.subForm


                    this.relations = response.relations 

                    this.inputs.forEach(input => {

                        
                        this.content[input.columnname] = {
                            value: input.default,
                            errors: []
                        }


                    });


                if(response.content) {
                    
                   // console.log(console.log(response.content))
                    this.inputs.forEach(input => {

                        if(input.type !== 'subForm') {

                            this.content[input.columnname].value = response.content[input.columnname]
                            
                        }


                    });
                }
                    this.loaded = 1
                });
            });

        },
        mounted () {



        },
        watch: {
        },
        methods: {
            addFile(event){
                //console.log(event, 'recibido en index.vue')
                this.files = event.files
                //console.log(this.files)
            },
            sendForm() {

                console.log('sendForm')
                console.log(this.content)

                this.$confirm.require({
                    message: 'Seguro?',
                    header: 'Guardar registro',
                    icon: 'pi pi-save',
                    acceptClass: 'p-button-success',
                    acceptLabel: 'Sí',
                    rejectLabel: 'No',
                    accept: () => {
                        //callback to execute when user confirms the action
                        this.postForm()

                    },
                    reject: () => {
                        //callback to execute when user rejects the action
                    }
                });


            },
            attachInput(formData, input, content) {

                
                if (input.type == 'subForm') {
                    //console.log(content)
                    content.value.forEach((item, index) => {
                        let subFormData = new FormData()
                        this.subForm[input.columnname].inputs.forEach(subInput => {
                            this.attachInput(subFormData, subInput, item.content[subInput.columnname])
                        });
                        if (item.content['id']) {
                            formData.append(input.columnname + '[' + index + '][id]', item.content['id'].value)
                        }
                        [...subFormData.entries()].forEach(pair => {
                            formData.append(input.columnname + '[' + index + '][' + pair[0] + ']', pair[1])
                        });
                    });
                } else {
                    formData.append(input.columnname, content.value);
                }
            },
            postForm() {
                let formData = new FormData()
                var subForm    = {}
                let subFormOld = this.subForm
                if(this.files){
                    this.files.forEach(file => { 

                        formData.append('file', file);

                    })

                }



                this.inputs.forEach(input => {
                    //console.log(this.content[input.columnname])
                    console.log(this.content)
                    if(input.type == 'file'){

                        this.files.forEach(file => { 

                            formData.append(input.columnname, file);

                        })

                    }else{

                        //formData.append(input.columnname, this.content[input.columnname].value);

                        //console.log(input.columname)
                        this.attachInput(formData, input, this.content[input.columnname])
                    }
                    //formData.append(input.columnname+'_en', this.content[input.columnname+'_en'].value);
                    //formData.append(input.columnname+'_pt', this.content[input.columnname+'_pt'].value);
                });

                //formData.append('subForm', JSON.stringify(subForm));

                let edcheck = ''
                if(this.edit){
                    edcheck = '/'+this.edit
                }
                
                axios.post('/adm/crud/' + this.tablename + edcheck, formData).then((response) => {
                    this.loaded = 2
                    setTimeout(() => {
                        //this.loaded = 1
                        
                        if(response.data.status == 'success' && response.data.action == 'edit'){


                                this.$toast.add({severity:'success', summary: 'Editado!', detail: 'Se actualizo correctamente', life: 3000})

                                this.loaded = 1

                                this.$router.back();
                            /*if(response.data.content) {


                                response.data.inputs.forEach(input => {

                                    if(input.type !== 'subForm'){
                                        this.content[input.columnname].value = response.data.content[input.columnname]
                                    }else{
                                        console.log(response.data.content)
                                    }

                                });



                                this.$toast.add({severity:'success', summary: 'Editado!', detail: 'Se actualizo correctamente', life: 3000})

                                this.loaded = 1
                                }*/


                        }else{
                            


                            this.$router.back();
                         

                        }
                         
                    }, 1000);



                }).catch((error) => {
                    if (error.response.data.message == 'CSRF token mismatch.') {
                        csrf.refresh()
                        .then(() => {
                            //console.log('csrf problem')
                        })
                        .catch((err) => {
                            if (err.message == 'Unauthenticated.') {
                                this.openLoginFormModal()
                            }
                        });
                        return true
                    }
                    if (error.response.data.message == 'Unauthenticated.') {
                        this.openLoginFormModal()
                        return true
                    }

                    if (error.response.data.message == 'The given data was invalid.'){


                        let parsedErrors  = '';
                        let errorData = error.response.data.errors

                        Object.keys(error.response.data.errors).forEach(item =>  {
                            //console.log(errorData[item][0])
                            this.$toast.add({severity:'error', summary: 'Error', detail: errorData[item][0], life: 5000})

                            }
                            //parsedErrors = parsedErrors + '<div style="text-align: center;"> ' + errorData[item] + ' </div>'
                        )

                            



                    }

                    this.loaded = 1
                })
            }
        },
        computed: {
        }
    }
</script>
<style lang="scss">


.truncated {
  width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  
  padding: 20px;
  font-size: 1.3rem;
  margin: 0;
  background: white;
  resize: horizontal;
}

.form-label {
    margin-bottom: .5rem;
    font-weight: bold;
}



</style>
