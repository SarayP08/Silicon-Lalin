<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { API_URL } from '../../config/api.js'
import { useAuthStore } from '../../stores/auth.js'

const router = useRouter()
const auth = useAuthStore()
const route = useRoute()

const material = ref(null)
const cargando = ref(true)
const error = ref('')
const movimientos = ref([])

const validandoPorId = ref({})
const validadoPorId = ref({})
const errorValidacion = ref('')

const esAdmin = computed(() => auth.usuario?.rol === 'admin')

const normalizar = (v) =>
  (v ?? '')
    .toString()
    .trim()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')

const esMovimientoDevolucion = (movimiento) =>
  normalizar(movimiento?.tipo_movimiento) === 'devolucion'

const eliminarMaterial = async () => {
  error.value = ''

  try {
    const id = route.params.id

    const res = await fetch(`${API_URL}/api/materiales/eliminarMercancia.php?id=${id}`, {
      method: 'DELETE',
      credentials: 'include',
    })

    const data = await res.json()

    if (!data.success) {
      throw new Error(data.error || 'No se pudo eliminar el material')
    }

    router.push('/Mercancias')
  } catch (err) {
    console.error(err)
    error.value = err.message || 'No se pudo eliminar el material'
  }
}

onMounted(async () => {
  try {
    const id = route.params.id
    const res = await fetch(`${API_URL}/api/materiales/detalleMercancia.php?id_material=${id}`, {
      credentials: 'include',
    })
    const data = await res.json()
    console.log(' DATA RECIBIDA: ', data)
    if (data.error) {
      error.value = data.message || 'No se pudo cargar el material'
      material.value = null
    } else {
      material.value = data
      console.log(' DATA después: ', material)
    }

    await cargarMovimientos()
  } catch (err) {
    error.value = 'Error al conectar con el servidor'
    console.error(err)
  } finally {
    cargando.value = false
  }
})

const cargarMovimientos = async () => {
  error.value = ''

  try {
    const id = route.params.id
    const respuesta = await fetch(
      `${API_URL}/api/materiales/historialMateriales.php?id_material=${id}`,
      {
        credentials: 'include',
      },
    )

    const resultado = await respuesta.json()

    console.log('HISTORIAL:', resultado)

    if (!respuesta.ok) {
      throw new Error(resultado.error || 'No se pudo cargar los movimientos')
    }

    movimientos.value = Array.isArray(resultado) ? resultado : []
  } catch (err) {
    error.value = err.message || 'No se pudo cargar los movimientos'
    movimientos.value = []
  }
}

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

const pintarUbicacionOrigen = (movimiento) => {
  if (!movimiento || !movimiento.tipo_origen) return null

  return {
    tipo: 'ubicacion',
    ubicacion: movimiento.tipo_origen,
    direccion: movimiento.direccion_origen,
    cp: movimiento.cp_origen,
    provincia: movimiento.provincia_origen,
  }
}

const pintarUbicacionDestino = (movimiento) => {
  if (!movimiento || !movimiento.id_destino) return null

  return {
    tipo: 'ubicacion',
    ubicacion: movimiento.tipo_destino,
    direccion: movimiento.direccion_destino,
    cp: movimiento.cp_destino,
    provincia: movimiento.provincia_destino,
  }
}

const pintarMovimiento = (movimiento) => {
  if (!movimiento) return null

  const ubicacionDestino = pintarUbicacionDestino(movimiento)
  if (ubicacionDestino) return ubicacionDestino

  const persona = pintarPersona(movimiento)
  if (persona) return persona

  const ubicacionOrigen = pintarUbicacionOrigen(movimiento)
  if (ubicacionOrigen) return ubicacionOrigen

  return null
}

const validarDevolucion = async (movimiento) => {
  if (!movimiento) return
  if (!esAdmin.value) return
  if (!esMovimientoDevolucion(movimiento)) return
  if (movimiento.id_movimiento == null) return

  const idMov = movimiento.id_movimiento
  errorValidacion.value = ''
  validandoPorId.value = { ...validandoPorId.value, [idMov]: true }

  try {
    const fd = new FormData()
    fd.append('id_movimiento', String(idMov))
    fd.append('id_material', String(route.params.id))

    const res = await fetch(`${API_URL}/api/admin/validarDevolucion.php`, {
      method: 'POST',
      body: fd,
      credentials: 'include',
      cache: 'no-store',
    })

    const data = await res.json()
    if (!res.ok || !data.success) {
      throw new Error(data.error || 'No se pudo validar la devolución')
    }

    validadoPorId.value = { ...validadoPorId.value, [idMov]: true }
    if (material.value) {
      material.value.estado = 'devuelto'
    }
    await cargarMovimientos()
  } catch (e) {
    console.error(e)
    errorValidacion.value = e?.message || 'No se pudo validar la devolucion'
  } finally {
    validandoPorId.value = { ...validandoPorId.value, [idMov]: false }
  }
}
</script>

