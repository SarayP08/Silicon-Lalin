<script setup>
import { onMounted, ref } from "vue";
import { API_URL } from "../../config/api.js";
import { useAuthStore } from "../../stores/auth.js";
import '../../assets/css/homeAdmin.css';

const auth = useAuthStore();
</script>

<template>
  <main class="admin-page">
    <section class="admin-hero">
      <div>
        <p class="admin-label">Panel de administración</p>
        <h1>
          ¡Bienvenid@!
          {{ auth.usuario?.nombre }}
        </h1>
        <p class="admin-description">Gestiona cada material en tu plataforma de confianza</p>
      </div>
    </section>

    <section class="dashboard-grid">
      <article class="dashboard-card cats-card">
        <div class="card-top">
          <div class="dashboard-icon">
            <i class="bi bi-heart-fill"></i>
          </div>
        </div>

        <h2>Materiales</h2>
        <p>Administra o crea los materiales disponibles.</p>

        <div class="action-list">
          <RouterLink class="action-btn" to="/Mercancias">
            <i class="bi bi-card-list"></i>
            Ver materiales
          </RouterLink>

          <RouterLink class="action-btn" to="/Añadir">
            <i class="bi bi-plus-circle"></i>
            Añadir nuevo material
          </RouterLink>
        </div>
      </article>
    </section>
  </main>
</template>