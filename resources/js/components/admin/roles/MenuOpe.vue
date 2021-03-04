<template>
    <div>
        <v-tooltip bottom v-if="isRoot">
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
    </div>
</template>
<script>
import {mapGetters} from 'vuex';
export default {
    props:{
        id: Number
    },
    data () {
      return {
          dialog: false,
      }
    },
    computed: {
        ...mapGetters([
            'isRoot',
        ]),
    },
    methods:{
        goIndex(){
            this.$router.push({ name: 'roles.index' })
        },
        goCreate(){
            this.$router.push({ name: 'roles.create' })
        },
    }
}
</script>
