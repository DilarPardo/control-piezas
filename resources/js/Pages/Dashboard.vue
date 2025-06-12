<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, watch, onMounted, nextTick } from 'vue'
import Chart from 'chart.js/auto'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  datosGraficos: Array,
  filtros: Object,
})

const fechaInicio = ref(props.filtros?.fecha_inicio || '')
const fechaFin = ref(props.filtros?.fecha_fin || '')
const graficoRef = ref(null)

const modalAbierto = ref(false)
const reporteFechaInicio = ref('')
const reporteFechaFin = ref('')

const renderChart = () => {
  const ctx = graficoRef.value
  if (!ctx || props.datosGraficos.length === 0) return

  const oldChart = Chart.getChart(ctx)
  if (oldChart) oldChart.destroy()

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: props.datosGraficos.map((d) => d.proyecto),
      datasets: [
        {
          label: 'Pendientes',
          data: props.datosGraficos.map((d) => d.pendientes),
          backgroundColor: 'rgba(255, 99, 132, 0.6)',
        },
        {
          label: 'Fabricados',
          data: props.datosGraficos.map((d) => d.fabricados),
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
  })
}

watch(
  () => props.datosGraficos,
  async () => {
    await nextTick()
    renderChart()
  },
  { immediate: true }
)

const aplicarFiltro = () => {
  router.get(
    '/dashboard',
    {
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value,
    },
    { preserveScroll: true, preserveState: true }
  )
}

const generarReporte = () => {
  if (!reporteFechaInicio.value || !reporteFechaFin.value) {
    alert('Por favor, selecciona ambas fechas.')
    return
  }

  const url = `/exportar-pendientes?fecha_inicio=${reporteFechaInicio.value}&fecha_fin=${reporteFechaFin.value}`
  window.open(url, '_blank') // abre en nueva pestaña o descarga directo
  modalAbierto.value = false
}
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Filtros -->
            <div class="mb-4">
              <h3 class="text-lg font-bold mb-2">Filtros por mes</h3>
              <div class="flex flex-wrap items-end gap-4">
                <div>
                  <label class="block text-sm font-medium">Mes de inicio</label>
                  <input type="month" v-model="fechaInicio" class="border rounded p-1" />
                </div>
                <div>
                  <label class="block text-sm font-medium">Mes de fin</label>
                  <input type="month" v-model="fechaFin" class="border rounded p-1" />
                </div>
                <button
                  @click="aplicarFiltro"
                  class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                  Aplicar filtro
                </button>

                <!-- Botón para generar reporte -->
                <button
                  @click="modalAbierto = true"
                  class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                  Crear reporte pieza
                </button>
              </div>
            </div>

            <!-- Modal -->
            <div v-if="modalAbierto" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
              <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Generar reporte de piezas pendientes</h3>
                <div class="mb-4">
                  <label class="block text-sm font-medium">Mes de inicio</label>
                  <input type="month" v-model="reporteFechaInicio" class="border rounded p-2 w-full" />
                </div>
                <div class="mb-4">
                  <label class="block text-sm font-medium">Mes de fin</label>
                  <input type="month" v-model="reporteFechaFin" class="border rounded p-2 w-full" />
                </div>
                <div class="flex justify-end gap-2">
                  <button
                    @click="modalAbierto = false"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                  >
                    Cancelar
                  </button>
                  <button
                    @click="generarReporte"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                  >
                    Generar
                  </button>
                </div>
              </div>
            </div>

            <!-- Gráfico -->
            <div class="mt-6">
              <div v-if="props.datosGraficos.length > 0" class="h-96">
                <canvas ref="graficoRef"></canvas>
              </div>
              <div v-else class="text-center text-gray-500 mt-10">
                No hay datos para mostrar en el gráfico con los filtros seleccionados.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
