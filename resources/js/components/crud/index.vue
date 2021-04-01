<template>




    <div class="">
        <ConfirmDialog></ConfirmDialog>



        <Toast position="top-right" />


        <div class="col-md-12">
            <div class="row justify-content-center" v-if="loaded == 0">
                <h3><center><i class="fas fa-sync fa-spin"></i><br>Cargando</center></h3>
            </div>
            <div class="row justify-content-center" v-if="loaded == 2">
                <h3><center><i class="fas fa-sync fa-spin"></i><br>Guardando</center></h3>
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
                   <h5> {{ table.name[selectedlang.value] }}  </h5>
                </div>
                <div class="card-body pb-0">

                    <div class="p-fluid p-grid p-formgrid">



                        <InputLayout :relations="relations" :value="content[input.columnname]" :input="input" v-for="(input, inputk) in inputs" :key="inputk"  @myUploader="addFile" :lang="selectedlang.value">
                        </InputLayout>

                    </div>
                </div>
            </div>


                    <div class="layout-config" >



            <Button type="button" @click="$router.go(-1)" class="p-button-info layout-config-button" id="layout-config-button"  icon="pi pi-chevron-left" label="Volver" style="top: -10px;"/>

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
  //  import draggable from 'vuedraggable'
    import InputLayout from './InputLayout'

    import ConfirmDialog from 'primevue/confirmdialog';
    import Toast from 'primevue/toast';
    import CrudService from './../../service/CrudService';
    import Message from 'primevue/message';
    import axios from 'axios'

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
        components:  { ConfirmDialog,
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
                edit: null,
                files:  [],
                file: null,
                loaded: 0
            }
        },
        created() {

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

                    this.languages = response.languages
                    this.tablename = response.tablename
                    this.table     = response.table
                    this.inputs    = response.inputs



                    this.relations = response.relations 
                    this.inputs.forEach(input => {

                        
                        this.content[input.columnname] = {
                            value: input.default,
                            errors: []
                        }


                    });


                if(response.content) {
                    this.inputs.forEach(input => {
                        this.content[input.columnname].value = response.content[input.columnname]


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
                console.log(event, 'recibido en index.vue')
                this.files = event.files
                console.log(this.files)
            },
            sendForm() {

                console.log('sendForm')

                this.$confirm.require({
                    message: 'Seguro?',
                    header: 'Guardar registro',
                    icon: 'pi pi-save',
                    acceptClass: 'p-button-success',
                    acceptLabel: 'Sí',
                    accept: () => {
                        //callback to execute when user confirms the action
                        this.postForm()

                    },
                    reject: () => {
                        //callback to execute when user rejects the action
                    }
                });


            },
            postForm() {
                let formData = new FormData()

                if(this.files){
                    this.files.forEach(file => { 

                        formData.append('file', file);

                    })

                }


                this.inputs.forEach(input => {
                    console.log(this.content[input.columnname].value)
                    if(input.type == 'file'){

                        this.files.forEach(file => { 

                            formData.append(input.columnname, file);

                        })

                    }else{

                        formData.append(input.columnname, this.content[input.columnname].value);

                    }
                    //formData.append(input.columnname+'_en', this.content[input.columnname+'_en'].value);
                    //formData.append(input.columnname+'_pt', this.content[input.columnname+'_pt'].value);
                });

                let edcheck = ''
                if(this.edit){
                    edcheck = '/'+this.edit
                }
                
                axios.post('/adm/crud/' + this.tablename + edcheck, formData).then((response) => {
                    this.loaded = 3
                    setTimeout(() => {
                        //this.loaded = 1
                        
                        if(response.data.status == 'success' && response.data.action == 'edit'){


                            if(response.data.content) {
                                response.data.inputs.forEach(input => {
                                    this.content[input.columnname].value = response.data.content[input.columnname]


                                });
                                this.loaded = 1
                            }


                        }else{
                            
                            this.$router.back();
                         

                        }
                         
                    }, 1000);



                }).catch((error) => {
                    if (error.response.data.message == 'CSRF token mismatch.') {
                        csrf.refresh()
                        .then(() => {
                            console.log('csrf problem')
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
<style lang="css">
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
