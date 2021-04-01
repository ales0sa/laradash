<template>
    <div class="p-grid p-fluid dashboard">

<ConfirmDialog></ConfirmDialog>



        <DataTable :value="data" dataKey="id" :paginator="true" :rows="10" :filters="filters"
        :loading="loading"
         :resizableColumns="true" columnResizeMode="fit"
 >


        <template #header>

            <div class="p-d-flex p-jc-between p-mx-auto ">
                <div style=""> 
                    <h3>{{ title }}</h3>


                </div>

<div class="p-d-flex">
                    <InputText v-model="filters['global']" placeholder="Buscar..." style="    height: 30px;
    margin-right: 10px;"/>


  
<router-link :to="{ name: 'crudcreate', params: { table:  $route.params.table }}" > 

    <Button icon="pi pi-plus-circle" label="Nuevo" class=" p-button-primary p-button p-component" /></router-link>

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

                        <img :src="slotProps.data[col.field].replace('public/','storage/')" height="40px"  v-if="slotProps.data[col.field]"/>

                        <!---
                        <Button label="Ver" icon="pi pi-file" class="p-button-secondary"  @click="openPlane(slotProps.data.file)" />  --->

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
<style>

.truncate span{
  overflow: hidden;

  text-overflow: ellipsis;
  white-space: nowrap;
}


</style>