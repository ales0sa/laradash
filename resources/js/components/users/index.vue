<template>
    <div class="">

<ConfirmDialog></ConfirmDialog>
        <Toast position="top-right" />

<Dialog :visible.sync="productDialog" :style="{width: '550px'}" header="Nuevo usuario del sistema" :modal="true" class="p-fluid">

<div class="p-field">
    <label for="name">Nombre</label>
    <InputText id="name" v-model.trim="uform.name" required="true" autofocus :class="{'p-invalid': submitted && !uform.name}" />
    <small class="p-invalid" v-if="submitted && !uform.name">Nombre obligatorío.</small>
</div>

    <div class="p-formgrid p-grid">
    <div class="p-field p-col">
    <!-- <Dropdown v-model="uform.role" :options="sectores" optionLabel="name" optionValue="id" placeholder="Seleccione rol" />--->
    <MultiSelect 
    :value="uform.role"
    display="chip"
    v-model="selectedRoles" 
    :options="sectores"
    optionLabel="name"
    optionValue="id"
    placeholder="Seleccione rol"
    />
</div>

</div>
<div class="p-formgrid p-grid">
    <div class="p-field p-col">
        <label for="price">Usuario</label>
        <InputText id="price" v-model="uform.username" />
    </div>
    <div class="p-field p-col" style="min-height: 100px;">
        <label for="quantity">Contraseña</label>
        
        <Password v-model="uform.password"
         weakLabel="Debil"
         mediumLabel="Media"
         strongLabel="Fuerte"
         promptLabel="Ingresa una contraseña"
         toggleMask></Password>
    </div>
</div>
<template #footer>
    <Button label="Cancelar" icon="pi pi-times" class="p-button-danger" @click="productDialog = false"/>
    <Button label="Generar" icon="pi pi-check" class="p-button-success" @click="newUser()" />
</template>
</Dialog>

<div class="p-grid card card-w-title">

    <DataTable :value="data" dataKey="id" :paginator="true" :rows="10" :filters="filters"
                
                :resizableColumns="true" columnResizeMode="fit"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown" 
                :rowsPerPageOptions="[10,25,50,100]">


        <template #header>

            <div class="p-d-flex p-jc-between p-mx-auto card-header">
                <div style=""> 
                    <h3> Usuarios </h3>
                </div>
                <div>
                    <router-link :to="{ name: 'grupos' }"><Button icon="pi pi-key" label="Roles"  class="p-button-secondary" /></router-link>
                    <Button label="Nuevo" icon="pi pi-plus" class="p-mr-2 p-button-primary" @click="openNew" />
                </div>
            </div>
        </template>


        <template #empty>
           Sin resultados
       </template>

       <Column field="name" header="Nombre"></Column>
       <Column field="email" header="E-mail"></Column>
       <Column field="username" header="Usuario"></Column>
       <Column field="grupito" header="Roles">

            <template #body="slotProps">
                
                    <Chip v-for="(rol, index) in slotProps.data.roles" :key="rol.id">
                        {{ rol.name }}
                    </Chip>
                
            </template>
       
       </Column>

        <Column> 
        <template #body="slotProps">

            
            <span class="p-buttonset">
                

                <Button icon="pi pi-pencil" class="p-button-outlined p-button-raised p-button-sm p-button-success" @click="edit(slotProps.data)" />
                <Button icon="pi pi-trash" class="p-button-outlined p-button-raised p-button-sm p-button-danger" @click="del(slotProps.data.id)" />
                <Button icon="pi pi-sign-in" class="p-button-outlined p-button-raised p-button-sm p-button-secondary" @click="login(slotProps.data.id)" />
            </span>
        </template>
    </Column>

</DataTable>
</div>
</div>
</template>

<script>

import UserService from '../../service/UserService';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
    export default {


        //props: [ '' ],
        data() {
            return {
            uform: {
                name: null,
                username: null,
                password: null,
                role: null,


            },
            sectores: null,
            products: null,
            productDialog: false,
            deleteProductDialog: false,
            deleteProductsDialog: false,
            product: {},
            selectedProducts: null,
            selectedRoles: null,
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

        },
        mounted() {

           //this.UserService.getGroups().then(data => this.sectores = data);
            //this.tablename = this.$route.params.table
            //this.UserService.getTable(this.tablename).then(data => this.inputs = data.inputs);
           // this.UserService.getData(this.tablename).then(data => this.data = data);
           this.load()

    },
    methods:{
        login(id){
                console.log(id)
                axios.get('/adm/loginas/' + id).then((response) => {
                
                    if(response.data.status == 'success'){
                        window.location.href = '/adm/'
                    }

                }).catch((error) => {

                    console.log(error)

                });

        },
        openNew() {
            this.uform = {};
            this.selectedRoles = null
            this.submitted = false;
            this.productDialog = true;
        },

        load(){
            this.data = [];
            this.inputs = [];
            this.columns = [];
            this.rels = [];
            this.UserService.getGroups().then(data => this.sectores = data);

            this.UserService.getUsers().then(data => { 
                this.data = data
             });


        },
        newUser(){
                console.log(this.uform)
                this.uform.role = this.selectedRoles
                let formData = new FormData()
                Object.keys(this.uform).forEach((key) => {
                    formData.append(key, this.uform[key]);
                })


                

                axios.post('/adm/user/', formData).then((response) => {
                    this.loaded = 3
                    setTimeout(function(){
                        this.loaded = 1
                        //console.log(response)
                        if(response.data.status = 'success'){
                            this.load()
                            this.productDialog = false;
                        }




                    }.bind(this), 1000);
                }).catch((error) => {

                        if (error.response.data.message == 'The given data was invalid.'){
                            let parsedErrors  = '';
                            let errorData = error.response.data.errors

                        Object.keys(error.response.data.errors).forEach(item =>  {
                                this.$toast.add({severity:'error', summary: 'Error', detail: errorData[item][0], life: 5000})
                                }
                            )
                        }

                });

        },
        edit(item) {

            this.uform = {...item};
            this.selectedRoles = item.roles.flatMap(x => x.id)
            this.productDialog = true;

        },
        del(item) {
                console.log(item)
                axios.get('/adm/user/'+item+'/delete').then((response) => {

                    console.log(response.data.status)

                    this.load()

                })

        },



    }
}
</script>
