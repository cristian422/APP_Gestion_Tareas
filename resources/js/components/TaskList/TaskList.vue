<!-- TaskList.vue -->
<template>
  <section class="task-list" :aria-busy="loading">
    <header class="tl-header">
      <h2>Listado de Tareas</h2>
      <div class="controls">
        <label>
          Por página:
          <select v-model.number="perPage">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
        </label>
      </div>
    </header>

    <div v-if="loading" class="state">Cargando tareas…</div>
    <div v-else-if="error" class="state error">⚠️ {{ error }}</div>

    <div v-else>
      <div v-if="tareas.length === 0" class="state">No hay tareas.</div>

      <table v-else class="tl-table">
        <thead>
          <tr>
            <th style="width:45%">Título</th>
            <th style="width:20%">Estado</th>
            <th style="width:25%">Palabras clave</th>
            <th style="width:10%"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in tareas" :key="t.id">
            <td>{{ t.nombre }}</td>
            <td>
              <span class="badge" :class="t.is_done ? 'done' : 'pending'">
                {{ t.is_done ? 'Hecha' : 'Pendiente' }}
              </span>
            </td>
            <td class="kw-cell">
              <span v-for="k in (t.keywords || [])" :key="k.id" class="kw-tag">
                {{ k.name }}
              </span>
              <span v-if="!t.keywords || t.keywords.length === 0" class="kw-empty">—</span>
            </td>
            <td>
              <button class="btn" @click="toggle(t)" :disabled="toggling.has(t.id)">
                {{ t.is_done ? 'Marcar pendiente' : 'Marcar hecha' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <nav v-if="meta" class="pager">
        <button class="btn" :disabled="!hasPrev" @click="go(meta.current_page - 1)">‹ Anterior</button>
        <span>Página {{ meta.current_page }} de {{ meta.last_page }}</span>
        <button class="btn" :disabled="!hasNext" @click="go(meta.current_page + 1)">Siguiente ›</button>
      </nav>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'

/**
 * Ajusta baseURL según tu proyecto:
 * - Si pusiste proxy en vite.config.js → usa '/api'
 * - O usa import.meta.env.VITE_API_URL
 */
const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
})

const tareas   = ref([])
const loading  = ref(false)
const error    = ref('')
const perPage  = ref(20)
const meta     = ref(null)
const toggling = ref(new Set())

const hasPrev = computed(() => !!meta.value?.prev_page_url || (meta.value && meta.value.current_page > 1))
const hasNext = computed(() => !!meta.value?.next_page_url || (meta.value && meta.value.current_page < meta.value.last_page))

watch(perPage, () => fetchTareas(1))

async function fetchTareas(page = 1) {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/tareas', { params: { page, per_page: perPage.value } })

    // Soporta tanto API Resource ({data, meta, links}) como paginate() clásico.
    if (Array.isArray(data?.data) && (data?.meta || 'current_page' in data)) {
      if (data.meta) {
        tareas.value = data.data
        meta.value = {
          current_page: data.meta.current_page,
          last_page: data.meta.last_page,
          next_page_url: data.links?.next ?? null,
          prev_page_url: data.links?.prev ?? null,
        }
      } else {
        tareas.value = data.data
        meta.value = {
          current_page: data.current_page,
          last_page: data.last_page,
          next_page_url: data.next_page_url,
          prev_page_url: data.prev_page_url,
        }
      }
    } else {
      tareas.value = Array.isArray(data) ? data : (data?.data ?? [])
      meta.value = null
    }
  } catch (e) {
    error.value = e?.response?.data?.message || e.message || 'Error cargando tareas'
  } finally {
    loading.value = false
  }
}

function go(page) {
  if (!meta.value) return
  if (page < 1 || page > meta.value.last_page) return
  fetchTareas(page)
}

async function toggle(task) {
  toggling.value.add(task.id)
  try {
    const { data } = await api.patch(`/tareas/${task.id}/toggle`)
    task.is_done = !!data.is_done
  } catch (e) {
    alert(e?.response?.data?.message || 'No se pudo cambiar el estado')
  } finally {
    toggling.value.delete(task.id)
  }
}

onMounted(() => fetchTareas())
</script>

<style scoped>
.task-list { max-width: 980px; margin: 24px auto; padding: 0 16px; }
.tl-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.controls select { padding: 6px 8px; }
.state { padding: 12px; }
.state.error { color: #b91c1c; }

.tl-table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.tl-table th, .tl-table td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
.tl-table thead th { background: #f8fafc; text-align: left; font-weight: 600; }

.badge { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; line-height: 1; }
.badge.done { background: #dcfce7; color: #166534; }
.badge.pending { background: #fee2e2; color: #991b1b; }

.kw-cell { white-space: normal; }
.kw-tag { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #eef2ff; color: #1e3a8a; font-size: 12px; margin: 2px 6px 2px 0; }
.kw-empty { color: #9ca3af; }

.btn { padding: 6px 10px; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 6px; cursor: pointer; }
.btn:disabled { opacity: .6; cursor: not-allowed; }

.pager { display: flex; align-items: center; gap: 12px; margin-top: 12px; }
</style>
