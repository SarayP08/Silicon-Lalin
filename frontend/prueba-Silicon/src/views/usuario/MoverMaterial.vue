<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { API_URL } from '../../config/api.js'
import { useAuthStore } from '../../stores/auth.js'
import '../../assets/css/mover.css'
import informaticaImg from '../../assets/img/informatica.jpg'
import ofimaticaImg from '../../assets/img/ofimatica.avif'
import mobiliarioImg from '../../assets/img/mobiliario.jpg'
import otrosImg from '../../assets/img/otros.jpg'

const router = useRouter()
const auth = useAuthStore()
const route = useRoute()
const material = ref('')

const imagenesCategoria = {
  informatica: informaticaImg,
  ofimatica: ofimaticaImg,
  mobiliario: mobiliarioImg,
  otros: otrosImg,
}

const imagenMaterial = (categoria) => {
  return imagenesCategoria[categoria] || otrosImg
}

const error = ref('')
const cargando = ref(true)
const id = route.params.id

const movimientos = ref([])

const movimientoActual = computed(() => movimientos.value[0] || null)

const nuevoDestino = ref({
  persona: {
    id_persona: '',
    nombre: '',
    apellidos: '',
    nif: '',
    correo: '',
    telefono: '',
  },
  ubicacion: {
    id_ubicacion: '',
    tipo: '',
    CP: '',
    provincia: '',
    direccion: '',
  },
})

const cargarMovimientos = async () => {
  const id = route.params.id

  try {
    const res = await fetch(`${API_URL}/api/materiales/historialMateriales.php?id_material=${id}`, {
      credentials: 'include',
      cache: 'no-store',
    })

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data?.error || 'No se pudo cargar los movimientos')
    }

    movimientos.value = Array.isArray(data) ? data : []
  } catch (e) {
    console.error(e)
    error.value = e?.message || 'No se pudo cargar los movimientos'
    movimientos.value = []
  }
}

onMounted(async () => {
  try {
    const id = route.params.id
    const res = await fetch(`${API_URL}/api/materiales/detalleMercancia.php?id_material=${id}`, {
      credentials: 'include',
      cache: 'no-store',
    })
    const data = await res.json()
    if (data.error) {
      error.value = data.message || 'No se pudo cargar el material'
      material.value = null
    } else {
      material.value = data
    }
  } catch (err) {
    error.value = 'Error al conectar con el servidor'
    console.error(err)
  } finally {
    cargando.value = false
  }
  await cargarMovimientos()
})

const pintarPersona = (movimiento) => {
  if (!movimiento) return null

  const tienePersona =
    movimiento.id_persona != null ||
    Boolean(movimiento.nif) ||
    Boolean(movimiento.correo) ||
    Boolean(movimiento.telefono) ||
    Boolean(movimiento.nombre) ||
    Boolean(movimiento.apellidos)

  if (!tienePersona) return null

  return {
    tipo: 'persona',
    nombre: `${movimiento.nombre || ''} ${movimiento.apellidos || ''}`.trim() || 'Sin nombre',
    nif: movimiento.nif || '',
    correo: movimiento.correo || '',
    telefono: movimiento.telefono || '',
  }
}

const pintarUbiOrigen = (movimiento) => {
  if (!movimiento) return null

  if (movimiento.tipo_destino) {
    return {
      tipo: 'ubicacion',
      ubicacion: movimiento.tipo_destino,
      direccion: movimiento.direccion_destino,
      cp: movimiento.cp_destino,
      provincia: movimiento.provincia_destino,
    }
  }

  const persona = pintarPersona(movimiento)
  if (persona) return persona

  if (movimiento.tipo_origen) {
    return {
      tipo: 'ubicacion',
      ubicacion: movimiento.tipo_origen,
      direccion: movimiento.direccion_origen,
      cp: movimiento.cp_origen,
      provincia: movimiento.provincia_origen,
    }
  }

  return null
}

const tipoElegido = ref('')
const tipoMovimiento = ref('')

