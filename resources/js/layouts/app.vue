<template>
  <div id="app-container" :class="getMenuType">
    <topnav />
    <sidebar />
    <main style="margin-top: 128px">
      <div class="container-fluid main-part">
        <child />
      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '../containers/navs/Sidebar'
import Topnav from '../containers/navs/Topnav'
import { mapGetters } from 'vuex'

export default {
  components: {
    topnav: Topnav,
    sidebar: Sidebar
  },
  data () {
    return {
      show: false
    }
  },
  computed: {
    ...mapGetters({
      getMenuType: 'menu/getMenuType'
    })
  },
  mounted () {
    setTimeout(() => {
      document.body.classList.add('default-transition')
    }, 100)
  }
}
</script>
