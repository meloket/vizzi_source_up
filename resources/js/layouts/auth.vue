<template>
  <div id="root">
    <div class="fixed-background" />
    <main>
      <div class="container text-center">
        <child />
      </div>
    </main>
  </div>
</template>
<script>
export default {
  name: 'AuthLayout',
  mounted () {
    document.body.classList.add('background')
  },
  beforeDestroy () {
    document.body.classList.remove('background')
  }
}
</script>
