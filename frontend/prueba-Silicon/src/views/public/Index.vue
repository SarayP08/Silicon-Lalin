<script setup>
import { ref } from 'vue'
import { useAuthStore } from "../../stores/auth.js";
import { useRouter, useRoute } from "vue-router";
import '../../assets/css/login.css';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const email = ref("");
const password = ref("");
const error = ref("");
const cargando = ref(false);

const login = async () => {
  error.value = "";
  cargando.value = true;
    const resultado = await auth.login(email.value, password.value);

  if (resultado.ok) {
    const redirect = route.query.redirect;

    if (redirect) {
      router.push(redirect);
      } else {
        router.push("/HomeUsuario");
      }
  } else {
    error.value = resultado.message;
  }
  cargando.value = false;
};
</script>

<template>
     <div class="contenedor">
    <main class="form-signin w-100 m-auto">
      <form @submit.prevent="login">
        <div class="text-center">
          <i class="mb-4 bi bi-person" style="font-size: 4.5rem; color: #278EF5"></i>
        </div>

        <h1 class="h3 mb-3 fw-normal text-center">Iniciar Sesión</h1>
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <div class="form-floating">
          <input v-model="email" type="email" class="form-control" id="floatingInput" placeholder="admin@example.com" required />
          <label for="floatingInput">Dirección de correo electrónico</label>
        </div>

        <div class="form-floating mt-2">
          <input v-model="password" type="password" class="form-control" id="floatingPassword" placeholder="Contraseña" required />
          <label for="floatingPassword">Contraseña</label>
        </div>

        <div class="form-check text-start my-3">
          <input class="form-check-input" type="checkbox" value="remember-me" id="checkDefault" />
          <label class="form-check-label" for="checkDefault">Recordarme</label>
        </div>

        <p class="text-body-secondary text-center">
          <a href="/contra-olvidada">¿Olvidaste tu contraseña?</a>
        </p>

        <button class="btn btn-primary w-100 py-2" type="submit" :disabled="cargando">
          {{ cargando ? "Entrando..." : "Iniciar Sesión" }}
        </button>

        <p class="text-body-secondary text-center">¿No tienes una cuenta? <a href="/Registro">Regístrate aquí</a></p>
      </form>
    </main>
  </div>
</template>