const actualizarMovimiento = async () => {
  error.value = ''
  try {
    if (
      tipoElegido.value === 'persona' &&
      !nuevoDestino.value.persona.id_persona &&
      nuevoDestino.value.persona.nif
    ) {
      await recuperarPersona()
    }

    const datosActualizados = new FormData()

    datosActualizados.append('id_material', route.params.id)
    datosActualizados.append('tipoDestino', tipoElegido.value)
    datosActualizados.append('tipoMovimiento', tipoMovimiento.value)

    if (tipoElegido.value === 'persona') {
      datosActualizados.append('id_persona', nuevoDestino.value.persona.id_persona ?? '')
      datosActualizados.append('nombre', nuevoDestino.value.persona.nombre)
      datosActualizados.append('apellidos', nuevoDestino.value.persona.apellidos)
      datosActualizados.append('nif', nuevoDestino.value.persona.nif)
      datosActualizados.append('correo', nuevoDestino.value.persona.correo)
      datosActualizados.append('telefono', nuevoDestino.value.persona.telefono)
    }

    if (tipoElegido.value === 'ubicacion') {
      datosActualizados.append('id_ubicacion', nuevoDestino.value.ubicacion.id_ubicacion ?? '')
      datosActualizados.append('tipo', nuevoDestino.value.ubicacion.tipo)
      datosActualizados.append('CP', nuevoDestino.value.ubicacion.CP)
      datosActualizados.append('provincia', nuevoDestino.value.ubicacion.provincia)
      datosActualizados.append('direccion', nuevoDestino.value.ubicacion.direccion)
    }
    const respuesta = await fetch(`${API_URL}/api/materiales/nuevoMovimiento.php`, {
      method: 'POST',
      body: datosActualizados,
      credentials: 'include',
    })

    const texto = await respuesta.text()

    const resultado = JSON.parse(texto)
    if (!resultado.success) {
      throw new Error(resultado.error)
    }
    router.push(`/Detalle/${route.params.id}`)
  } catch (err) {
    console.error(err)
    error.value = err.message
  }
}
const recuperarPersona = async () => {
  if (!nuevoDestino.value.persona.nif) return
  try {
    const respuesta = await fetch(
      `${API_URL}/api/materiales/buscarPersona.php?nif=${nuevoDestino.value.persona.nif}`,
      { credentials: 'include' },
    )
    const resultado = await respuesta.json()
    if (resultado.existe) {
      nuevoDestino.value.persona.id_persona = resultado.id_persona
      Object.assign(nuevoDestino.value.persona, resultado.persona)
      if (resultado.persona?.nombre_persona != null) {
        nuevoDestino.value.persona.nombre = resultado.persona.nombre_persona
      }
    } else {
      nuevoDestino.value.persona.id_persona = ''
    }
  } catch (err) {
    error.value = 'Error recuperando a la persona'
  }
}
</script>

