<template>
	<div class="layout-menu-container">

		<AppSubmenu :items="model" class="layout-menu" :root="true" @menuitem-click="onMenuItemClick" />
	</div>
</template>

<script>
import AppSubmenu from './AppSubmenu';

export default {
	props: {
		model: {
			type: Object,
			default: []
		},
	},
    methods: {
        onMenuItemClick(event) {
            this.$emit('menuitem-click', event);
        }
    },
	created() {
		console.log(this.model)
	},
	components: {
		'AppSubmenu': AppSubmenu
	}
}
</script>

<style scoped>

</style>