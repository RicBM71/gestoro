<template>
    <div>
        <my-dialog :dialog.sync="dialog" registro="registro" @destroyReg="destroyReg"></my-dialog>
        <reacli-dialog :dialog_reacli.sync="dialog_reacli" :cliente_id_nuevo.sync="cliente_id_nuevo" @goReaCli="goReaCli"></reacli-dialog>
        <trasladar-dialog :dialog_trasladar.sync="dialog_trasladar" :compra="compra"></trasladar-dialog>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn

                    v-show="compra.id > 0  && hasAddCom"
                    :disabled="!computedTraslado"
                    v-on="on"
                    color="white"
                    icon
                    @click="dialog_trasladar=!dialog_trasladar"
                >
                    <v-icon color="orange darken-4">explore</v-icon>
                </v-btn>
            </template>
            <span>Trasladar Compra</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="compra.id > 0  && hasAddCom"
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
                    v-show="hasAddCom"
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
                    v-show="compra.id > 0"
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
                    v-show="compra.id > 0 && hasAddCom"
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
        <v-tooltip bottom v-if="compra.id > 0">
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
                    v-show="computedImprimeCompra && hasAddCom"
                    v-on="on"
                    color="white"
                    icon
                    @click="printPDF"
                >
                    <v-icon color="primary">print</v-icon>
                </v-btn>
            </template>
            <span>Imprimir documentos de compra</span>
        </v-tooltip>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="compra.fase_id == 5 && compra.factura > 0  && hasAddCom"
                    v-on="on"
                    color="white"
                    icon
                    @click="printRecuPDF"
                >
                    <v-icon color="primary">print</v-icon>
                </v-btn>
            </template>
            <span>Imprimir factura de recuperación de compra</span>
        </v-tooltip>

        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn
                    v-show="computedScanDocu  && hasAddCom"
                    v-on="on"
                    color="white"
                    icon
                    @click="scanDocu()"
                >
                    <v-icon color="red darken-4">add_a_photo</v-icon>
                </v-btn>
            </template>
            <span>Aportar documentos DNI/NIE/Pasaporte</span>
        </v-tooltip>
        <v-tooltip bottom v-if="isRoot && compra.id > 0">
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
        <update-fase
            :compra.sync="compra"
            :dialog_fas.sync="dialog_fas"
        >
        </update-fase>
    </div>
</template>
<script>
import MyDialog from '@/components/shared/MyDialog'
import ReaCli from '@/components/shared/ReaCli'
import Trasladar from './Trasladar'
import UpdateFase from './UpdateFase'
import {mapGetters} from 'vuex';
export default {
    props:{
        compra: Object,
        docu_ok: Boolean,
        refresh: Number,

    },
    components: {
        'my-dialog': MyDialog,
        'update-fase': UpdateFase,
        'reacli-dialog': ReaCli,
        'trasladar-dialog': Trasladar,
    },
    data () {
      return {
          dialog_reacli: false,
          dialog_trasladar: false,
          cliente_id_nuevo: 0,
          dialog: false,
          ruta: "compra",
          url: "/compras/compras",
          dialog_fas: false,

      }
    },
    computed: {
        ...mapGetters([
            'hasReaCom',
            'hasDelCom',
            'userName',
            'isRoot',
            'hasScan',
            'hasAddCom'
        ]),
        computedReaCli(){
            return (this.compra.id > 0 && this.compra.fase_id <= 4 && this.hasReaCom && this.compra.factura == null);
        },
        computedTraslado(){

            const hoy = new Date().toISOString().substr(0, 10);

            return (this.compra.id > 0 && this.compra.fase_id <= 4 && this.hasReaCom && this.compra.factura == null && this.compra.fecha_compra == hoy);
        },
        computedImprimeCompra(){

            //if ((this.compra.fase_id == 3 || this.compra.fase_id == 4) && (this.docu_ok || this.hasScan==false))
            if (this.compra.fase_id == 3 && (this.docu_ok || this.hasScan==false))
                return true;

            if (this.compra.fase_id >= 4)
                return true;

            return false;
        },
        computedScanDocu(){
            if (!this.hasScan) return false;

            if (this.compra.fase_id == 3 && !this.docu_ok)
                return true;
            return false;
        },
        computedAuthBorrar(){
            if (this.compra.id > 0){

                if (this.compra.fase_id > 4)
                    return false;

                if (this.compra.created_at.substr(0, 10) == new Date().toISOString().substr(0, 10) && this.compra.username==this.userName)
                    return true;

                if (this.compra.created_at.substr(0, 10) == new Date().toISOString().substr(0, 10) && this.hasReaCom)
                    return true;

                if  (this.hasDelCom)
                    return true;

            }

            return false;
        }
    },
    methods:{
        goBack(){
            this.$router.go(-1);
        },
        goCliente(){
            this.$router.push({ name: 'cliente.edit', params: { id: this.compra.cliente_id } })
        },
        goCreate(){
            this.$router.push({ name: this.ruta+'.create', params: { cliente_id: this.compra.cliente_id }  })
        },
        goIndex(){
            this.$router.push({ name: this.ruta+'.index' })
        },
        goFind(){
            this.$router.push({ name: 'find.compra' })
        },
        printPDF(){
            var url = '/compras/print/'+this.compra.id;
            window.open(url, '_blank');
            setTimeout(() => this.$emit('update:refresh', this.refresh+1), 1000);
        },
        printRecuPDF(){
            var url = '/ventas/print/'+this.compra.id;
            window.open(url, '_blank');
        },
        openDialog (){
            this.dialog = true;
        },
        scanDocu(){
            this.$router.push({ name: 'clidoc.create', params: { cliente_id: this.compra.cliente_id, compra_id: this.compra.id } })
        },
        destroyReg () {
            this.dialog = false;

            axios.post(this.url+'/'+this.compra.id,{_method: 'delete'})
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
        openDialogReaCli(){
            this.dialog_reacli = true;
        },
        goReaCli(){
            this.show_loading = true;
            axios.put('/utilidades/reacli',
                            {   id: this.compra.id,
                                cliente_id: this.cliente_id_nuevo,
                                tipo_op: "C"
                            })
                .then(res => {
                    this.$emit('update:compra', res.data.compra);
                })
                .catch(err =>{
                    this.$toast.error(err.response.data.message);
                })
                .finally(()=> {
                    this.show_loading = false;
                });

        },

    }
}
</script>
