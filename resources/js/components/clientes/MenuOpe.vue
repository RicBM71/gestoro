<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
         <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="goCreateCompra"
                >
                    <v-icon color="primary">add_shopping_cart</v-icon>
                </v-btn>
            </template>
                <span>Nueva Compra</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="goCreateVenta"
                >
                    <v-icon color="primary">credit_card</v-icon>
                </v-btn>
            </template>
                <span>Nueva Venta</span>
        </v-tooltip>
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
                    v-show="id > 0 && isRoot"
                    v-on="on"
                    color="white"
                    icon
                    @click="openDialog"
                >
                    <v-icon color="primary">delete</v-icon>
                </v-btn>
            </template>
                <span>Borrar Registro</span>
        </v-tooltip>
        <v-tooltip bottom>
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
            <span>Clientes</span>
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
import {mapGetters} from 'vuex'
export default {
    props:{
        id: Number
    },
    components: {
        'my-dialog': MyDialog
    },
    data () {
      return {
          dialog: false,
      }
    },
    computed: {
        ...mapGetters([
            'isAdmin',
            'isRoot'
        ]),
    },
    methods:{
        goBack(){
            this.$router.go(-1);
        },
        goCreate(){
            this.$router.push({ name: 'cliente.create' })
        },
        goIndex(){
            this.$router.push({ name: 'cliente.index' })
        },
        openDialog (){
            this.dialog = true;
        },
        destroyReg () {
            this.dialog = false;

            axios.post('/mto/clientes/'+this.id,{_method: 'delete'})
                .then(response => {
                this.$router.push({ name: 'cliente.index' })
                this.$toast.success('Cliente eliminado!');

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },
        goCreateCompra(){
            this.$router.push({ name: 'compra.create', params: { cliente_id: this.id }  })
        },
         goCreateVenta(){
            this.$router.push({ name: 'albaran.create', params: { cliente_id: this.id }  })
        },
        // printPDF(){

        //     var url = '/util/mandato/'+this.id+'/print';

        //     window.open(url, '_blank');
        // },

    }
}
</script>
