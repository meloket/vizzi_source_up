<template>
    <div :style="getStyle()" class="d-flex">
        <v-screen :id="id" :color="color" v-if="isMounted" class="vw-75" />
        <div class="bg-white vw-25">
            <v-chat :id="id" />
            <hr style="margin-top: 0px; margin-bottom: -2px"/>
            <!-- <v-note /> -->
        </div>
    </div>
</template>

<script>
import {
    mapGetters,
    mapActions
} from 'vuex'

import SessionScreen from '../../containers/session/SessionScreen'
import SessionChat from '../../containers/session/SessionChat'
import SessionNote from '../../containers/session/SessionNote'
import Axios from 'axios'

export default {
    components: {
        'v-screen': SessionScreen,
        'v-chat': SessionChat,
        'v-note': SessionNote
    },
    metaInfo () {
        return { title: `Session` }
    },
    data() {
        return {
            id: null,
            height: 64,
            session: null,
            video_sources: [],
            isMounted: false,
            color: '#922c88'
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            currentUser: "auth/user"
        })
    },
    mounted() {
        this.init()
    },
    methods: {
        init() {
            this.id = this.$route.params.id
            this.isMounted = true
            Axios.post('/site/domain', {id: this.siteId}).then(res => {
                if (res.data.style) {
                    this.color = JSON.parse(res.data.style).bg
                    this.height = JSON.parse(res.data.style).height
                }
            })
        },
        getStyle() {
            return {
                'width': '100%'
            }
        }
    }
}
</script>

<style lang="css">
    .h-60 {
        height: 60%;
    }
    .h-40 {
        height: 40%;
    }
    .vw-25 {
        width: 25vw;
    }
    .vw-75 {
        width: 75vw;
    }
</style>