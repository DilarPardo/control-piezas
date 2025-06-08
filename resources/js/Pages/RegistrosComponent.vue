<script setup>
import { ref, reactive, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  registros: Array,
  usuarioNombre: String,
  bloques: Array,
  piezas: Array 
})

const showModal = ref(false)
const isEdit = ref(false)
const isLoading = ref(false)

const form = reactive({
  id_pieza: '',
  pieza: '',
  peso_teorico: '',
  peso_real: '',
  diferencia: '', // 🔴 Se agregó este campo, necesario para el v-model en el input
  estado: 'Pendiente',
  id_bloque: '',
  fecha_registro: '',
  registrado_por: ''
})

const errors = reactive({ peso_teorico: '' })

// Filtra piezas por bloque y estado pendiente
const piezasFiltradas = ref([])
watch(() => form.id_bloque, val => {
  piezasFiltradas.value = props.piezas.filter(
    p => p.id_bloque === val && p.estado === 'Pendiente'
  )
})

// Cuando seleccionas la pieza, carga peso teórico
watch(() => form.id_pieza, val => {
  const sel = props.piezas.find(p => p.id_pieza === val)
  if (sel) {
    form.pieza = sel.pieza
    form.peso_teorico = sel.peso_teorico
    form.diferencia = ''
  } else {
    form.pieza = ''
    form.peso_teorico = ''
    form.diferencia = ''
  }
})

// Al cambiar peso_real, calcula diferencia
watch(() => form.peso_real, val => {
  const pt = parseFloat(form.peso_teorico) || 0
  const pr = parseFloat(val) || 0
  form.diferencia = (pr - pt).toFixed(2)
})

function openCreateModal() {
  isEdit.value = false
  Object.assign(form, {
    id_pieza: '',
    pieza: '',
    peso_teorico: '',
    peso_real: '',
    diferencia: '',
    estado: 'Pendiente',
    id_bloque: '',
    fecha_registro: new Date().toISOString().slice(0, 10),
    registrado_por: props.usuarioNombre
  })
  errors.peso_teorico = ''
  showModal.value = true
}

function openEditModal(registro) {
  isEdit.value = true
  Object.assign(form, {
    ...registro,
    diferencia: (registro.peso_real - registro.peso_teorico).toFixed(2)
  })
  showModal.value = true
}

function saveRegistro() {
  errors.peso_teorico = ''
  const pt = parseFloat(form.peso_teorico)
  if (isNaN(pt) || pt <= 0) {
    errors.peso_teorico = 'El peso teórico debe ser mayor que cero.'
    form.peso_teorico = ''
    return
  }

  isLoading.value = true
  const payload = { ...form }

  const finish = () => {
    isLoading.value = false
    showModal.value = false
    router.reload({ only: ['registros'] })
    isEdit.value = false
  }

  const errorFn = () => {
    alert('Ocurrió un error al guardar.')
    isLoading.value = false
  }

  if (isEdit.value && form.id_registro) {
    router.put(`/registros/${form.id_registro}`, payload, { onSuccess: finish, onError: errorFn })
  } else {
    router.post('/registros', payload, { onSuccess: finish, onError: errorFn })
  }
}

function deleteRegistro(id) {
  if (confirm('¿Eliminar este registro?')) {
    router.delete(`/registros/${id}`, {
      onSuccess: () => router.reload({ only: ['registros'] }),
      onError: () => alert('Error al eliminar.')
    })
  }
}
</script>

<template>
  <Head title="Registros" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800">Listado de Registros</h2>
    </template>

    <div class="py-6 max-w-7xl mx-auto px-6">
      <button @click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 hover:bg-blue-700">
        Agregar nuevo
      </button>

      <table class="min-w-full bg-white border shadow rounded">
        <thead>
          <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2">Pieza</th>
            <th class="px-4 py-2">Teórico</th>
            <th class="px-4 py-2">Real</th>
            <th class="px-4 py-2">Diferencia</th>
            <th class="px-4 py-2">Estado</th>
            <th class="px-4 py-2">Bloque</th>
            <th class="px-4 py-2">Fecha</th>
            <th class="px-4 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(reg, i) in props.registros" :key="reg.id_registro">
            <td class="px-4 py-2 border">{{ i + 1 }}</td>
            <td class="px-4 py-2 border">{{ reg.pieza }}</td>
            <td class="px-4 py-2 border">{{ reg.peso_teorico }}</td>
            <td class="px-4 py-2 border">{{ reg.peso_real ?? '-' }}</td>
            <td class="px-4 py-2 border">
              {{ reg.peso_real !== null ? (reg.peso_real - reg.peso_teorico).toFixed(2) : '-' }}
            </td>
            <td class="px-4 py-2 border">{{ reg.estado }}</td>
            <td class="px-4 py-2 border">{{ reg.bloque.nombre_bloque }}</td>
            <td class="px-4 py-2 border">{{ reg.fecha_registro }}</td>
            <td class="px-4 py-2 border">
              <button @click="openEditModal(reg)" class="text-blue-600 hover:underline mr-2">Editar</button>
              <button @click="deleteRegistro(reg.id_registro)" class="text-red-600 hover:underline">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]">
        <h3 class="text-lg font-semibold mb-4">{{ isEdit ? 'Editar Registro' : 'Agregar Registro' }}</h3>

        <form @submit.prevent="saveRegistro" class="space-y-4">
          <div>
            <label>Bloque</label>
            <select v-model="form.id_bloque" class="w-full border rounded px-3 py-2">
              <option value="" disabled>Seleccione bloque</option>
              <option v-for="b in props.bloques" :key="b.id_bloque" :value="b.id_bloque">
                {{ b.nombre_bloque }}
              </option>
            </select>
          </div>

          <div>
            <label class="block font-medium mb-1">Pieza</label>
            <select v-model="form.id_pieza" class="w-full border rounded px-3 py-2" :disabled="piezasFiltradas.length === 0">
              <option disabled value="">Seleccione una pieza</option>
                <option v-for="pieza in piezasFiltradas" :key="pieza.id_pieza" :value="pieza.id_pieza">
                  {{ pieza.pieza }}
                </option>
            </select>
          </div>

          <div class="flex space-x-4">
            <div class="flex-1">
              <label>Peso Teórico</label>
              <input v-model="form.peso_teorico" disabled class="w-full bg-gray-100 rounded px-3 py-2" />
            </div>
            <div class="flex-1">
              <label>Peso Real</label>
              <input v-model="form.peso_real" type="number" step="0.01" class="w-full border rounded px-3 py-2" />
            </div>
            <div class="flex-1">
              <label>Diferencia</label>
              <input v-model="form.diferencia" disabled class="w-full bg-gray-100 rounded px-3 py-2" />
            </div>
          </div>
          <p v-if="errors.peso_teorico" class="text-red-600">{{ errors.peso_teorico }}</p>

          <div>
            <label>Estado</label>
            <select v-model="form.estado" class="w-full border rounded px-3 py-2">
              <option value="Fabricado">Fabricado</option>
              <option value="Pendiente">Pendiente</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label>Fecha registro</label>
              <input v-model="form.fecha_registro" type="date" disabled class="w-full bg-gray-100 rounded px-3 py-2" />
            </div>
            <div>
              <label>Registrado por</label>
              <input v-model="form.registrado_por" disabled class="w-full bg-gray-100 rounded px-3 py-2" />
            </div>
          </div>

          <div class="flex justify-end space-x-2">
            <button @click="showModal = false" type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Cancelar
            </button>
            <button type="submit" :disabled="isLoading" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              {{ isLoading ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Guardar') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
