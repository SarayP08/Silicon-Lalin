<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import { useAuthStore } from './stores/auth.js'

const auth = useAuthStore()

onMounted(() => {
  auth.comprobarSesion()
})
</script>
<template>
  <Header />
  <main class="app-main">
    <RouterView />
  </main>
  <Footer />
</template>

<style>
html,
body,
#app {
  min-height: 100vh;
  margin: 0;
}

#app {
  display: flex;
  flex-direction: column;
}

.app-main {
  flex: 1;
}
</style>
