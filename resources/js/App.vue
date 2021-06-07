<template>
	<div :class="containerClass" @click="onWrapperClick">
		<AppTopBar @menu-toggle="onMenuToggle" />

        <transition name="layout-sidebar" >
            <div :class="sidebarClass" @click="onSidebarClick" v-show="isSidebarVisible()">
                <div class="layout-logo">
                    <router-link to="/">
                        <picture v-if="!imgError">
                            <img alt="Logo" :src="'/logo.png'" @error="onImgError()" style="max-width: 100%" />
                        </picture>
                        <picture v-else>
                            
                            <img alt="Logo" :src="imageUrl()" @error="onImgError()"  />

                        </picture>

                    </router-link>
                </div>

                <AppProfile />
                
                <AppMenu  :model="cm" @menuitem-click="onMenuItemClick"  />
            </div>
        </transition>


		<div class="layout-main" >
            <transition name="fade" mode="out-in">
			    <router-view :value="authGroup" />
            </transition>
		</div>



		<AppFooter />
	</div>

</template>

<script>
import axios from 'axios'
import AppTopBar from './AppTopbar.vue';
import AppProfile from './AppProfile.vue';
import AppMenu from './AppMenu.vue';
import AppConfig from './AppConfig.vue';
import AppFooter from './AppFooter.vue';
import CrudService from './service/CrudService';
import EventBus from './service/event-bus';

export default {
    data() {
        return {
            logo: window.logo,
            transitionName: null,
            auth: 1,
            isAuth: false,
            imgError: false,
            authUser: window.authUser,
            authPermissions: window.authPermissions,
            authGroup: window.authGroup,
            authRoot: 0,
            layoutMode: 'static',
            layoutColorMode: 'light',
            staticMenuInactive: false,
            overlayMenuActive: true,
            mobileMenuActive: false,
            cm: [],
            menu : [
               /* {label: 'Inicio', icon: 'pi pi-fw pi-home', to: '/'},
                {
                    label: 'Configuraciones', icon: 'pi pi-fw pi-file',
                    items: [
                        {label: 'Usuarios', icon: 'pi pi-fw pi-file', to: '/users'},
                        {label: 'Contacto', icon: 'pi pi-fw pi-file', to: '/crud/material'},
                        
                    ]
                },*/
            ]
        }
    },
    watch: {
        $route(to, from) {
            const toDepth = to.path.split('/').length
            const fromDepth = from.path.split('/').length
            this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
 
            this.menuActive = false;
            //this.$toast.removeAllGroups();
        }
    },
    mounted(){
        EventBus.$on('reloadMenu', (payload) => {
            this.reloadMenu()
        });
        EventBus.$on('axiosSuccess', (payload) => {
            //this.$toast.add({severity:'success', summary: 'Success', detail: payload.message, life: 3000})
        });
        EventBus.$on('axiosError', (payload) => {
            this.$toast.add({severity:'error', summary: 'Error', detail: payload.message, life: 3000})
        });
    },
    created(){
        //this.cm = this.menu;
        
        axios.get('/adm/whoami').then((response) => {

            this.authRoot = response.data.user.root
            this.authGroup = response.data.user.roles
            console.log(response.data)
            this.isAuth = true

        });
                    

        
        this.CrudService = new CrudService()
        this.CrudService.getMenu().then(data => this.cm = data).then( data => {
             //let list = this.cm//.filter((x, i, a) => this.menu.indexOf(x) == i)
             this.cm = data //list //this.cm.concat(this.cm)

        });


    },
    methods: {
        reloadMenu(){

        this.CrudService.getMenu().then(data => this.cm = data).then( data => {
             //let list = this.menu//.filter((x, i, a) => this.cm.indexOf(x) == i)
             this.cm = data //this.cm.concat(this.cm)

        });

        },
        checkGroup(val){
            return true
           /* let grups = authGroup.map(group => group.id)
            if(grups.includes(val) || grups.includes(1)){
                return true
            }else{
                return false
            }*/
            
        },
        isUserAdmin(){
            return false;
        },
        onImgError() {
            this.imgError = true;
        },
        onWrapperClick() {
            if (!this.menuClick) {
                this.overlayMenuActive = false;
                this.mobileMenuActive = false;
            }

            this.menuClick = false;
        },
        onMenuToggle() {
            this.menuClick = true;

            if (this.isDesktop()) {
                if (this.layoutMode === 'overlay') {
					if(this.mobileMenuActive === true) {
						this.overlayMenuActive = true;
					}

                    this.overlayMenuActive = !this.overlayMenuActive;
					this.mobileMenuActive = false;
                }
                else if (this.layoutMode === 'static') {
                    this.staticMenuInactive = !this.staticMenuInactive;
                }
            }
            else {
                this.mobileMenuActive = !this.mobileMenuActive;
            }

            event.preventDefault();
        },
        onSidebarClick() {
            this.menuClick = true;
        },
        onMenuItemClick(event) {
            if (event.item && !event.item.items) {
                this.overlayMenuActive = false;
                this.mobileMenuActive = false;
            }
        },
		onLayoutChange(layoutMode) {
			this.layoutMode = layoutMode;
		},
		onLayoutColorChange(layoutColorMode) {
			this.layoutColorMode = layoutColorMode;
		},
        addClass(element, className) {
            if (element.classList)
                element.classList.add(className);
            else
                element.className += ' ' + className;
        },
        removeClass(element, className) {
            if (element.classList)
                element.classList.remove(className);
            else
                element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
        },
        isDesktop() {
            return window.innerWidth > 1024;
        },
        isSidebarVisible() {
            if (this.isDesktop()) {
                if (this.layoutMode === 'static')
                    return !this.staticMenuInactive;
                else if (this.layoutMode === 'overlay')
                    return this.overlayMenuActive;
                else
                    return true;
            }
            else {
                return true;
            }
        },
        imageUrl(username) {
            return `https://ui-avatars.com/api/?name=${this.authUser.name}`;
        }
    },
    computed: {


        containerClass() {
            return ['layout-wrapper', {
                'layout-overlay': this.layoutMode === 'overlay',
                'layout-static': this.layoutMode === 'static',
                'layout-static-sidebar-inactive': this.staticMenuInactive && this.layoutMode === 'static',
                'layout-overlay-sidebar-active': this.overlayMenuActive && this.layoutMode === 'overlay',
                'layout-mobile-sidebar-active': this.mobileMenuActive,
				 //'p-input-filled': this.$appState.inputStyle === 'filled',
				//'p-ripple-disabled': this.$primevue.ripple === false
            }];
        },
        sidebarClass() {
            return ['layout-sidebar', {
                'layout-sidebar-dark': this.layoutColorMode === 'dark',
                'layout-sidebar-light': this.layoutColorMode === 'light'
            }];
        },

    },
    beforeUpdate() {
        if (this.mobileMenuActive)
            this.addClass(document.body, 'body-overflow-hidden');
        else
            this.removeClass(document.body, 'body-overflow-hidden');
    },
    components: {
        'AppTopBar': AppTopBar,
        'AppProfile': AppProfile,
        'AppMenu': AppMenu,
        'AppConfig': AppConfig,
        'AppFooter': AppFooter,

    }
}
</script>

<style lang="scss">
@import './App.scss';

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}


.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

</style>
