<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goCreate"
                >
                    <v-icon color="primary">add</v-icon>
                </v-btn>
            </template>
                <span>Nuevo</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0"
                    v-on="on"
                    color="white"
                    icon
                    :disabled="computedDisabledBorrar"
                    @click="openDialog"
                >
                    <v-icon color="primary">delete</v-icon>
                </v-btn>
            </template>
                <span>Borrar Registro</span>
        </v-tooltip>
        <v-tooltip bottom v-if="id > 0">
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goIndex"
                >
                    <v-icon color="primary">list</v-icon>
                </v-btn>
            </template>
            <span>Lista</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0 && cliente_id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="printGD"
                >
                    <v-icon color="primary">print</v-icon>
                </v-btn>
            </template>
            <span>Imprimir garantía de depósito</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goBack()"
                >
                    <v-icon color="primary">arrow_back</v-icon>
                </v-btn>
            </template>
                <span>Volver</span>
        </v-tooltip>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import {mapGetters} from 'vuex';
export default {
    props:{
        id: Number,
        cliente_id: Number,
        estado_id: Number
    },
    components: {
        'my-dialog': MyDialog
    },
    data () {
      return {
          dialog: false,
          ruta: "producto",
          url: "/mto/productos",
      }
    },
    computed: {
    ...mapGetters([
            'hasEdtPro',
        ]),
        computedDisabledBorrar(){

            if (this.estado_id > 2) return true;

            return !this.hasEdtPro;
        }
    },
    methods:{
        goBack(){
           this.$router.go(-1)
        },
        goCreate(){
            this.$router.push({ name: this.ruta+'.create' })
        },
        goIndex(){
            this.$router.push({ name: this.ruta+'.index' })
        },
        openDialog (){
            this.dialog = true;
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.id,{_method: 'delete'})
                .then(response => {
                this.$router.push({ name: this.ruta+'.index' })
                this.$toast.success(response.data.msg);

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        printGD(){
            var url = '/mto/productos/print/'+this.id;
            window.open(url, '_blank');
        },

    }
}
</script>
