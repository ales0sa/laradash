
//import Vue from 'vue';

window.Vue = require('vue').default;
//

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

//import './assets/layout/layout.scss';


import PrimeVue from 'primevue/config';

import ColorPicker from 'primevue/colorpicker';
import Listbox from 'primevue/listbox';
import Skeleton from 'primevue/skeleton';
import Password from 'primevue/password';
import TieredMenu from 'primevue/tieredmenu';
import Slider from 'primevue/slider';
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import Message from 'primevue/message';
import Button from 'primevue/button';
import AutoComplete from 'primevue/autocomplete';
import DataView from 'primevue/dataview';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import Chips from 'primevue/chips';
import InputSwitch from 'primevue/inputswitch';
import InputMask from 'primevue/inputmask';
import InputNumber from 'primevue/inputnumber';
import MultiSelect from 'primevue/multiselect';
import Dropdown from 'primevue/dropdown';
import Card from 'primevue/card';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';

import RadioButton from 'primevue/radiobutton';
import FileUpload from 'primevue/fileupload';
import DataViewLayoutOptions from 'primevue/dataviewlayoutoptions';

import Editor from 'primevue/editor';
import SelectButton from 'primevue/selectbutton';
import Checkbox from 'primevue/checkbox';
import Carousel from 'primevue/carousel';
import Divider from 'primevue/divider';
import Fieldset from 'primevue/fieldset';
import Panel from 'primevue/panel';
import Vuelidate from 'vuelidate'
import moment from 'moment'
import App from './App.vue';
import ProgressSpinner from 'primevue/progressspinner'


import ConfirmationService from 'primevue/confirmationservice';
import ConfirmDialog from 'primevue/confirmdialog';
window.Vue.use(ConfirmationService);
window.Vue.use(ToastService);
//window.Vue.use(Message);
window.Vue.use(PrimeVue)
window.Vue.use(moment)
window.Vue.use(Vuelidate)

import Sidebar from 'primevue/sidebar';

window.Vue.component('Sidebar', Sidebar );


import Chart from 'primevue/chart';

window.Vue.component('Chart', Chart);


import ProgressBar from 'primevue/progressbar';
import ScrollPanel from 'primevue/scrollpanel';

window.Vue.component('ScrollPanel', ScrollPanel );
window.Vue.component('ProgressBar', ProgressBar );


import VueHtmlToPaper from 'vue-html-to-paper';

const optionsprint = {
    name: '_blank',
    specs: [
      'fullscreen=yes',
      'titlebar=yes',
      'scrollbars=yes'
    ],
    styles: [
      '/css/layout.css',
      '/css/prints.css'
      //''
    ],
    timeout: 300, // default timeout before the print window appears
    autoClose: true, // if false, the window will not close after printing
    windowTitle: window.document.title, // override the window title
  }
  
  Vue.use(VueHtmlToPaper, optionsprint);

window.Vue.component('ColorPicker', ColorPicker );
window.Vue.component('Message', Message );
window.Vue.component('Skeleton', Skeleton );
window.Vue.component('ProgressSpinner', ProgressSpinner);
window.Vue.component('Listbox', Listbox);
window.Vue.component('Password', Password);
window.Vue.component('SplitButton', SplitButton);
window.Vue.component('Panel', Panel);
window.Vue.component('Fieldset', Fieldset);
window.Vue.component('ConfirmDialog', ConfirmDialog);
window.Vue.component('Carousel', Carousel);
window.Vue.component('Checkbox', Checkbox);
window.Vue.component('SelectButton', SelectButton);
window.Vue.component('Editor', Editor);
window.Vue.component('Toast', Toast);
window.Vue.component('DataViewLayoutOptions', DataViewLayoutOptions);
window.Vue.component('InputSwitch', InputSwitch);
window.Vue.component('AutoComplete', AutoComplete);
window.Vue.component('Chips', Chips);
window.Vue.component('InputMask', InputMask);
window.Vue.component('FileUpload', FileUpload);
window.Vue.component('InputNumber', InputNumber);
window.Vue.component('MultiSelect', MultiSelect);
window.Vue.component('RadioButton', RadioButton);
window.Vue.component('Card', Card);
window.Vue.component('Toolbar', Toolbar);
window.Vue.component('Toolbar', Toolbar);
window.Vue.component('Dialog', Dialog);
window.Vue.component('Button', Button);

// los odio a todos




window.Vue.component('Calendar', Calendar);
window.Vue.component('Dropdown', Dropdown);
window.Vue.component('Column', Column);
window.Vue.component('DataTable', DataTable);
window.Vue.component('DataView', DataTable);
window.Vue.component('InputText', InputText);
window.Vue.component('Divider', Divider);
//window.Vue.component('Slider', Divider);
import ToggleButton from 'primevue/togglebutton';
window.Vue.component('ToggleButton', ToggleButton);
import Chip from 'primevue/chip';
window.Vue.component('Chip', Chip);

window.Vue.component('dashtable', require('./components/DashTable.vue').default)
window.Vue.component('users', require('./components/users/index.vue').default)
window.Vue.component('InputLayout', require('./components/crud/InputLayout.vue').default)

 const eventHub = new Vue() // Single event hub

 // Distribute to components using global mixin
 Vue.mixin({
     data: function () {
         return {
             eventHub: eventHub
         }
     }
 })

 Vue.filter('toMoney', function (value) { 
        let val = (value/1).toFixed(2).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")

  })

Vue.component('file-manager', require('./components/file-manager').default);


import router from './router';


import vueFilePond from 'vue-filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';

import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginImageCrop);
Vue.component('filePond', FilePond);


new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
