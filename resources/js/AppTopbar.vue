<template>
<div>
	<div class="layout-topbar">
		<button class="p-link layout-menu-button" @click="onMenuToggle">
			<span class="pi pi-bars"></span>
		</button>


		<div class="layout-topbar-icons">

			<a href="/" target="_blank">
			<Button class="p-link " >
				<span class="pi pi-globe"> </span> Ir al sitio
			</Button>
			</a>
			
			<Button class="p-link "  @click="$router.go(-1)">
				<span class="pi pi-chevron-left"> </span> VOLVER
			</Button>


		</div>


	</div>




	</div>
</template>

<script>
import TieredMenu from 'primevue/tieredmenu'
export default {
	    data() {
        return {
            items: [
                {
                   label:'Quit',
                   icon:'pi pi-fw pi-power-off'
                }
             ]
        }

    	},
		components: { TieredMenu },
    methods: {
		toggle(event) {
 this.$refs.usermenu.toggle(event);
},
        onMenuToggle(event) {
            this.$emit('menu-toggle', event);
        }
    }
}
</script>


<style type="text/css">

</style>