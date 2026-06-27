<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { API_URL } from '../../config/api.js'
import '../../assets/css/FormularioMaterial.css'
import { useAuthStore } from '../../stores/auth.js'

const router = useRouter()
const auth = useAuthStore()

const formulario = ref({
  codigo: '',
  nombre: '',
  descripcion: '',
  categoria: '',
  asignar: '',
  id_persona: '',
  id_ubicacion: '',

  persona: {
    nif: '',
    nombre_persona: '',
    apellidos: '',
    correo: '',
    telefono: '',
  },

  ubicacion: {
    tipo: '',
    CP: '',
    provincia: '',
    direccion: '',
  },
})

const cargando = ref(false)
const mensaje = ref('')
const error = ref('')

const recuperarPersona = async () => {
  if (!formulario.value.persona.nif) return
  try {
    const respuesta = await fetch(
      `${API_URL}/api/materiales/buscarPersona.php?nif=${formulario.value.persona.nif}`,
      { credentials: 'include' },
    )
    const resultado = await respuesta.json()
    if (resultado.existe) {
      formulario.value.id_persona = resultado.id_persona
      Object.assign(formulario.value.persona, resultado.persona)
    }
  } catch (err) {
    error.value = 'Error recuperando a la persona'
  }
}

const añadirMaterial = async () => {
  mensaje.value = ''
  error.value = ''

  if (
    !formulario.value.codigo ||
    !formulario.value.nombre ||
    !formulario.value.descripcion ||
    !formulario.value.categoria ||
    !formulario.value.asignar
  ) {
    error.value = 'Por favor, rellena todos los campos obligatorios.'
    return
  }

  if (formulario.value.descripcion.length > 500) {
    error.value = 'La descripción no puede superar los 500 caracteres.'
    return
  }

  try {
    cargando.value = true
    if (
      formulario.value.asignar === 'persona' &&
      !formulario.value.id_persona &&
      formulario.value.persona.nif
    ) {
      await recuperarPersona()
    }

    const datos = new FormData()
    datos.append('codigo', formulario.value.codigo)
    datos.append('nombre', formulario.value.nombre)
    datos.append('descripcion', formulario.value.descripcion)
    datos.append('categoria', formulario.value.categoria)
    datos.append('asignado', formulario.value.asignar)
    datos.append('id_persona', formulario.value.id_persona)
    datos.append('id_ubicacion', formulario.value.id_ubicacion)

    const idUsuario = auth.usuario.id;
    datos.append('id_usuario', idUsuario)

    if (formulario.value.asignar === 'persona') {
      datos.append('nif', formulario.value.persona.nif)
      datos.append('nombre_persona', formulario.value.persona.nombre_persona)
      datos.append('apellidos', formulario.value.persona.apellidos)
      datos.append('correo', formulario.value.persona.correo)
      datos.append('telefono', formulario.value.persona.telefono)
    }

    if (formulario.value.asignar === 'ubicacion') {
      datos.append('tipo', formulario.value.ubicacion.tipo)
      datos.append('CP', formulario.value.ubicacion.CP)
      datos.append('provincia', formulario.value.ubicacion.provincia)
      datos.append('direccion', formulario.value.ubicacion.direccion)
    }

    console.log('DATOS A ENVIAR: ', formulario.value)
    const respuesta = await fetch(`${API_URL}/api/materiales/anhadirMaterial.php`, {
      method: 'POST',
      body: datos,
      credentials: 'include',
    })

    const texto = await respuesta.text()
    console.log('RESPUESTA PHP: ', texto)
    const resultado = JSON.parse(texto)

    if (!respuesta.ok || resultado.error) {
      throw new Error(resultado.message || 'No se pudo añadir el material')
    }

    mensaje.value = 'Material añadido correctamente '

    setTimeout(() => {
      router.push('/Mercancias')
    }, 1200)
  } catch (err) {
    error.value = err.message || 'Error al conectar con el servidor.'
  } finally {
    cargando.value = false
  }
}
</script>

