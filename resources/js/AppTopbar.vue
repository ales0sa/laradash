<template>

	<div class="layout-topbar">
		<button class="p-link layout-menu-button" @click="onMenuToggle">
			<span class="pi pi-bars"></span>
		</button>



		<div class="layout-topbar-icons">

			<!--- <a href="/" target="_blank">
			<Button class="p-link " >
				<span class="pi pi-globe"> </span> Ir al sitio
			</Button>
			</a>
			
			<Button class="p-link "  @click="$router.go(-1)">
				<span class="pi pi-chevron-left"> </span> VOLVER
			</Button> ---->



		</div>
		<div class="layout-topbar-profile">
			<Button v-if="this.$parent.authRoot && this.$parent.authRoot !== 1 "  :label="this.$parent.authGroup[0].name"
			icon="fa fa-sm fa-group" class="p-button" style="background: white; color: black;"
			
			/>
			<router-link :to="{ name: 'crudgenerator' }" v-if="this.$parent.authRoot == 1" >
				 <Button label="Developer" icon="fa fa-sm fa-code" class="p-button" style="background: white; color: black;"/>
			</router-link>
				
 			<SplitButton :label="this.$parent.authUser.name" 
 			icon="pi pi-user" :model="usermenu" class="profile-button">
	 		</SplitButton>
	 	</div>

	</div>

</template>

<script>
import TieredMenu from 'primevue/tieredmenu'
export default {
	    data() {
        return {
            usermenu: [
                {
                   label:'Salir',
                   icon:'pi pi-fw pi-power-off',
				   url: '/adm/logout'
                }
             ]
        }

    	},
		components: { TieredMenu },
    methods: {

        onMenuToggle(event) {
            this.$emit('menu-toggle', event);
        }
    }
}
</script>


<style type="scss" scoped>

</style>