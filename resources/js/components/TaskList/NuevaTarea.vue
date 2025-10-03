<!-- CreateTask.vue (multi-select con checkboxes + chips) -->
<template>
  <section class="card">
    <h2>Crear tarea</h2>

    <form @submit.prevent="submit" novalidate>
      <div class="field">
        <label for="nombre">Título *</label>
        <input
          id="nombre"
          v-model.trim="form.nombre"
          type="text"
          required
          :disabled="submitting"
          placeholder="Ej. Comprar insumos"
        />
      </div>

      <div class="field">
        <label for="descripcion">Descripción</label>
        <textarea
          id="descripcion"
          v-model.trim="form.descripcion"
          rows="3"
          :disabled="submitting"
          placeholder="Detalles de la tarea (opcional)"
        ></textarea>
      </div>

      <!-- 🔹 Agregar palabras clave (existentes del backend) -->
      <div class="field">
        <label>Agregar palabras clave</label>

        <!-- chips seleccionadas -->
        <div class="chips" v-if="selectedKeywords.length">
          <span v-for="k in selectedKeywords" :key="k.id" class="chip">
            {{ k.name }}
            <button type="button" class="x" @click="toggleKeyword(k.id)" aria-label="Quitar">×</button>
          </span>
        </div>

        <!-- buscador -->
        <div class="kw-toolbar">
          <input
            v-model.trim="kwSearch"
            type="search"
            placeholder="Buscar keyword…"
            :disabled="loadingKeywords || submitting"
          />
          <small v-if="loadingKeywords">Cargando keywords…</small>
        </div>

        <!-- lista de selección múltiple con checkboxes -->
        <div class="kw-list" role="listbox" aria-multiselectable="true">
          <label
            v-for="k in filteredKeywords"
            :key="k.id"
            class="kw-item"
          >
            <input
              type="checkbox"
              :value="k.id"
              :checked="isSelected(k.id)"
              @change="toggleKeyword(k.id)"
              :disabled="submitting"
            />
            <span>{{ k.name }}</span>
          </label>
          <div v-if="!loadingKeywords && filteredKeywords.length === 0" class="empty">
            Sin resultados.
          </div>
        </div>

        <small v-if="!loadingKeywords && keywords.length === 0">
          No hay keywords registradas.
        </small>
      </div>

      <div v-if="error" class="error">⚠️ {{ error }}</div>
      <ul v-if="fieldErrors.length" class="error-list">
        <li v-for="(msg, i) in fieldErrors" :key="i">• {{ msg }}</li>
      </ul>

      <div class="actions">
        <button class="btn" type="submit" :disabled="submitting">
          {{ submitting ? 'Guardando…' : 'Crear tarea' }}
        </button>
        <button class="btn ghost" type="button" @click="reset" :disabled="submitting">
          Limpiar
        </button>
      </div>

      <p v-if="success" class="ok">✅ Tarea creada</p>
    </form>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Usa proxy de Vite o ENV
const api = axios.create({ baseURL: '/api', timeout: 12000 })

const form = ref({
  nombre: '',
  descripcion: '',
  keyword_ids: [], // array de IDs (números)
})

const submitting  = ref(false)
const success     = ref(false)
const error       = ref('')
const fieldErrors = ref([])

// --- Keywords existentes ---
const keywords        = ref([])   // [{id, name}, ...]
const loadingKeywords = ref(false)
const kwSearch        = ref('')

const filteredKeywords = computed(() => {
  const q = kwSearch.value.toLowerCase()
  if (!q) return keywords.value
  return keywords.value.filter(k => k.name.toLowerCase().includes(q))
})

const selectedKeywords = computed(() =>
  keywords.value.filter(k => form.value.keyword_ids.includes(k.id))
)

function isSelected(id) {
  return form.value.keyword_ids.includes(id)
}

function toggleKeyword(id) {
  const arr = form.value.keyword_ids
  const idx = arr.indexOf(id)
  if (idx === -1) arr.push(id)
  else arr.splice(idx, 1)
}

async function fetchKeywords() {
  loadingKeywords.value = true
  try {
    // Puedes pedir sin paginar: /api/keywords?per_page=0
    const { data } = await api.get('/keywords', { params: { per_page: 0 } })
    const list = Array.isArray(data) ? data : (data.data ?? [])
    // Asegura IDs numéricos
    keywords.value = list.map(k => ({ id: Number(k.id), name: k.name }))
    // Si había valores como strings en keyword_ids (por navegación previa), normaliza:
    form.value.keyword_ids = form.value.keyword_ids.map(Number)
  } catch (e) {
    console.warn('No se pudieron cargar keywords', e)
    keywords.value = []
  } finally {
    loadingKeywords.value = false
  }
}

function reset() {
  form.value = { nombre: '', descripcion: '', keyword_ids: [] }
  error.value = ''
  fieldErrors.value = []
  success.value = false
}

async function submit() {
  error.value = ''
  fieldErrors.value = []
  success.value = false

  if (!form.value.nombre.trim()) {
    fieldErrors.value.push('El título (nombre) es obligatorio.')
    return
  }

  submitting.value = true
  try {
    const payload = {
      nombre: form.value.nombre.trim(),
      descripcion: form.value.descripcion?.trim() || null,
      keyword_ids: form.value.keyword_ids.map(Number), // 👈 asegura números
    }
    await api.post('/tareas', payload)

    success.value = true
    reset()
    // opcional: emit('created')
  } catch (e) {
    if (e.response?.status === 422 && e.response.data?.errors) {
      fieldErrors.value = Object.values(e.response.data.errors).flat()
    } else {
      error.value = e.response?.data?.message || e.message || 'No se pudo crear la tarea'
    }
  } finally {
    submitting.value = false
  }
}

onMounted(fetchKeywords)
</script>

<style scoped>
.card { max-width: 720px; margin: 24px auto; padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; }
h2 { margin: 0 0 12px; }
.field { display: grid; gap: 6px; margin-bottom: 12px; }
input, textarea { width: 100%; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; }

.kw-toolbar { display: flex; align-items: center; gap: 8px; }
.kw-list {
  margin-top: 6px;
  border: 1px solid #e5e7eb; border-radius: 10px; background: #fafbff;
  max-height: 220px; overflow: auto; padding: 6px;
}
.kw-item {
  display: flex; align-items: center; gap: 10px;
  padding: 6px 8px; border-radius: 8px;
}
.kw-item:hover { background: #f3f5ff; }
.kw-item input { width: 16px; height: 16px; }

.chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 6px; }
.chip {
  display: inline-flex; align-items: center; gap: 6px;
  background: #e8eaff; color: #1e293b;
  padding: 4px 10px; border-radius: 999px; font-size: 12px;
  border: 1px solid #dfe5ff;
}
.chip .x {
  background: transparent; border: 0; cursor: pointer; font-size: 14px; line-height: 1;
}

.actions { display: flex; gap: 8px; margin-top: 8px; }
.btn { padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; background: #f8fafc; cursor: pointer; }
.btn.ghost { background: transparent; }
.btn:disabled { opacity: .6; cursor: not-allowed; }

.error { color: #b91c1c; margin: 8px 0; }
.error-list { color: #b91c1c; margin: 8px 0; padding-left: 18px; }
.ok { color: #166534; margin-top: 8px; }
.empty { color: #64748b; font-size: 14px; padding: 6px 8px; }
</style>
