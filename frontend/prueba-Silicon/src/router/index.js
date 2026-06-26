import { createRouter, createWebHistory } from 'vue-router'

import Index from "../views/public/Index.vue";
import Registro from "../views/public/Registro.vue"; 
import AdminHome from "../views/administrador/AdminHome.vue";
import ListadoMercancias  from "../views/usuario/ListadoMercancias.vue";
import AnhadirMaterial from "../views/usuario/AnhadirMaterial.vue";
import DetalleMercancia from "../views/usuario/DetalleMercancia.vue";

const routes = [
  {path: "/", component: Index}, 
  {path: "/Registro", component: Registro},
  {path: "/HomeAdmin", component: AdminHome},
  {path: "/Mercancias", component: ListadoMercancias}, 
  {path: "/Añadir", component: AnhadirMaterial},
  {path: "/Detalle/:id", component: DetalleMercancia}
]; 


const router = createRouter( {
  history: createWebHistory(),
  routes,
})

export default router;