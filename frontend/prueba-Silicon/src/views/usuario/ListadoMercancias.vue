<script setup>
import { computed, onMounted, ref } from 'vue'
import { API_URL } from '../../config/api.js'

const materiales = ref([])
const filtroNombre = ref('')

onMounted(async () => {
  try {
    const res = await fetch(`${API_URL}/api/materiales/despliegueMateriales.php`, {
      credentials: 'include',
    })

    const data = await res.json()
    console.log('Materiales recibidos:', data)

    materiales.value = data
  } catch (error) {
    console.error('Error cargando materiales:', error)
  }
})

const materialFiltrado = computed(() => {
  return materiales.value.filter((material) => {

    const coincideNombre =
      !filtroNombre.value ||
      material.nombre?.toLowerCase().includes(filtroNombre.value.toLowerCase())

    return coincideNombre
  })
})

</script>
<template>
  <div class="container mt-5 gatos-page">
    <h1 class="mb-4 text-center titulo-gatos">Materiales disponibles</h1>

    <div class="row mb-4">
      <div class="col-md-3">
        <input v-model="filtroNombre" class="form-control" placeholder="Buscar por nombre" />
      </div>
    </div>
      <div class="row g-4">
        <div v-for="material in materialFiltrado" :key="material.id_material" class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100">
        <img src="../../assets/img/fondo_provisional.png" alt="imagen"/>

        <div class="card-body d-flex flex-column">
          <h5 class="card-title">{{ material.nombre }}</h5>

          <RouterLink :to="`/Detalle/${material.id_material}`" class="btn btn-detalle flex-fill">
            Trazabilidad
          </RouterLink>
        </div>
      </div>
        </div>
      </div>
    </div>
</template>
