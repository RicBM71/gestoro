<template>
    <div>
        <loading :show_loading="show_loading"></loading>
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
    components: {
        'loading': Loading,
    },
    data () {
      return {
          ruta: "albaran",
          url: "/ventas/albaranes",
          show_loading: false,
      }
    },
    computed: {
        ...mapGetters([
            'isRoot',
        ]),
    },
    methods:{
        goBack(){
            this.$router.go(-1);
        },
    }
}
</script>
