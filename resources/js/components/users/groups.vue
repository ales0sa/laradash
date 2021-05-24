<template>
    <div class="">

<ConfirmDialog></ConfirmDialog>


<Dialog :visible.sync="productDialog" :style="{width: '550px'}" header="Nuevo rol" :modal="true" class="p-fluid">

    <div class="p-field">
        <label for="name">Nombre</label>
        <InputText id="name" v-model.trim="uform.name" required="true" autofocus :class="{'p-invalid': submitted && !uform.name}" />
        <small class="p-invalid" v-if="submitted && !uform.name">Nombre obligatorío.</small>
    </div>

    <template #footer>
        <Button label="Cancelar" icon="pi pi-times" class="p-button-danger"/>
        <Button label="Generar" icon="pi pi-check" class="p-button-success" @click="newRol()" />
    </template>
</Dialog>



        <DataTable :value="groups" dataKey="id" :paginator="true" :rows="10" :filters="filters"
        class="p-datatable-gridlines"
                 :resizableColumns="false" columnResizeMode="fit"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" :rowsPerPageOptions="[5,10,25]" >


        <template #header>

            <div class="p-d-flex p-jc-between p-mx-auto">
                <div style=""> 
                    <h3> Grupos </h3>
                </div>
                <div>
                    <router-link :to="{ name: 'users' }"><Button icon="pi pi-user" label="Usuarios"  class="p-button-secondary" /></router-link>
                    <Button label="Nuevo" icon="pi pi-plus" class="p-mr-2 p-button-primary" @click="openNew" />
                </div>
            </div>
        </template>


        <template #empty>
           Sin resultados
       </template>

       <Column field="name" header="Nombre"></Column>


        <Column> 
        <template #body="slotProps">

            
            <span class="p-buttonset">
                <Button icon="pi pi-pencil" class="p-button-outlined p-button-raised p-button-sm p-button-success" @click="edit(slotProps.data.id)" />
                <Button icon="pi pi-trash" class="p-button-outlined p-button-raised p-button-sm p-button-danger" @click="del(slotProps.data.id)" />
            </span>
        </template>
    </Column>

</DataTable>

</div>
</template>

<script>

import UserService from './../../service/UserService';
import ConfirmDialog from 'primevue/confirmdialog';

    export default {



        data() {
            return {
            uform: {
                name: '', 
                username: '', 
                password: '', 
                role: ''


            },
            sectores: null,
            products: null,
            productDialog: false,
            deleteProductDialog: false,
            deleteProductsDialog: false,
            groups: [],
            selectedProducts: null,
            filters: {},
            submitted: false,
                columns: [],
                data: [],
                inputs:  [],
                rels:  [],
                filters: {},
                tablename: 'users', 
                title: ''
            }
            
        },
        components:  { ConfirmDialog },
        watch:{ 
            '$route.params.table': function (table){

                this.load()

            },



        },
        created() {

            this.UserService = new UserService();
            this.load()
        },
        mounted() {

          // this.UserService.getGroups().then(data => this.groups = data);
            //this.tablename = this.$route.params.table
            //this.UserService.getTable(this.tablename).then(data => this.inputs = data.inputs);
           // this.UserService.getData(this.tablename).then(data => this.data = data);
           //this.load()

    },
    methods:{
        openNew() {
            this.product = {};
            this.submitted = false;
            this.productDialog = true;
        },

        load(){
            this.productDialog = false
            this.data = [];
            this.inputs = [];
            this.columns = [];
            this.rels = [];
            this.uform = [];

            this.UserService.getGroups().then(data => this.groups = data);


        },
        newRol(){
                //console.log(this.uform)
                let formData = new FormData()
                Object.keys(this.uform).forEach((key) => {
                    formData.append(key, this.uform[key]);
                })


                

                axios.post('/adm/groups', formData).then((response) => {
                    this.loaded = 3
                    setTimeout(function(){
                        this.loaded = 1
                        //console.log(response)
                        if(response.statusText == 'Created'){
                            this.load()
                        }
                    }.bind(this), 1000);
                })

        },
        edit(item) {


        },
        del(item) {


        },



    }
}
</script>
