<template>
    <div>
        <loading :show_loading="show_loading"></loading>
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
import Loading from '@/components/shared/Loading'
import {mapGetters} from 'vuex';
export default {
    props:{
        albaran: Object,
    },
    components: {
        'loading': Loading,
    },
    data () {
      return {
          show_loading: false,
      }
    },
    computed: {
        ...mapGetters([
        ]),
    },
    methods:{
        goBack(){
            this.$router.go(-1);
        },
        goCliente(){
                this.$router.push({ name: 'cliente.edit', params: { id: this.albaran.cliente_id } })
        },
        goFind(){
            this.$router.push({ name: 'find.albaran' })
        },

    }
}
</script>
