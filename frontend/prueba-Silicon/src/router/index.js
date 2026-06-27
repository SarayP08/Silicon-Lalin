import { createRouter, createWebHistory } from 'vue-router'

import Index from '../views/public/Index.vue'
import Registro from '../views/public/Registro.vue'
import HomeUsuario from '../views/usuario/HomeUsuario.vue'
import ListadoMercancias from '../views/usuario/ListadoMercancias.vue'
import AnhadirMaterial from '../views/usuario/AnhadirMaterial.vue'
import DetalleMercancia from '../views/usuario/DetalleMercancia.vue'
import MoverMaterial from '../views/usuario/MoverMaterial.vue'

const routes = [
  { path: '/', component: Index },
  { path: '/Registro', component: Registro },
  { path: '/HomeUsuario', component: HomeUsuario },
  { path: '/Mercancias', component: ListadoMercancias },
  { path: '/Añadir', component: AnhadirMaterial },
  { path: '/Detalle/:id', component: DetalleMercancia },
  { path: '/Movimiento/:id', component: MoverMaterial },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
