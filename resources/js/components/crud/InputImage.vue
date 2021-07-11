
<template>
<!--<div class="p-fileupload p-fileupload-advanced p-component">--->
<div class="">
    {{ value }}
        <label style="left: 0.429rem;
    color: #383636;
    text-transform: uppercase; font-weight: bolder;
     margin-bottom: 5px; margin-left: 5px;"> {{ input.label.es }}</label>

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
        v-on:init="loadFiles"
        @removefile="remove()"
        :allowRemove="checkRemove"
        :allowRevert="false"
        :allowProcess="checkUpload"
        :server="{  process, revert,  restore, load, fetch }"
        data-pdf-preview-height="320"  
        data-pdf-component-extra-params="toolbar=0&navpanes=0&scrollbar=0&view=fitH"
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
import FilePondPluginPdfPreview from "filepond-plugin-pdf-preview";
const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview,
  FilePondPluginImageCrop,
  FilePondPluginImageTransform,
  FilePondPluginPdfPreview
);
    export default {
        props: {
            tablename:{
                type: String,
                default: {}
            },
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

        },
        components: {
            FilePond
        },
        data(){
            return {
                checkLocal: true,
                checkRemove: true,
                checkUpload: true,
                myFiles: [ 
                    
                ],
                fileTypes: ['image/*', 'application/pdf'],
                myServer: {
                    url: 'url-to-your-api'
                }

            }
        },
        created() {
            console.log(this.value)
            if(this.value){
                this.checkRemove = true
                this.checkUpload = false
                this.myFiles = this.value
            }
        },
        mounted () {

        },
        watch: {


        },
        methods: {
            process(fieldName, file, metadata, load, error, progress, abort) {
                console.log("process start");
                //this.$emit('myUploader', file, this.input.columnname);
                        const formData = new FormData()
                        formData.append(this.input.columnname, file, file.name)
                        // const CancelToken = axios.CancelToken
                        // const source = CancelToken.source()

                        axios({
                            method: 'POST',
                            url: '/adm/crud/' + this.$route.params.table + '/' + this.$route.params.id + '/upload/' + this.input.columnname,
                            data: formData,
                            // 'csrf-token': '<CSRF-TOKEN>',
                            // cancelToken: source.token,
                            onUploadProgress: (e) => {
                                progress(e.lengthComputable, e.loaded, e.total)
                            }
                        }).then(response => {
                            console.log(response)
                            // passing the file id to FilePond
                            this.load(response.data.file)
                        }).catch((thrown) => {
                            if (axios.isCancel(thrown)) {
                                console.log('Request canceled', thrown.message)
                            } else {
                                // handle error
                            }
                        })

            },
            load(uniqueFileId, load, error) {
            // error();
            
            console.log('load to new file')
            console.log(uniqueFileId, this.input.columnname)
            this.value = uniqueFileId
            this.$emit('myUploader', uniqueFileId, this.input.columnname)

            },

            fetch(url, load, error, progress, abort, headers) {
                //error("Local files only");
                console.log('fetching')
            },

            restore(uniqueFileId, load, error, progress, abort, headers) {
            // error();
            //console.log('delete?')
                console.log('restore')
            },
            revert: (uniqueFileId, load, error) => {
            
            // Should remove the earlier created temp file here
            // ...
            console.log('revert')
            // Can call the error method if something is wrong, should exit after
            //error('oh my goodness');
            },
            loadFiles(){
                console.log('loading files')
            },
            clearFiles(){
                console.log('clear files')
            },
            checkExt(file){
                let ext = file.split('.')
                return ext[1]
            },
          remove(){
            console.log(this.$refs.pond.getFiles().length)
            this.loaded = 0
            //this.checkLocal = true
            if(this.checkUpload){
                
                return false
            }else {
                this.checkUpload = true
            }
            
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

                axios.delete('/adm/crud/' + this.$route.params.table + '/' + this.$route.params.id + '/clean/' + this.input.columnname).then((response) => {
                    setTimeout(() => {
                        //this.load()
                        //this.value = null
                        this.loaded = 1
                        //this.myFiles = null
                        console.log(response)

                    }, 1000);
                }).catch((error) => {

                    console.log(error)

                });

                },
                reject: () => {

                    if(this.value){

                        this.$refs.pond.addFile(this.value)

                    }
                }
            });


            
          },
        handleFilePondInit(event) {
            //console.log(event)
            //this.$emit('myUploader', event, this.input.columnname);
        },

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
.filepond--credits {
    display: none;
}
</style>
