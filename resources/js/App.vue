<!-- resources/js/components/App.vue -->
<template>
  <div class="app pastel">
    <!-- Topbar -->
    <header class="topbar">
      <div class="wrap">
        <div class="brand">
          <span class="logo" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.9L18.18 22 12 18.77 5.82 22 7 14.17l-5-4.9 6.91-1.01L12 2z"/>
            </svg>
          </span>
          <h1>Gestión de Tareas</h1>
        </div>

        <nav class="nav">
          <RouterLink to="/" class="nav-link">Inicio</RouterLink>
          <!-- <RouterLink to="/crear" class="nav-link">Crear</RouterLink> -->
        </nav>
      </div>
    </header>

    <main class="content wrap">
      <!-- 📌 Breadcrumb -->
      <nav v-if="crumbs.length" class="breadcrumb-wrap" aria-label="Breadcrumb">
        <ul class="breadcrumb">
          <li>
            <RouterLink to="/">Inicio</RouterLink>
          </li>
          <li v-for="(c, i) in crumbs" :key="i">
            <template v-if="c.to">
              <RouterLink :to="c.to">{{ c.label }}</RouterLink>
            </template>
            <template v-else>
              <a class="active">{{ c.label }}</a>
            </template>
          </li>
        </ul>
      </nav>

      <div class="shell">
        <RouterView />
      </div>
    </main>

    <footer class="footer">
      <div class="wrap">
        <p>© {{ year }} AppGestionarTareas — <span class="muted">Laravel + Vue</span></p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const year = new Date().getFullYear()
const route = useRoute()

// Construye migas a partir de las rutas "matched"
// Usa meta.breadcrumb (o meta.title / name / path como fallback)
const crumbs = computed(() => {
  const m = route.matched.filter(r => r.path) // evita raíz vacía
  return m.map((r, idx) => ({
    label: r.meta?.breadcrumb ?? r.meta?.title ?? r.name ?? r.path,
    // Enlaza todas menos la última
    to: idx < m.length - 1 ? { name: r.name, params: route.params, query: route.query } : null,
  }))
})
</script>

<style scoped>
/* ===== Paleta pastel (clara) ===== */
:root {
  --bg: #f8fafc;
  --bg-grad-a: #f8fafc;
  --bg-grad-b: #f5f7ff;
  --bg-grad-c: #fef7fb;

  --text: #2b2f36;
  --muted: #6b7280;

  --card: #ffffff;
  --border: #e9eef5;
  --shadow: 0 12px 28px rgba(22, 29, 59, .08);

  --primary: #c7b9ff;   /* lavanda */
  --primary-2: #a5f3d5; /* menta  */
  --ring: rgba(199, 185, 255, .45);
  --chip-bg: #e8eaff;
}

/* wrapper */
.app {
  min-height: 100dvh;
  background:
    radial-gradient(1200px 600px at 10% -10%, rgba(165, 243, 213, .25), transparent 60%),
    radial-gradient(1000px 500px at 120% 10%, rgba(199, 185, 255, .25), transparent 55%),
    linear-gradient(180deg, var(--bg-grad-a), var(--bg-grad-b) 40%, var(--bg-grad-c));
  color: var(--text);
}

.wrap { width: min(1120px, 92%); margin-inline: auto; }

/* ===== Topbar ===== */
.topbar {
  position: sticky; top: 0; z-index: 20;
  background: linear-gradient(180deg, rgba(255,255,255,.85), rgba(255,255,255,.65));
  backdrop-filter: blur(6px);
  border-bottom: 1px solid var(--border);
}
.topbar .wrap { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; }

.brand { display: flex; align-items: center; gap: 10px; }
.brand h1 { font-size: 18px; font-weight: 700; letter-spacing: .2px; margin: 0; color: #374151; }

.logo {
  display: inline-grid; place-items: center;
  width: 34px; height: 34px; border-radius: 10px;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color: #2b2f36;
  box-shadow: 0 6px 18px rgba(137, 115, 255, .30);
}

/* ===== Nav ===== */
.nav { display: flex; align-items: center; gap: 6px; }
.nav-link {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 8px 12px;
  border-radius: 10px;
  color: #4b5563;
  text-decoration: none;
  font-weight: 600;
  transition: transform .15s ease, background-color .2s ease, color .2s ease;
}
.nav-link:hover { transform: translateY(-1px); background: rgba(165, 243, 213, .35); color: #111827; }
.nav-link.router-link-exact-active {
  background: var(--chip-bg);
  outline: 1px solid rgba(199, 185, 255, .6);
  box-shadow: 0 0 0 6px var(--ring);
  color: #111827;
}

/* ===== Breadcrumb ===== */
.breadcrumb-wrap { margin: 8px 0 18px; }
ul.breadcrumb {
  padding: 8px 12px;
  list-style: none;
  background-color: #f2f4f8;
  border: 1px solid var(--border);
  border-radius: 10px;
}
ul.breadcrumb li { display: inline; }
ul.breadcrumb li + li:before {
  padding: 0 8px;
  color: #6b7280;
  content: "/\00a0";
}
ul.breadcrumb a,
ul.breadcrumb a:visited {
  text-decoration: none;
  color: #374151;
}
ul.breadcrumb a:hover { text-decoration: underline; }
ul.breadcrumb a.active {
  color: #4CAF50;
  font-weight: 700;
}
ul.breadcrumb a.active:hover { color: #397f3b; }

/* ===== Content / Shell ===== */
.content { padding: 28px 0 40px; }
.shell {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 18px;
  box-shadow: var(--shadow);
  padding: clamp(16px, 2vw, 24px);
  outline: 1px solid rgba(255,255,255,.6);
}
.shell :is(h1,h2,h3) { margin-top: 0; }

/* ===== Footer ===== */
.footer {
  border-top: 1px solid var(--border);
  padding: 18px 0 26px;
  background: linear-gradient(180deg, rgba(255,255,255,.5), rgba(255,255,255,.2));
}
.footer p { margin: 0; font-size: 13px; color: var(--text); }
.muted { color: var(--muted); }

/* ===== Responsive ===== */
@media (max-width: 640px) {
  .brand h1 { font-size: 16px; }
  .nav-link { padding: 7px 10px; font-size: 14px; }
}
</style>