<template>
  <main class="container py-4">
    <h1 class="text-center mb-4">Nuevo movimiento</h1>

    <section v-if="material" class="row justify-content-center mb-4">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <img
            :src="imagenMaterial(material.categoria)"
            :alt="material.categoria"
            class="card-img-top material-img"/>

          <div class="card-body">
            <h5 class="card-title">{{ material.nombre }}</h5>
            <p class="mb-1"><strong>Código:</strong> {{ material.codigo }}</p>
            <p class="mb-1"><strong>Categoría:</strong> {{ material.categoria }}</p>
            <p class="mb-0"><strong>Estado:</strong> {{ material.estado }}</p>
          </div>
        </div>
      </div>
    </section>

    <section v-if="movimientoActual" class="row g-4">
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <div class="card-header">Origen actual</div>

          <div class="card-body">
            <div v-if="pintarUbiOrigen(movimientoActual)?.tipo === 'persona'">
              <label class="form-label">Nombre</label>
              <input
                class="form-control mb-3"
                :value="pintarUbiOrigen(movimientoActual).nombre"
                disabled/>

              <label class="form-label">NIF</label>
              <input
                class="form-control mb-3"
                :value="pintarUbiOrigen(movimientoActual).nif"
                disabled/>

              <label class="form-label">Correo</label>
              <input
                class="form-control mb-3"
                :value="pintarUbiOrigen(movimientoActual).correo"
                disabled/>

              <label class="form-label">Teléfono</label>
              <input
                class="form-control"
                :value="pintarUbiOrigen(movimientoActual).telefono"
                disabled/>
            </div>

            <div v-else-if="pintarUbiOrigen(movimientoActual)?.tipo === 'ubicacion'">
              <label class="form-label">Ubicación</label>
              <input
                class="form-control mb-3"
                :value="pintarUbiOrigen(movimientoActual).ubicacion"
                disabled/>

              <label class="form-label">Dirección</label>
              <input
                class="form-control mb-3"
                :value="pintarUbiOrigen(movimientoActual).direccion"
                disabled/>

              <label class="form-label">CP</label>
              <input
                class="form-control mb-3"
                :value="pintarUbiOrigen(movimientoActual).cp"
                disabled/>

              <label class="form-label">Provincia</label>
              <input
                class="form-control"
                :value="pintarUbiOrigen(movimientoActual).provincia"
                disabled/>
            </div>

            <p v-else class="text-muted mb-0">Sin origen registrado</p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <div class="card-header">Nuevo destino</div>

          <div class="card-body">
            <label class="form-label">Tipo de destino</label>
            <select class="form-select mb-3" v-model="tipoElegido">
              <option value="">Selecciona una opción</option>
              <option value="persona">Persona</option>
              <option value="ubicacion">Ubicación</option>
            </select>

            <div v-if="tipoElegido === 'ubicacion'">
              <label class="form-label">Tipo</label>
              <input class="form-control mb-3" v-model="nuevoDestino.ubicacion.tipo" />

              <label class="form-label">Provincia</label>
              <input class="form-control mb-3" v-model="nuevoDestino.ubicacion.provincia" />

              <label class="form-label">CP</label>
              <input class="form-control mb-3" v-model="nuevoDestino.ubicacion.CP" />

              <label class="form-label">Dirección</label>
              <input class="form-control mb-3" v-model="nuevoDestino.ubicacion.direccion" />

              <label class="form-label">Estado</label>
              <select class="form-select mb-3" v-model="tipoMovimiento">
                <option value="">Selecciona un estado</option>
                <option value="transferido">Transferido</option>
                <option value="asignado">Asignado</option>
                <option value="reparacion">Reparación</option>
                <option value="devolucion">Devolución</option>
              </select>
            </div>

            <div v-if="tipoElegido === 'persona'">
              <label class="form-label">Nombre</label>
              <input class="form-control mb-3" v-model="nuevoDestino.persona.nombre" />

              <label class="form-label">Apellidos</label>
              <input class="form-control mb-3" v-model="nuevoDestino.persona.apellidos" />

              <label class="form-label">NIF</label>
              <input
                class="form-control mb-3"
                v-model="nuevoDestino.persona.nif"
                @blur="recuperarPersona"/>

              <label class="form-label">Correo</label>
              <input class="form-control mb-3" v-model="nuevoDestino.persona.correo" />

              <label class="form-label">Teléfono</label>
              <input class="form-control" v-model="nuevoDestino.persona.telefono" />

              <label class="form-label">Estado</label>
              <select class="form-select mb-3" v-model="tipoMovimiento">
                <option value="">Selecciona un estado</option>
                <option value="alta">Alta</option>
                <option value="devolucion">Devolución</option>
                <option value="reparacion">Reparación</option>
                <option value="asignado">Asignado</option>
                <option value="transferido">Transferido</option>
              </select>
            </div>
          </div>
        </div>
        <button class="btn btn-primary mt-3" @click="actualizarMovimiento">Actualizar</button>
      </div>
    </section>
  </main>
</template>
