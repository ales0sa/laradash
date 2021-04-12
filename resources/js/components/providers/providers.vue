<template>
    <div class="p-grid p-fluid dashboard">

        <ConfirmDialog></ConfirmDialog>

        <DataTable :value="data" dataKey="id" :paginator="true" :rows="10" :filters="filters"
        :loading="loading"
         :resizableColumns="false" columnResizeMode="fit"
        >


        <template #header>

            <div class="p-d-flex p-jc-between p-mx-auto ">
                <div style=""> 

                    <h3>{{ title }}</h3>

                </div>

                <div class="p-d-flex">
                    
                        <InputText v-model="filters['global']" placeholder="Buscar..." 
                        style="height: 30px;margin-right: 10px;" />
                
                        <router-link :to="{ name: 'crudcreate', params: { table:  $route.params.table }}" > 
                            <Button icon="pi pi-plus-circle" label="Nuevo" class=" p-button-primary p-button p-component" style="height: 30px;"/>
                        </router-link>

                </div>

            </div>
        </template>


        <template #empty>
           Sin resultados
       </template>


        <Column bodyClass=""  v-for="col of columns" :field="col.field" :header="col.header" :key="col.field" sortable>

                <template #filter>


                    <div  v-if="col.type == 'text'" >
                           <InputText type="text" v-model="filters[col.field]" class="p-column-filter" :placeholder="'Buscar por '+ col.header"/>
                    </div>


                    <div  v-if="col.type == 'date'" >
                            <Calendar v-model="filters[col.field]" dateFormat="dd-mm-yy" class="p-column-filter" placeholder="Fecha" :showButtonBar="true"/>
                    </div>

                    <div  v-if="col.type == 'select' && col.options">                     
                    <select v-model="filters[col.field]" style="width:100%;">
                        <option></option>
                        <option  v-for="opt of col.options" :value="opt.key" :key="opt.key"> {{opt.text}} </option>
                    </select>


                    </div>

                </template>



                <template  #body="slotProps" v-if="col.type == 'textarea'">
                    <div class="">
                        <span class="" v-html="slotProps.data[col.field]" />
                    </div>
                </template>
                <template #body="slotProps" v-else-if="col.type == 'file'">

                    <div v-if="checkFileType(slotProps.data[col.field]) == 'image'">
                       <a target="_blank" :href="slotProps.data[col.field]">
                           
                           <img :src="'/storage/'+slotProps.data[col.field]" v-if="slotProps.data[col.field]"
                            @error="setAltImg"
                            class="product-image"
                           />
                           
                        </a>
                    </div>
                    <div v-if="checkFileType(slotProps.data[col.field]) == 'pdf'">

                          
                        <Button icon="pi pi-file-pdf" label="PDF" class="p-button-rounded p-button-danger p-button-outlined" 
                        @click="openPlane(slotProps.data.file)"
                        /> 
                    </div>

                </template>
                <template #body="slotProps" v-else-if="col.type == 'boolean'">

                  
                        <InputSwitch :value="Boolean(Number(slotProps.data[col.field]))"  :disabled="true"/>

           <!---              <span v-if="slotProps.data[col.field] === 1">
                            <i class="pi pi-check"></i>
                        </span>
                        <span v-else> 
                            <i class="pi pi-times"></i> 
                        </span>
--->

                </template>
                <template #body="slotProps" v-else-if="col.type == 'checkbox'">

                <span v-if="col.tabledata && col.valueoriginselector == 'table' "> 

                    <Chip class="p-ml-1 p-mb-1"
                      :label="rels[col.tabledata][item]" v-for="item of splitMe(slotProps.data[col.field])" :key="item" />

                </span>
                <span v-else-if="col.valueoriginselector == 'values' "> 

                      <Chip class="p-ml-1 p-mb-1"
                      :label="getKeyByValue(col.options,item)" v-for="item of splitMe(slotProps.data[col.field])" :key="item" />
                </span>

                </template>


                
                <template #body="slotProps" v-else-if="col.type == 'radio'">

                        {{ getKeyByValue(col.options,slotProps.data[col.field]) }}

                </template>

                <template #body="slotProps" v-else-if="col.type == 'select'">
                    

                    <div v-if="col.multiple == true">
                            
                        <span v-if="col.tabledata && col.valueoriginselector == 'table' "> 

                    <Chip class="p-ml-1 p-mb-1"
                      :label="rels[col.tabledata][item]" v-for="item of splitMe(slotProps.data[col.field])" :key="item" />

                        </span>
                        <span v-else-if="col.valueoriginselector == 'values' "> 

                    <Chip class="p-ml-1 p-mb-1"
                      :label="getKeyByValue(col.options,item)" v-for="item of splitMe(slotProps.data[col.field])" :key="item" />

                        </span>



                    </div>
                    <div v-else>
                        <span v-if="col.tabledata && col.valueoriginselector == 'table' "> 

                            {{ rels[col.tabledata][slotProps.data[col.field]] }}

                        </span>
                        <span v-else-if="col.valueoriginselector == 'values' "> 

                            {{ getKeyByValue(col.options,slotProps.data[col.field]) }}

                        </span>
                    </div>
                </template>
                
        </Column>

        <Column v-if="columns.length >= 1" headerStyle="width: 120px;">
        <template #body="slotProps">

            <span class="p-buttonset">

            <Button icon="pi pi-pencil" class="p-button-outlined p-button-raised p-button-sm p-button-success" @click="edit(slotProps.data.id)" />
            <Button icon="pi pi-copy" class="p-button-outlined p-button-raised p-button-sm p-button-secondary" @click="dupe(slotProps.data.id)" />
            <Button icon="pi pi-trash" class="p-button-outlined p-button-raised p-button-sm  p-button-danger" @click="del(slotProps.data.id)" />
            </span>
        </template>
    </Column>