<template>
  <main class="container py-4">
    <section class="card shadow-sm">
      <div class="card-body">
        <div class="mb-4">
          <p class="text-muted mb-1">Panel de administración</p>
          <h1 class="h3 mb-2">Añadir material</h1>
          <p class="mb-0">Rellena los campos obligatorios</p>
        </div>

        <form @submit.prevent="añadirMaterial">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="codigo" class="form-label">Codigo</label>
              <input
                id="codigo"
                v-model="formulario.codigo"
                type="text"
                class="form-control"
                placeholder="Ej: ECC9827"
              />
            </div>

            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input
                id="nombre"
                v-model="formulario.nombre"
                type="text"
                class="form-control"
                placeholder="Paquete de pinturas"
              />
            </div>

            <div class="col-12">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea
                id="descripcion"
                v-model="formulario.descripcion"
                class="form-control"
                rows="4"
                placeholder="Describe máx. 500 caracteres)"
              ></textarea>
            </div>

            <div class="col-md-6">
              <label for="categoria" class="form-label">Categoría</label>
              <select id="categoria" v-model="formulario.categoria" class="form-select">
                <option value="" disabled>Selecciona una opción</option>
                <option value="informatica">Informática</option>
                <option value="ofimatica">Ofimática</option>
                <option value="mobiliario">Mobiliario</option>
                <option value="otros">Otros</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="ubicacion" class="form-label">Asignar a</label>
              <select id="ubicacion" v-model="formulario.asignar" class="form-select">
                <option value="" disabled>Selecciona una opción</option>
                <option value="persona">Persona</option>
                <option value="ubicacion">Ubicación</option>
              </select>
            </div>
          </div>

          <div v-if="formulario.asignar === 'persona'" class="mt-4">
            <h2 class="h5 mb-3">Datos de la persona</h2>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">NIF</label>
                <input
                  v-model="formulario.persona.nif"
                  type="text"
                  class="form-control"
                  placeholder="Ej: 12345678A"
                  @blur="recuperarPersona"
                />
              </div>

              <div class="col-md-6">
                <label class="form-label">Nombre persona</label>
                <input
                  v-model="formulario.persona.nombre_persona"
                  type="text"
                  class="form-control"
                />
              </div>

              <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input v-model="formulario.persona.apellidos" type="text" class="form-control" />
              </div>

              <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input v-model="formulario.persona.correo" type="email" class="form-control" />
              </div>

              <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input v-model="formulario.persona.telefono" type="text" class="form-control" />
              </div>
            </div>
          </div>

          <div v-if="formulario.asignar === 'ubicacion'" class="mt-4">
            <h2 class="h5 mb-3">Datos de la ubicación</h2>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Tipo</label>
                <input v-model="formulario.ubicacion.tipo" type="text" class="form-control" />
              </div>

              <div class="col-md-6">
                <label class="form-label">CP</label>
                <input v-model="formulario.ubicacion.CP" type="text" class="form-control" />
              </div>

              <div class="col-md-6">
                <label class="form-label">Provincia</label>
                <input v-model="formulario.ubicacion.provincia" type="text" class="form-control" />
              </div>

              <div class="col-md-6">
                <label class="form-label">Dirección</label>
                <input v-model="formulario.ubicacion.direccion" type="text" class="form-control" />
              </div>
            </div>
          </div>

          <div v-if="error" class="alert alert-danger mt-4 mb-0">
            {{ error }}
          </div>

          <div v-if="mensaje" class="alert alert-success mt-4 mb-0">
            {{ mensaje }}
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <RouterLink to="/HomeUsuario" class="btn btn-secondary"> Volver </RouterLink>

            <button class="btn btn-primary" type="submit" :disabled="cargando">
              {{ cargando ? 'Añadiendo...' : 'Añadir material' }}
            </button>
          </div>
        </form>
      </div>
    </section>
  </main>
</template>
