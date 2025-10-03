<!-- resources/js/Home.vue -->
<template>
  <main class="p-6">
    <h1>Home</h1>
    <p>Bienvenido 👋</p>
    <router-link :to="{ name: 'enlistarTareas' }">
      <button>Enlistar Tareas</button>
    </router-link>
    <router-link :to="{ name: 'nuevaTarea' }">
      <button>Crear nueva tarea</button>
    </router-link>
  </main>
</template>

<script setup>
// si necesitas lógica, colócala aquí
</script>

<style scoped>
main { font-family: system-ui, sans-serif; }
button { padding: .5rem .75rem; border: 1px solid #ddd; border-radius: 6px; }

</style>
