<template>
    <div class="">

<Toolbar>
    <template #left>
    <router-link :to="{ name: 'cren' }"> <Button icon="pi pi-plus" class="p-button-primary p-mr-2" label="New CRUD"/></router-link>
    <router-link :to="{ name: 'crdb' }"> <Button icon="pi pi-clone" class="p-button-secondary" label="CRUD From DB"/></router-link>
    </template>

    <template #right>
        <router-link :to="{ name: 'laralogs' }">  <Button icon="pi pi-info-circle" class="p-button-outlined p-mr-2" label="Logs" /></router-link>
        <Button icon="pi pi-refresh" class="p-button-outlined p-button-danger p-mr-2" label="Cache Clear" @click="cleanCache()" />
        <Button icon="pi pi-link" class="p-button-outlined p-button-help p-mr-2" label="Storage Link" @click="makeSymlink()" />
        <!---<Button icon="pi pi-calendar" class="p-button-success p-mr-2" />
        <Button icon="pi pi-times" class="p-button-danger" /> --->
    </template>
</Toolbar>

        <div class="p-md-3">
            
        </div>
        <div class="p-md-3">
           
        </div>


        <DataTable 
        :value="data2"  
        :paginator="true"
        :rows="10" 
        class="p-datatable-gridlines"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5,10,25]" >
            <template #empty>
            Sin resultados
        </template>

            <Column v-for="col of columns" :field="col.field" :header="col.header" :key="col.field">
            </Column>

            <Column headerStyle="width: 20%;" bodyStyle="text-align: center; overflow: visible">
            <template #body="slotProps">          
                <router-link :to="{ name: 'cre', params: { file: slotProps.data.header }}">  <Button icon="pi pi-pencil" class="p-button-rounded p-button-success p-mr-2" /> </router-link>
                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="del(slotProps.data.header)" />
            </template>
            </Column>

        </DataTable>

    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        data(){
            return{
                languages: {},
        columns: [

            {field: 'value', header: 'File'}           
            
        ],
                data: [],
                data2: [],
                inputs: [],
                loaded: 0
            }
        },
        methods: {
            cleanCache(){
                axios.get('/adm/ccache').then((response) => {
                    
                    if(response.data.status == 'success') {
                        console.log(response.data)
                    }
                });
            },
            makeSymlink(){
                axios.get('/adm/symlink').then((response) => {
                    
                    if(response.data.status == 'success') {
                        console.log(response.data)
                    }
                });
            },
            del(what){
                axios.get('/adm/crud-generator/'+what+'/delete').then((response) => {
                    
                    if(response.data.status == 'success') {
                        this.data  = response.data.data
                        this.data2 = []
                        for (var index = 0; index < this.data.length; index++) {
                            
                                this.data2.push({ value: this.data[index], header: this.data[index] });

                        }

                        EventBus.$emit('reloadMenu');
                        
                    }


                });
            }
        },
        mounted() {
            console.log('Component mounted.') 
            this.$nextTick(() => {
                axios.get('/adm/crud-generator').then((response) => {
                    
                    if(response.data) {
                        this.data  = response.data
                    }

               for (var index = 0; index < this.data.length; index++) {
                   
                    this.data2.push({ value: this.data[index], header: this.data[index] });

                }


                });
            });

        }
    }
</script>
