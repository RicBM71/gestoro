<template>
    <div>
        <loading :show_loading="show_loading"></loading>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <reacli-dialog :dialog_reacli.sync="dialog_reacli" :cliente_id_nuevo.sync="cliente_id_nuevo" @goReaCli="goReaCli"></reacli-dialog>
        <update-fase
            v-if="albaran.id > 0 && isRoot"
            :albaran.sync="albaran"
            :dialog_fas.sync="dialog_fas"
        >
        </update-fase>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="albaran.id>0"
                    :disabled="!computedReaCli"
                    v-on="on"
                    color="white"
                    icon
                    @click="openDialogReaCli"
                >
                    <v-icon color="red darken-4">how_to_reg</v-icon>
                </v-btn>
            </template>
            <span>Reasignar Cliente</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="albaran.id>0"
                    :disabled="!computedMail"
                    v-on="on"
                    color="white"
                    icon
                    @click="goEmail"
                >
                    <v-icon color="primary">mail</v-icon>
                </v-btn>
            </template>
            <span>Enviar por email</span>
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
                    v-show="albaran.id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="goCliente"
                >
                    <v-icon color="primary">person</v-icon>
                </v-btn>
            </template>
            <span>Ir a cliente</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-on="on"
                    color="white"
                    icon
                    @click="goFind"
                >
                    <v-icon color="primary">search</v-icon>
                </v-btn>
            </template>
                <span>Buscar por número</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="albaran.id > 0"
                    :disabled="!computedAuthBorrar"
                    v-on="on"
                    color="white"
                    icon
                    @click="openDialog"
                >
                    <v-icon color="red darken-4">delete</v-icon>
                </v-btn>
            </template>
                <span>Borrar Registro</span>
        </v-tooltip>
        <v-tooltip bottom v-if="albaran.id > 0">
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
                    v-show="albaran.id > 0"
                    v-on="on"
                    color="white"
                    icon
                    @click="printAlbPDF"
                >
                    <v-icon color="primary">print</v-icon>
                </v-btn>
            </template>
            <span>Imprimir albarán/factura</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="albaran.id > 0 && albaran.tipo_id==5"
                    v-on="on"
                    color="white"
                    icon
                    @click="printTallerPDF"
                >
                    <v-icon color="primary">build</v-icon>
                </v-btn>
            </template>
            <span>Imprimir hoja de taller</span>
        </v-tooltip>
        <v-tooltip bottom v-if="isRoot && albaran.id > 0">
            <template v-slot:activator="{ on }">
                <v-btn

                    v-on="on"
                    color="white"
                    icon
                    @click="dialog_fas=!dialog_fas"
                >
                    <v-icon color="red">warning</v-icon>
                </v-btn>
            </template>
                <span>Corregir Fase</span>
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
import ReaCli from '@/components/shared/ReaCli'
import Loading from '@/components/shared/Loading'
import UpdateFase from './UpdateFase'
import {mapGetters} from 'vuex';
export default {
    props:{
        albaran: Object,
        refresh: Number,
    },
    components: {
        'my-dialog': MyDialog,
        'reacli-dialog': ReaCli,
        'update-fase': UpdateFase,
        'loading': Loading,
    },
    data () {
      return {
          dialog: false,
          dialog_reacli: false,
          cliente_id_nuevo: 0,
          ruta: "albaran",
          url: "/ventas/albaranes",
          dialog_fas: false,
          hoy: new Date().toISOString().substr(0, 10),
          show_loading: false,
      }
    },
    computed: {
        ...mapGetters([
            'isAdmin',
            'hasBorraCompras',
            'userName',
            'isRoot'
        ]),
        computedReaCli(){
            if (this.isAdmin)
                return (this.albaran.id > 0);
            else
                return (this.albaran.id > 0 && this.albaran.factura == 0 && this.albaran.fase_id == 10);
        },
        computedMail(){

            if (this.albaran.id > 0 && this.albaran.cliente.email > '' && this.albaran.fecha_notificacion == null)
                return true;
            return false;
        },
        computedImprimeCompra(){
            return false;
        },
        computedAuthBorrar(){

            if ( this.albaran.id == 0) return false;

            //if (this.isRoot) return true;

            if (this.albaran.factura > 0 || this.albaran.id == 0) return false;

            if (this.albaran.username == this.userName && this.albaran.created_at.substr(0, 10) == this.hoy)
                return true;
            else
                return (this.isAdmin);

        }
    },
    methods:{
        goBack(){
            this.$router.go(-1);
        },
        goCliente(){
            // if (!this.isAdmin)
            //     this.$router.push({ name: 'cliente.show', params: { id: this.albaran.cliente_id } })
            // else
                this.$router.push({ name: 'cliente.edit', params: { id: this.albaran.cliente_id } })
        },
        goCreate(){
            this.$router.push({ name: this.ruta+'.create', params: { cliente_id: this.albaran.cliente_id }  })
        },
        goIndex(){
            this.$router.push({ name: this.ruta+'.index' })
        },
        goFind(){
            this.$router.push({ name: this.ruta+'.find' })
        },
        printAlbPDF(){
            var url = '/ventas/print/'+this.albaran.id+'/albaran';
            window.open(url, '_blank');
        },
        printTallerPDF(){
            var url = '/ventas/print/'+this.albaran.id+'/taller';
            window.open(url, '_blank');
        },
        openDialog (){
            this.dialog = true;
        },
        goEmail(){
            this.show_loading = true;
            axios.put('/ventas/print/'+this.albaran.id+'/mail')
                .then(res => {
                    console.log(res);
                    this.$emit('update:albaran', res.data.albaran);
                    this.$toast.success('Mail en cola de envío...');
                })
                .catch(err => {
                    this.$toast.error(err.response.data);
                })
                .finally(()=> {
                    this.show_loading = false;
                });

        },
        openDialogReaCli(){
            this.dialog_reacli = true;
        },
        goReaCli(){
            this.show_loading = true;
            axios.put('/utilidades/reacli',
                            {   id: this.albaran.id,
                                cliente_id: this.cliente_id_nuevo,
                                tipo_op: "V"
                            })
                .then(res => {
                    this.$emit('update:albaran', res.data.albaran);
                })
                .catch(err =>{
                    this.$toast.error(err.response.data.message);
                })
                .finally(()=> {
                    this.show_loading = false;
                });

        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.albaran.id,{_method: 'delete'})
                .then(res => {
                    if (res.data.estado)
                        this.$toast.success('Registro eliminado! '+res.data.msg);
                    else
                        this.$toast.warning(res.data.msg);

                this.$router.push({ name: this.ruta+'.index' })

            })
            .catch(err => {
                this.status = true;
                var msg = err.response.data.message;
                this.$toast.error(msg);

            });

        },

    }
}
</script>
