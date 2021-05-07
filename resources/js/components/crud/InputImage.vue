
<template>
<div class="p-fileupload p-fileupload-advanced p-component">

        <FileUpload name="demo[]" 
                    :chooseLabel="chooseLabel"
                    :showUploadButton="false"

                    :showCancelButton="false"
                    accept="image/*" :maxFileSize="10000000"  :customUpload="true" @uploader="myUploader" :auto="true"

        
        >

      <template #empty>

            
            <div class="" v-if="file">
                <div class="p-fileupload-row">
                    <div>
                        <img role="presentation" :alt="file" :src="file" :width="previewWidth" />
                    </div>
                    <div> </div>
                    
                    <div>
                      <Button icon="pi pi-times" class="p-button-rounded p-button-danger" style="width: 35px; height: 35px;" @click="remove()" v-if="$route.params.id >= 1 && input.nullable == 1"/>

                    </div>
                </div>
            </div>

          <p v-if="!file"> Arrastre los archivos aquí.</p>
      </template>

        </FileUpload>
</div>
</template>

<script>
    import axios from 'axios'
    
    export default {
        props: {
            input: {
                type: Object,
                default: {}
            },
            value: {
                type: String,
                default: {}
            },
            previewWidth: {
                type: Number,
                default: 100
            }
        },
        components: {

        },
        data(){
            return{
                file: null,
            }
        },
        created() {
        },
        mounted () {
          this.file = this.value
        },
        watch: {


        },
        methods: {

          remove(){


            this.$confirm.require({
                message: 'Seguro ?',
                header: 'Eliminar foto',
                icon: 'pi pi-exclamation-triangle',
                acceptClass: 'p-button-danger',
                acceptLabel: 'Sí',
                accept: () => {
                    //callback to execute when user confirms the action

                axios.get('/adm/crud/' + this.$route.params.table + '/' + this.$route.params.id + '/clean/' + this.input.columnname).then((response) => {
                    setTimeout(() => {
                        this.file = null
                        //this.load()
                        this.loaded = 1
                    }, 1000);
                }).catch((error) => {

                    console.log(error)

                });

                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });


            
          },
        myUploader(event) {
              console.log(event)
            this.$emit('myUploader', event);
        }

        },
        computed: {
          chooseLabel(){

            if(this.file == "" || this.file == null){

              return 'Seleccionar imagen'
            }else{
              return 'Cambiar imagen'
            }
          }
        }
    }

</script>

<style>
.p-fileupload-content {
    position: relative;
}
.p-fileupload-row {
    display: flex;
    align-items: center;
}
.p-fileupload-row > div {
    flex: 1 1 auto;
    width: 25%;
}
.p-fileupload-row > div:last-child {
    text-align: right;
}
.p-fileupload-content .p-progressbar {
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
.p-button.p-fileupload-choose {
    position: relative;
    overflow: hidden;
}
.p-button.p-fileupload-choose input[type=file] {
    display: none;
}
.p-fileupload-choose.p-fileupload-choose-selected input[type=file] {
    display: none;
}
.p-fluid .p-fileupload .p-button {
    width: auto;
}
</style>