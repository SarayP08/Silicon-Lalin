<script setup>
import '../assets/css/header.css'
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth.js'

const auth = useAuthStore()
const rutaHeader = computed(() => {
  return auth.logueado ? '/HomeUsuario' : '/Registro'
})

const textoHeader = computed(() => {
  return auth.logueado ? 'Inicio' : 'Registrarse'
})
</script>
<template>
  <div class="container">
    <header class="app-header d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
      <a
        href="/"
        class="brand d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none"
      >
        <img src="../assets/img/icono/icono.png" alt="logo" class="brand-logo" />
        <span class="brand-name">Materiales Deza</span>
      </a>

      <ul class="nav nav-pills">
        <li class="nav-item">
          <RouterLink :to="rutaHeader" class="nav-link">
            {{ textoHeader }}
          </RouterLink>
        </li>
      </ul>
    </header>
  </div>
</template>