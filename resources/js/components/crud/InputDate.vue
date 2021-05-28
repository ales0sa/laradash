<template>

         <div class="p-float-label p-mt-3" v-if="input.visible_edit == 1" > 

    <input type="time" class="p-inputtext p-component p-filled" 
   
   v-model="value.value"
   v-if="input.type == 'time'"
    />
    
    <input type="datetime-local" class="p-inputtext p-component p-filled" 
   
   v-model="tempDateTime"
   v-else-if="input.type == 'datetime'"
    />

    <input type="date" class="p-inputtext p-component p-filled" 
   
   v-model="tempDate"
   v-else-if="input.type == 'date'"
    />

            <label class="">{{ input.label.es }}</label>  
          
        </div>

        
   
</template>
<script>
    import moment from 'moment'
    


    export default {
        props: {
            input: {
                type: Object,
                default: {}
            },
            value: {
                type: Object,
                default: {}
            }
        },
        components: {},
        data(){
            return{
                val: this.value.value,
                tempDate: moment(String(this.value.value)).format('YYYY-MM-DD'),//this.value.value,
                tempDateTime: moment(String(this.value.value)).format('YYYY-MM-DDTHH:mm:ss.SSS'),//this.value.value,
                tempTime: moment(String(this.value.value)).format('hh:mm'),//this.value.value,
                es: {
                    firstDayOfWeek: 1,
                    dayNames: [ "Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado" ],
                    dayNamesShort: [ "Dom","Lun","Mar","Mié","Jue","Vie","Sáb" ],
                    dayNamesMin: [ "D","L","M","X","J","V","S" ],
                    monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre" ],
                    monthNamesShort: [ "Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic" ],
                    today: 'Hoy',
                    clear: 'Borrar',
                    weekHeader: 'Sm'
                },
                columna: null,
                cuit: null,
                disabled: false,
                domicilioFiscal: {},
            }
        },
        created() {

        },
        filters: {


        },

        mounted () {



            
            

        },
      watch: {
      	tempDate: function(newVal, oldVal) { // watch it
          console.log('Prop changed: ', newVal, ' | was: ', oldVal)
          this.value.value = newVal
        },
      	tempDateTime: function(newVal, oldVal) { // watch it
          console.log('Prop changed: ', newVal, ' | was: ', oldVal)
          this.value.value = newVal
        }
      },
        methods: {
            changed(event){
              //  this.$emit('update:myProp',this.myProp)
              console.log(event)
            },
            toInputDate(value){
                if (value) {
                    return moment(String(value)).format('YYYY-MM-DD HH:mm:ss')
                }
            },
            lang() {
                return document.documentElement.lang
            }
        },
        computed: {

        }
    }

</script>
<style lang="css" scoped>

</style>
