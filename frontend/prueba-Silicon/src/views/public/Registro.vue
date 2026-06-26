<script setup>
import { ref } from "vue"; 
import { useRouter } from "vue-router"; 
import { API_URL } from "../../config/api.js"; 
import { useAuthStore } from "../../stores/auth.js"; 
const router = useRouter();
const auth = useAuthStore();
const nombre = ref(""); 
const apellidos = ref(""); 
const email = ref(""); 
const password = ref(""); 
const confirmPassword = ref(""); 
const mensaje = ref(""); 
const cargando = ref(false); 
const error = ref("");

const passwordValida = (valor) =>
  valor.length >= 8 &&
  /[a-z]/.test(valor) &&
  /[A-Z]/.test(valor) &&
  /\d/.test(valor) &&
  /[^A-Za-z0-9\s]/.test(valor);

const handleSubmit = async () => {
  error.value = "";
  mensaje.value = "";

  if (!passwordValida(password.value)) {
    error.value =
      "La contraseña debe tener al menos 8 caracteres, una minúscula, una mayúscula, un número y un carácter especial";
    return;
  }

  if (password.value !== confirmPassword.value) {
    error.value = "Las contraseñas no coinciden";

    return;
  }

  try {
    cargando.value = true;
    const datos = new FormData();
    datos.append("nombre", nombre.value);
    datos.append("apellidos", apellidos.value);
    datos.append("email", email.value);
    datos.append("password", password.value);

    console.log(API_URL + '/api/auth/crearUsuario.php');
    const res = await fetch(`${API_URL}/api/auth/crearUsuario.php`, {
      method: "POST",
      body: datos,
    });

    const data = await res.json();

    if (!data.success) {
      error.value = data.error || "No se pudo registrar";
      return;
    }

    const resultadoLogin = await auth.login(email.value, password.value);

    if (!resultadoLogin.ok) {
         error.value =
        resultadoLogin.message ||
        "La cuenta se creó, pero no se pudo iniciar sesión automáticamente";
      return;
    }

    mensaje.value = "Cuenta creada correctamente";

    setTimeout(() => {
      router.push("/HomeUsuario");
    }, 1200);
  } catch (err) {
    console.error("ERROR REAL: ", err);
    error.value = "Error al conectar con el servidor";

  } finally {
    cargando.value = false;
  }
};
</script>

<template>
    
  <div class="contenedor">
    <main class="form-signin w-100 m-auto">
      <form @submit.prevent="handleSubmit">
        <div class="text-center">
          <i class="mb-4 bi bi-person-plus" style="font-size: 4.5rem"></i>
        </div>

        <h1 class="h3 mb-3 fw-normal text-center">Crear Cuenta</h1>
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <div v-if="mensaje" class="alert alert-success">
          {{ mensaje }}
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="nombre" type="text" class="form-control" placeholder="Nombre" required />
              <label>Nombre</label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="apellidos" type="text" class="form-control" placeholder="Apellidos" required />
              <label>Apellidos</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-floating">
              <input v-model="email" type="email" class="form-control" placeholder="Email" required />
              <label>Correo electrónico</label>
            </div>
          </div>
        </div>

        <div class="form-floating">
          <input v-model="password" type="password" class="form-control" placeholder="Password" minlength="8" autocomplete="new-password" required />
          <label>Contraseña</label>
        </div>

        <p class="password-ayuda">
          Mínimo 8 caracteres, con una minúscula, una mayúscula, un número y un carácter especial.
        </p>

        <div class="form-floating">
          <input v-model="confirmPassword" type="password" class="form-control" placeholder="Confirm Password" minlength="8" autocomplete="new-password" required />
          <label>Confirmar contraseña</label>
        </div>

        <div class="form-check text-start my-3">
          <input class="form-check-input" type="checkbox" required />
          <label class="form-check-label">
          <a href="/terminosCondiciones" target="_blank">Acepto los términos y condiciones</a>
          </label>
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit" :disabled="cargando">
          {{ cargando ? "Registrando..." : "Registrarse" }}
        </button>
        <br /><br />

        <p class="text-body-secondary text-center">¿Ya tienes cuenta? <a href="/iniciarSesion">Inicia sesión</a></p>

      </form>
    </main>
</div>
</template>