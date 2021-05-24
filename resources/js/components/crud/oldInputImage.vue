
<template>
<div class="p-fileupload p-fileupload-advanced p-component">

    <FilePond
        name="test"
        ref="pond"
        class-name="my-pond"
        label-idle="Arrastre el archivo aquí..."
        :allow-multiple="false"
        :allow-image-crop="true"
        :instant-upload="false"
        :accepted-file-types="this.fileTypes"
        v-bind:files="myFiles"
        image-crop-aspect-ratio="1"
        :allowImageCrop="true"
        v-on:init="loadFiles"
        :server="myServer"
        />
<!---
        <FileUpload name="demo[]" 
                    :chooseLabel="chooseLabel"
                    :showUploadButton="true"
                    :fileLimit=1
                    uploadLabel="Subir"
                    cancelLabel="Borrar"
                    :showCancelButton="true"
                    :maxFileSize="10000000"  
                    :customUpload="true" @uploader="handleFilePondInit" :auto="true"
                    :clear="clearFiles"

        
        >

      <template #empty>
            
            <div class="" v-if="file">
                <div class="p-fileupload-row">
                    <div v-if="checkExt(file) == 'pdf'">
                        {{ checkExt(file)}}
                    </div>

                    <div v-else>
                        <img role="presentation" :alt="file" :src="file" :width="previewWidth" />
                    </div>

                    
                    <div>
                      <Button icon="pi pi-times" class="p-button-rounded p-button-danger" style="width: 35px; height: 35px;" @click="remove()" v-if="$route.params.id >= 1 && input.nullable == 1"/>

                    </div>
                </div>
            </div>

          <p v-if="!file"> Arrastre los archivos aquí.</p>
      </template>

        </FileUpload> --->
</div>
</template>

<script>
    import axios from 'axios'
    // Import FilePond
import vueFilePond, { setOptions } from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview,
  FilePondPluginImageCrop,
  FilePondPluginImageTransform
);
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
            },
             myServer: {
                url: 'url-to-your-api'
            }
        },
        components: {
            FilePond
        },
        data(){
            return{
                myFiles: [ 
                    
                ],
                fileTypes: ['image/*', 'application/pdf'],
            }
        },
        created() {
        },
        mounted () {
          this.myFiles = this.value
        },
        watch: {


        },
        methods: {
            loadFiles(){
                console.log('wololo')
            },
            clearFiles(){
                console.log('clear files')
            },
            checkExt(file){
                let ext = file.split('.')
                return ext[1]
            },
          remove(){


            this.$confirm.require({
                message: 'Seguro ?',
                header: 'Eliminar foto',
                icon: 'pi pi-exclamation-triangle',
                acceptClass: 'p-button-danger',
                acceptLabel: 'Sí',
                rejectClass: 'p-button-info',
                rejectLabel: 'No',
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
        handleFilePondInit(event) {
            //console.log(event)
            this.$emit('myUploader', event, this.input.columnname);
        }

        },
        computed: {
          chooseLabel(){

            if(this.file == "" || this.file == null){

              return 'Seleccionar ' + this.input.label.es
            }else{
              return 'Cambiar ' + this.input.label.es
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

.filepond--credits {
    display: none;
}
</style>