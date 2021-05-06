<template>
    <v-app>
          <loading :show_loading="show_loading"></loading>
          <div class="text-xs-center">
            <v-dialog
            v-model="myEmpresa"
            width="500"
            >
            <v-card>
                <v-card-title
                class="headline grey lighten-2"
                primary-title
                >
                Seleccionar empresa
                </v-card-title>

                <v-card-text>
                    <v-flex sm2 d-flex></v-flex>
                    <v-flex sm8 d-flex>
                        <v-select
                            v-on:change="setEmpresa"
                            v-model="empresa_id"
                            :items="empresas"
                            label="Empresa"
                        ></v-select>
                    </v-flex>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    round
                    flat
                    @click="myEmpresa=false"
                >
                    Cerrar
                </v-btn>
                </v-card-actions>
            </v-card>
            </v-dialog>
        </div>
        <div v-if="isLoggedIn">
            <v-navigation-drawer
                v-if="isLoggedIn"
                v-model="drawer"
                :clipped="$vuetify.breakpoint.lgAndUp"
                fixed
                app
                >
                <v-list dense>
                    <template v-for="item in mn_items">
                    <v-layout
                        v-if="item.heading"
                        :key="item.heading"
                        row
                        align-center
                    >
                        <v-flex xs6>
                        <v-subheader v-if="item.heading">
                            {{ item.heading }}head
                        </v-subheader>
                        </v-flex>
                        <v-flex xs6 class="text-xs-center">
                        <a href="#!" class="body-2 black--text">EDIT</a>
                        </v-flex>
                    </v-layout>
                    <v-list-group
                        v-else-if="item.children"
                        :key="item.text"
                        v-model="item.model"
                        :prepend-icon="item.model ? item.icon : item['icon-alt']"
                        append-icon=""
                    >
                        <v-list-tile slot="activator">
                        <v-list-tile-content>
                            <v-list-tile-title>
                            {{ item.text }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile
                        v-for="child in item.children"
                        :key="child.name"
                        :to="{ name: child.name}"
                        >
                        <v-list-tile-action v-if="child.icon">
                            <v-icon>{{ child.icon }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                            {{ child.text }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                        </v-list-tile>
                    </v-list-group>
                    <v-list-tile v-else :key="item.text" @click="abrir(item.name)">
                        <v-list-tile-action>
                        <v-icon>{{ item.icon }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                        <v-list-tile-title>
                            {{ item.text }}
                        </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    </template>
                </v-list>
            </v-navigation-drawer>
            <v-toolbar
                v-if="menu"
                :clipped-left="$vuetify.breakpoint.lgAndUp"
                color="blue darken-3"
                dark
                app
                fixed
                >
                <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
                    <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
                    <span class="hidden-sm-and-down">{{ this.user.empresa_nombre }}</span>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn v-on="on" icon v-show="jobs > 0">
                            <v-icon color="red darken-4">notification_important</v-icon>
                        </v-btn>
                    </template>
                    <span>({{jobs}}) Procesos pendientes.</span>
                </v-tooltip>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn v-on="on" @click="goCompras()" icon v-show="computedLotes">
                            <v-icon color="warning">touch_app</v-icon>
                        </v-btn>
                    </template>
                    <span>Tienes lotes ({{lotes}}) pendientes de cierre.</span>
                </v-tooltip>
                <v-btn v-if="traspasos>0" icon v-on:click="goTraspasos()">
                    <v-icon color="warning">notifications</v-icon>
                </v-btn>
                <v-btn icon v-on:click="empresa">
                    <v-icon>work_outline</v-icon>
                </v-btn>
                <v-btn icon v-on:click="home">
                    <v-icon>home</v-icon>
                </v-btn>
                <v-btn icon v-on:click="passwd">
                    <v-avatar v-if="user.avatar !='#'" size="32px">
                        <img class="img-fluid" :src="user.avatar">
                    </v-avatar>
                    <v-icon v-else>settings</v-icon>
                </v-btn>
                <strong v-html="user.name"></strong>
                <v-btn icon large  v-on:click="Logout">
                    <v-avatar size="32px" tile>
                        <v-icon>exit_to_app</v-icon>
                    </v-avatar>
                </v-btn>
                </v-toolbar>
            <v-content>


                    <router-view :key="$route.fullPath"></router-view>


            </v-content>
        </div>
</v-app>
</template>
<script>
import {mapActions} from "vuex";
import {mapState} from 'vuex'
import {mapGetters} from 'vuex';
import Loading from '@/components/shared/Loading'
export default {
    components: {
        'loading': Loading,
    },
    computed:{
        ...mapState({
            user: state => state.auth
        }),
        ...mapGetters([
            'isLoggedIn',
            'isRoot',
            'isAdmin',
            'hasFactura',
            'hasUsers',
            'empresaActiva',
            'hasLiquidar',
            'lotes',
            'parametros',
            'hasConsultas',
            'hasProcesos',
            'hasEcommerce'

        ]),
        computedLotes(){

            if (this.$route.name == "dash"){
                return this.lotes > 0;
            }
            else
                return 0;
        }
    },
    data: () => ({
        menu: true,
        dialog: false,
        drawer: false,
        show: true,

        empresaTxt:"...",
        empresas:[],
        myEmpresa:false,
        empresa_id:0,
        traspasos: 0,
        jobs: 0,
        show_loading:  false,
        //productos_online: 0,

        // user: {
        //     name : ""
        // },

        mn_root: {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Root',
            model: false,
            children: [
                // { text: 'Ampliación COVID', name: 'tools.ampliar' },
                // { text: 'Cambio Masivo Interés', name: 'tools.interes' },
                {text: 'Find Producto', name: 'tools.find.producto' },
                // { text: 'Importar Producto', name: 'tools.importar.producto' },
                { text: 'Histórico compras', name: 'hcompras.index' },
                { text: 'Usuarios', name: 'users.index' },
                { text: 'Roles', name: 'roles.index' },
                { text: 'Permisos', name: 'permisos.index' },
                { text: 'Grupos', name: 'grupo.index' },
                { text: 'Clases', name: 'clase.index' },
                { text: 'Tipos de Iva', name: 'iva.index' },
                { text: 'Cruces Caja', name: 'cruce.index' },
                { text: 'Formas de pago', name: 'fpago.index' },
                { text: 'RRSS', name: 'social.index' },
                { text: 'Plantilla Whatsapps', name: 'whatsapp.index' },
                { text: 'Cierre', name: 'tools.cierre' },
                { text: 'Parámetros', name: 'parametro.edit' },
            ]
        },

        mn_config: {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'settings',
            text: 'Configuración',
            model: false,
            children: [
                { icon: 'mdi-arrow-horizontal-lock', text: 'Roles', name: 'roles.index' },
                { icon: 'mdi-whatsapp', text: 'Plantilla Whatsapps', name: 'whatsapp.index' },
                { icon: 'tour', text: 'Categorías', name: 'categoria.index' },
                { icon: 'storage', text: 'Marcas', name: 'marca.index' },
                { icon: 'mdi-map-marker-radius-outline', text: 'Distintivos', name: 'tag.index' },
                { icon: 'mdi-emoticon-sad-outline', text: 'Motivos Devolución', name: 'motivo.index' },
            ]
        },


        mn_admin: {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Administración',
            model: false,
            children: [
                { icon: 'mdi-account-group', text: 'Usuarios', name: 'users.index' },
                { icon: 'outlined_flag', text: 'Apuntes', name: 'apunte.index' },
                { icon: 'language', text: 'Empresas', name: 'empresa.index' },
                { icon: 'event_seat', text: 'Ubicaciones', name: 'almacen.index' },
                { icon: 'book', text: 'Libros', name: 'libro.index' },
                { icon: 'alarm', text: 'Contadores', name: 'contador.index' },
                { icon: 'account_balance', text: 'Cuentas IBAN', name: 'cuenta.index' },
                { icon: 'sentiment_satisfied_alt', text: 'Garantías', name: 'garantia.index' },
                { icon: 'build', text: 'Talleres', name: 'taller.index' },
                { icon: 'tour', text: 'Existencias', name: 'existencia.index' },
            ]
        },

        mn_procesos:{
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Procesos',
            model: false,
            children: [
                { icon: 'lock', text: 'Facturar Recuperaciones', name:'facturacion.compras' },
                { icon: 'lock', text: 'Facturar Albaranes', name:'facturacion.albaranes' },
                { text: 'Reubicar Albaranes', name: 'reubicar', icon: 'shuffle'},
                { icon: 'fireplace', text: 'Liquidar Lotes', name:'liquidar.index' },
                { icon: 'compare_arrows', text: 'Intercambio de Operaciones', name:'intercambio' },
                { icon: 'check_circle_outline', text: 'Check Contadores', name:'contador.check' },
                { icon: 'auto_delete', text: 'Borrado Masivo ', name:'tools.delete' },
                { icon: 'shopping_bag', text: 'Asignar Categorías', name:'categoria.assign' },
            ]
        },

         mn_procesos_simple:{
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Procesos',
            model: false,
            children: [
                { icon: 'fireplace', text: 'Liquidar Lotes', name:'liquidar.index' },
                { icon: 'forward', text: 'Inventario', name:'exportar.inventario' },
                { icon: 'book', text: 'Libros', name: 'libro.index' },
            ]
        },

        mn_items: [
            { icon: 'people', text: 'Clientes', name:'cliente.index' },
            { icon: 'local_offer', text: 'Productos', name:'producto.index' },
        ],

        mn_fixing: {

             icon: 'insights', text: 'Fixing', name:'fixing.index' ,
        },

         mn_consultas:{
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Consultas I',
            model: false,
            children: [
                { icon: 'forward', text: 'Balance por empresa', name:'exportar.balance' },
                { icon: 'forward', text: 'Operaciones por empresa', name:'exportar.operaciones' },
                { icon: 'forward', text: 'Cuadro de Mando', name:'exportar.mando' },
                { icon: 'forward', text: 'Resumen por concepto', name:'exportar.situacion' },
                { icon: 'forward', text: 'Resumen Contable', name:'exportar.resconta' },
                { icon: 'forward', text: 'Detalle de compras', name:'exportar.detacom' },
                { icon: 'forward', text: 'Detalle de ventas', name:'exportar.detaven' },
                { icon: 'forward', text: 'Servicios Taller', name:'exportar.service' },
                { icon: 'forward', text: 'Liquidados', name:'exportar.liquidados' },
                { icon: 'home_work', text: 'Ventas en depósito', name:'exportar.vendepo' },
            ],
        },

         mn_consultas2:{
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Consultas II',
            model: false,
            children: [
                { text: 'Metal en Depósito', name: 'exportar.metdep', icon: 'print'},
                { text: 'Apuntes por banco', name: 'exportar.apuban', icon: 'account_balance'},
                { text: 'Relación facturas recuperacion', name: 'facturacion.lisfacom', icon: 'print'},
                { text: 'Relación facturas de venta', name: 'facturacion.lisfaven', icon: 'print'},
                { icon: 'forward', text: 'Inventario', name:'exportar.inventario' },
                { icon: 'forward', text: 'Valor Existencias', name:'exportar.valorex' },
                { icon: 'archive', text: 'Mod. 347', name:'exportar.mod347' },
                { icon: 'menu_book', text: 'Imprimir Libro', name:'exportar.libro' },
                { icon: 'check_circle_outline', text: 'Check Contadores', name:'contador.check' },
            ],
        },

        // mn_users: {
        //     icon: 'keyboard_arrow_up',
        //     'icon-alt': 'keyboard_arrow_down',
        //     text: 'Administración',
        //     model: false,
        //     children: [
        //         { icon: 'supervised_user_circle', text: 'Usuarios', name: 'users.index' },
        //     ]
        // },

        mn_etiquetas:{
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Recuentos/Etiquetas',
            model: false,
            children: [
                { icon: 'forward', text: 'Recuentos', name:'recuento.index' },
                { icon: 'forward', text: 'Importar Recuento', name:'rfid.import' },
                { icon: 'forward', text: 'Exportar Etiquetas', name:'rfid.export' },
                { icon: 'forward', text: 'Estados RFID', name:'rfid.estados' },
                { icon: 'forward', text: 'Etiquetas Apli 4x22', name:'etiquetas.aplipdf' },
                { icon: 'forward', text: 'Etiquetas Apli 3x7', name:'etiquetas.apli3x7' },
                { icon: 'forward', text: 'Generar Excel Etiquetas', name:'etiquetas.rollo' },
            ],
        },
         mn_ecommerce:{
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'eCommerce',
            model: false,
            children: [
                { icon: 'forward', text: 'Pendientes', name:'ecommerce.index' },
            ],
        },
        expired: false,
    }),

    mounted(){

        axios.get('/dash')
                .then(res => {

                    this.setAuthUser(res.data.user);

                    if (this.parametros.fixing == true)
                        this.mn_items.push(this.mn_fixing);

                    this.mn_items.push(this.mn_etiquetas);

                    if (this.hasConsultas){
                        //this.drawer = true;
                        this.mn_items.push(this.mn_consultas);
                        this.mn_items.push(this.mn_consultas2);
                    }

                    this.empresa_id = this.user.empresa_id;

                    if (this.hasProcesos)
                        this.mn_items.push(this.mn_procesos);

                    if (this.isRoot)
                        this.mn_items.push(this.mn_root);

                    if(this.isAdmin)
                        this.mn_items.push(this.mn_admin);

                    if (this.isRoot || this.isAdmin)
                        this.mn_items.push(this.mn_config);

                    if (this.hasEcommerce)
                        this.mn_items.push(this.mn_ecommerce);

                    this.empresas = res.data.user.empresas;
                    var idx = this.empresas.map(x => x.value).indexOf(this.empresa_id);

                    // this.empresaTxt = this.empresas[idx].text;
                    //this.empresaTxt = this.user.empresa_nombre;

                    this.traspasos = res.data.traspasos;
                    this.jobs = res.data.jobs;

                    //this.productos_online = res.data.productos_online;

                    // res.data.user.empresas.map((e) =>{
                    //     if (e.id == this.empresa_id)
                    //         this.empresaTxt = e.titulo;
                    //     this.empresas.push({id: e.id, name: e.titulo});
                    // })

                    this.empresas.sort(function (a, b) {
                    if (a.text > b.text) {
                        return 1;
                    }
                    if (a.text < b.text) {
                        return -1;
                    }
                    // a must be equal to b
                    return 0;
                    });

                    this.expired = res.data.expired;
                    if (this.expired){
                        this.$toast.error('Password ha Expirado, debe reemplazarla');
                        this.$router.push({name: 'edit.password'});
                    }

                })
                .catch(err => {

                    this.show = false;
                    if (err.request.status == 401){ // fallo de validated.
                        //this.$router.push("/login");
                        window.location = '/login';
                    }
                })

    },

    methods:{
        ...mapActions([
                'setAuthUser',
                'unsetParametros'
			]),
        abrir(name){
            //this.drawer = false;
            this.$router.push({name: name});
        },
        goTraspasos(){
            this.$router.push({name: 'traspaso.index'});
        },
        goCompras(){
            this.$router.push({name: 'compra.index'});
        },
        home(){
            axios.get('/dash')
                .then(res => {

                    this.setAuthUser(res.data.user);

                    this.empresa_id = this.user.empresa_id;
                    this.empresas = res.data.user.empresas;
                    this.empresas.sort(function (a, b) {
                    if (a.text > b.text) {
                        return 1;
                    }
                    if (a.text < b.text) {
                        return -1;
                    }
                    // a must be equal to b
                    return 0;
                    });
                    //this.empresas = res.data.user.empresas.sortBy('text');
                    var idx = this.empresas.map(x => x.value).indexOf(this.empresa_id);

                    this.empresaTxt = this.empresas[idx].text;
                    this.traspasos = res.data.traspasos;
                    this.jobs = res.data.jobs;

                    // res.data.user.empresas.map((e) =>{
                    //     if (e.value == this.empresa_id)
                    //         this.empresaTxt = e.text;
                    //     this.empresas.push({id: e.id, name: e.titulo});
                    // })

                    this.expired = res.data.expired;
                    if (this.expired){
                        this.$toast.error('Password ha Expirado, debe reemplazarla');
                        this.$router.push({name: 'edit.password'});
                    }

                })
                .catch(err => {
                    this.show = false;
                    if (err.request.status == 401){ // fallo de validated.
                        window.location = '/login';
                    }
                })

            this.$router.push({name: 'dash'});
        },
        passwd(){
            this.$router.push({name: 'edit.password'});
        },
        empresa(){
            this.empresa_id = this.empresaActiva;
            this.myEmpresa=true;
        },
        getReloadEmpresa(){
            this.show_loading = true;
            axios.get('/dash')
                .then(res => {

                    this.setAuthUser(res.data.user);
                    var idx = this.empresas.map(x => x.value).indexOf(this.empresa_id);
                    this.empresaTxt = this.empresas[idx].text;
                    if (this.$route.path != '/home')
                        this.$router.push({name: 'dash'});
                })
                .catch(err => {
                    this.$toast.error("Fallo en reload empresa...");
                })
                 .finally(()=> {
                        this.show_loading = false;
                });
        },
        setEmpresa(){

            this.show_loading = true;
            this.empresas.map((e) =>{
                   if (e.id == this.empresa_id)
                        this.empresaTxt = e.name;
                });

            axios({
                    method: 'put',
                    url: '/admin/users/'+this.user.id+'/empresa',
                    data:{ empresa_id: this.empresa_id }
                })
                .then(res => {
                    //this.$toast.success("Cambiando de empresa...");
                   // this.setAuthUser(res.data.user);

                    this.getReloadEmpresa();
                   // this.$router.push({name: 'dash'});
                })
                .catch(err => {
                    this.$toast.error("No se ha podido seleccionar la empresa");
                });

            this.myEmpresa=false;
        },
        Logout() {
            this.$toast.success('Logout, espere...');
            axios.post('/logout').then(res => {

                this.$store.dispatch('unsetAuthUser')
                this.$router.push({name: 'index'});
			})
        },
    }
  }
</script>