<template>
  <main class="container py-5">
    <h1 class="text-center mb-5">Detalle del material</h1>

    <div v-if="material" class="row justify-content-center mb-5">
      <div class="col-lg-8">
        <div class="card shadow">
          <div class="row g-0">
            <div class="col-md-4">
              <img
                src="../../assets/img/fondo_provisional.png"
                class="img-fluid rounded-start h-100"
                alt="Material"
              />
            </div>

            <div class="col-md-8">
              <div class="card-body">
                <h3 class="card-title mb-4">
                  {{ material.nombre }}
                </h3>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <strong>Código</strong>
                    <p>{{ material.codigo }}</p>
                  </div>

                  <div class="col-md-6 mb-3">
                    <strong>Categoría</strong>
                    <p>{{ material.categoria }}</p>
                  </div>

                  <div class="col-12 mb-3">
                    <strong>Descripción</strong>
                    <p>{{ material.descripcion }}</p>
                  </div>

                  <div class="col-md-6">
                    <strong>Estado actual</strong>
                    <p>{{ material.estado }}</p>
                  </div>
                </div>
              </div>

              <div class="card-footer bg-white">
                <div class="d-grid gap-2 d-md-flex">
                  <RouterLink
                    :to="`/Movimiento/${material.id_material}`"
                    class="btn btn-primary flex-fill"
                  >
                    Nuevo movimiento
                  </RouterLink>

                  <RouterLink
                    :to="`/Movimiento/${material.id_material}`"
                    class="btn btn-warning flex-fill"
                  >
                    Devolver
                  </RouterLink>

                  <button class="btn btn-danger flex-fill" @click="eliminarMaterial()">
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <h2 class="text-center mb-4">Historial de movimientos</h2>

    <p v-if="errorValidacion" class="text-center text-danger">
      {{ errorValidacion }}
    </p>

    <p v-if="cargando" class="text-center">Cargando historial...</p>

    <p v-else-if="error" class="text-center text-danger">
      {{ error }}
    </p>

    <p v-else-if="movimientos.length === 0" class="text-center text-muted">
      No hay movimientos registrados.
    </p>

    <div v-else class="row g-4">
      <div
        v-for="(movimiento, idx) in movimientos"
        :key="
          movimiento.id_movimiento ??
          `${movimiento.fecha_movimiento}-${movimiento.tipo_movimiento ?? ''}-${movimiento.nif ?? ''}-${movimiento.tipo_destino ?? ''}-${idx}`
        "
        class="col-lg-6"
      >
        <div class="card shadow h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <p>
              <strong>Estado:</strong>
              {{
                movimiento.tipo_movimiento?.charAt(0).toUpperCase() +
                movimiento.tipo_movimiento?.slice(1)
              }}
            </p>
            <span v-if="esMovimientoDevolucion(movimiento)" class="badge bg-warning text-dark">
              Esperando validación
            </span>
            <span v-if="movimiento.email_validador" class="text-success ms-2">
              | Validado por: {{ movimiento.email_validador }}
            </span>

            <button
              v-if="esMovimientoDevolucion(movimiento) && esAdmin"
              class="btn btn-sm btn-outline-success"
              :disabled="validandoPorId[movimiento.id_movimiento]"
              @click="validarDevolucion(movimiento)"
              type="button"
            >
              {{ validandoPorId[movimiento.id_movimiento] ? 'Validando...' : 'Devolver' }}
            </button>
          </div>

          <div class="card-body">
            <p class="text-muted">
              {{ movimiento.fecha_movimiento }}
            </p>

            <div v-if="pintarMovimiento(movimiento)?.tipo === 'persona'" class="row">
              <div class="col-md-6">
                <strong>Nombre</strong>
                <p>{{ pintarMovimiento(movimiento).nombre }}</p>
              </div>

              <div class="col-md-6">
                <strong>NIF</strong>
                <p>{{ pintarMovimiento(movimiento).nif }}</p>
              </div>

              <div class="col-md-6">
                <strong>Correo</strong>
                <p>{{ pintarMovimiento(movimiento).correo }}</p>
              </div>

              <div class="col-md-6">
                <strong>Teléfono</strong>
                <p>{{ pintarMovimiento(movimiento).telefono }}</p>
              </div>
            </div>

            <div v-else-if="pintarMovimiento(movimiento)?.tipo === 'ubicacion'" class="row">
              <div class="col-md-6">
                <strong>Tipo</strong>
                <p>{{ pintarMovimiento(movimiento).ubicacion }}</p>
              </div>

              <div class="col-md-6">
                <strong>Dirección</strong>
                <p>{{ pintarMovimiento(movimiento).direccion }}</p>
              </div>

              <div class="col-md-6">
                <strong>CP</strong>
                <p>{{ pintarMovimiento(movimiento).cp }}</p>
              </div>

              <div class="col-md-6">
                <strong>Provincia</strong>
                <p>{{ pintarMovimiento(movimiento).provincia }}</p>
              </div>
            </div>
            <p v-else class="text-muted">Sin información registrada</p>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
