import { createRouter, createWebHistory } from 'vue-router'

const Home     = () => import('./Home.vue')
const Crear    = () => import('./Components/Usuarios/Crear.vue')
const Editar   = () => import('./Components/Usuarios/Editar.vue')
const Ver      = () => import('./Components/Usuarios/Mostrar.vue')
const TaskList = () => import('./components/TaskList/TaskList.vue')
const NuevaTarea = () => import('./components/TaskList/NuevaTarea.vue')

const routes = [
  // elige tu landing:
  { path: '/', name: 'home', component: Home },
  { path: '/crear', name: 'crear', component: Crear },
  { path: '/editar/:id', name: 'editar', component: Editar, props: true },
  { path: '/ver/:id', name: 'ver', component: Ver, props: true },
  { path: '/enlistarTareas', name: 'enlistarTareas', component: TaskList },
  {path: '/nuevaTarea', name: 'nuevaTarea', component: NuevaTarea},
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