</DataTable>

</div>
</template>

<script>

import CrudService from './../../service/CrudService';

import ConfirmDialog from 'primevue/confirmdialog';
import axios from 'axios'
    export default {



        data() {
            return {
                columns: [],
                loading: true,
                data: [],
                inputs:  [],
                rels:  [],
                filters: {},
                tablename: this.$route.params.table,
                title: ''
            }
            
        },
        components:  { ConfirmDialog },
        watch:{ 
            '$route.params.table': function (table){

                this.load()

            },


            inputs(val) {
               for (var index = 0; index < this.inputs.length; index++) {
                    //console.log(index)
                    if(this.inputs[index]['visible'] == 1 || this.inputs[index]['visible'] == "1" ){
                        this.columns.push({ 

                            field: this.inputs[index]['columnname'],
                            header: this.inputs[index]['label']['es'],
                            type: this.inputs[index]['type'],
                            options: this.inputs[index]['options'],
                            tabledata: this.inputs[index]['tabledata'],
                            multiple: this.inputs[index]['multiple'],  
                            valueoriginselector: this.inputs[index]['valueoriginselector']

                            });


 
                    }
                    
                }


            }

        },
        created() {
        this.CrudService = new CrudService();

        //console.log(this.data)

        },
        mounted() {


            //this.tablename = this.$route.params.table
            //this.CrudService.getTable(this.tablename).then(data => this.inputs = data.inputs);
           // this.CrudService.getData(this.tablename).then(data => this.data = data);
           this.load()

    },
    methods:{
                checkFileType(file){
            
            if(file == null){
                return 'file'
            }else{
                
                let parts = file.split('.')
                switch(parts[1]) {
                  case 'pdf':
                    //// code block
                    return 'pdf'
                    break;
                  case 'jpg':
                  case 'png':
                  case 'jpeg':
                  case 'webp':
                    return 'image'
                    break;
                  case 'doc':
                  case 'docx':
                  case 'txt':
                  case 'rtf':
                    return 'doc'
                    // code block
                    break;
                  default:
                    return 'file'
                }
                
            }



        },
        splitMe(string){
            if(string){
                return string.split(',')
            }else{
                return [];
            }
        },
        checkSplit(value){
            if(value){
                console.log(value)
                return true
            }
            

        },
        setAltImg(event) { 
            event.target.src = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMTE1LjE5IDEyMy4zOCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMTE1LjE5IDEyMy4zOCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PHN0eWxlIHR5cGU9InRleHQvY3NzIj4uc3Qwe2ZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO3N0cm9rZTojMDAwMDAwO3N0cm9rZS13aWR0aDowLjU7c3Ryb2tlLW1pdGVybGltaXQ6Mi42MTMxO308L3N0eWxlPjxnPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik05My4xMyw3OS41YzEyLjA1LDAsMjEuODIsOS43NywyMS44MiwyMS44MmMwLDEyLjA1LTkuNzcsMjEuODItMjEuODIsMjEuODJjLTEyLjA1LDAtMjEuODItOS43Ny0yMS44Mi0yMS44MiBDNzEuMzEsODkuMjcsODEuMDgsNzkuNSw5My4xMyw3OS41TDkzLjEzLDc5LjV6IE04LjA4LDAuMjVoOTUuMjhjMi4xNywwLDQuMTEsMC44OSw1LjUzLDIuM2MxLjQyLDEuNDIsMi4zLDMuMzksMi4zLDUuNTN2NzAuMDEgYy0yLjQ2LTEuOTEtNS4yNC0zLjQ0LTguMjUtNC40OFY5Ljk4YzAtMC40My0wLjE2LTAuNzktMC40Ni0xLjA1Yy0wLjI2LTAuMjYtMC42Ni0wLjQ2LTEuMDUtMC40Nkg5Ljk0IGMtMC40MywwLTAuNzksMC4xNi0xLjA1LDAuNDZDOC42Myw5LjE5LDguNDMsOS41OCw4LjQzLDkuOTh2NzAuMDJoMC4wM2wzMS45Ny0zMC42MWMxLjI4LTEuMTgsMy4yOS0xLjA1LDQuNDQsMC4yMyBjMC4wMywwLjAzLDAuMDMsMC4wNywwLjA3LDAuMDdsMjYuODgsMzEuOGMtNC43Myw1LjE4LTcuNjIsMTIuMDgtNy42MiwxOS42NWMwLDMuMjksMC41NSw2LjQ1LDEuNTUsOS40SDguMDggYy0yLjE3LDAtNC4xMS0wLjg5LTUuNTMtMi4zcy0yLjMtMy4zOS0yLjMtNS41M1Y4LjA4YzAtMi4xNywwLjg5LTQuMTEsMi4zLTUuNTNTNS45NCwwLjI1LDguMDgsMC4yNUw4LjA4LDAuMjV6IE03My45OCw3OS4zNSBsMy43MS0yMi43OWMwLjMtMS43MSwxLjkxLTIuOSwzLjYyLTIuNmMwLjY2LDAuMSwxLjI1LDAuNDMsMS43MSwwLjg2bDE3LjEsMTcuOTdjLTIuMTgtMC41Mi00LjQ0LTAuNzktNi43OC0wLjc5IEM4NS45MSw3MS45OSw3OS4xMyw3NC43Nyw3My45OCw3OS4zNUw3My45OCw3OS4zNXogTTgxLjk4LDE4LjE5YzMuMTMsMCw1Ljk5LDEuMjgsOC4wMywzLjMyYzIuMDcsMi4wNywzLjMyLDQuOSwzLjMyLDguMDMgYzAsMy4xMy0xLjI4LDUuOTktMy4zMiw4LjAzYy0yLjA3LDIuMDctNC45LDMuMzItOC4wMywzLjMyYy0zLjEzLDAtNS45OS0xLjI4LTguMDMtMy4zMmMtMi4wNy0yLjA3LTMuMzItNC45LTMuMzItOC4wMyBjMC0zLjEzLDEuMjgtNS45OSwzLjMyLTguMDNDNzYuMDIsMTkuNDQsNzguODYsMTguMTksODEuOTgsMTguMTlMODEuOTgsMTguMTl6IE04NS44Miw4OC4wNWwxOS45NiwyMS42IGMxLjU4LTIuMzksMi41LTUuMjUsMi41LTguMzNjMC04LjM2LTYuNzgtMTUuMTQtMTUuMTQtMTUuMTRDOTAuNDgsODYuMTcsODcuOTksODYuODUsODUuODIsODguMDVMODUuODIsODguMDV6IE0xMDAuNDQsMTE0LjU4IGwtMTkuOTYtMjEuNmMtMS41OCwyLjM5LTIuNSw1LjI1LTIuNSw4LjMzYzAsOC4zNiw2Ljc4LDE1LjE0LDE1LjE0LDE1LjE0Qzk1Ljc4LDExNi40Niw5OC4yNywxMTUuNzgsMTAwLjQ0LDExNC41OEwxMDAuNDQsMTE0LjU4eiIvPjwvZz48L3N2Zz4=" 
        },
        openPlane(file) {   
          window.open("/storage/"+file.replace('public/','storage/'), "_blank");    
        },
        getKeyByValue(object, value) { 


            
            if(!value || value == null || value == '' || value == ""){
                 return ''
            
            }else{

            let text = object.filter(function (op) { return op.key == String(value) });
            return text[0].text
            }
            

        },
        load(){
            this.loading = true
            this.data = [];
            this.inputs = [];
            this.columns = [];
            this.rels = [];


            this.tablename = this.$route.params.table

            setTimeout(() => {

            this.CrudService.getTable(this.tablename).then(data => { 
                this.title = data.table.name.es
                this.inputs = data.inputs
                this.rels   = data.relations
                this.data = data.data
                this.loading = false;
             });

             }, 100);

           // console.log(this.rels)
            //this.CrudService.getTable(this.tablename).then(data => this.data = data.data);
            //this.loading = false;
        },
        edit(item) {

            //console.log('edit')
            this.$router.push({ name: 'ced', params: { id: item }});
        },
        del(item) {

            this.$confirm.require({
                message: 'Seguro quieres eliminar esto?',
                header: 'Eliminar',
                icon: 'pi pi-exclamation-triangle',
                acceptClass: 'p-button-danger',
                acceptLabel: 'Sí',
                rejectLabel: 'No',
                accept: () => {
                    //callback to execute when user confirms the action

                axios.get('/adm/crud/' + this.tablename + '/' + item + '/delete').then((response) => {
                    setTimeout(() => {
                        //this.loaded = 1
                        this.load()
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

        dupe(item) {

            this.$confirm.require({
                message: 'Seguro quieres duplicar esto?',
                header: 'Duplicar',
                icon: 'pi pi-copy',
                acceptClass: 'p-button-warning',
                acceptLabel: 'Sí',
                rejectLabel: 'No',
                accept: () => {
                    //callback to execute when user confirms the action
                    this.loading = true
                axios.get('/adm/crud/' + this.tablename + '/' + item + '/copy').then((response) => {
                    setTimeout(() => {
                        //this.loaded = 1
                        this.load()
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

    }
}
</script>
<style scoped>

.truncate span{
  overflow: hidden;

  text-overflow: ellipsis;
  white-space: nowrap;
}

.product-image {
    width: 50px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23)
}
</style>