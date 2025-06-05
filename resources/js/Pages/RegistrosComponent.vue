<script setup>
import { ref, reactive } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  registros: Array,
  usuarioNombre: String,
  bloques: Array
})

const showModal = ref(false)
const isEdit = ref(false)
const form = reactive({
  pieza: '',
  peso_teorico: '',
  peso_real: '',
  estado: 'Pendiente',
  id_bloque: '',
  fecha_registro: '',
  registrado_por: ''
})

function openCreateModal() {
  isEdit.value = false
  Object.assign(form, {
    pieza: '',
    peso_teorico: '',
    peso_real: '',
    estado: 'Pendiente',
    id_bloque: '',
    fecha_registro: new Date().toISOString().slice(0, 10),
    registrado_por: props.usuarioNombre 
  })
  showModal.value = true
}

function openEditModal(registro) {
  isEdit.value = true
  Object.assign(form, { ...registro })
  showModal.value = true
}

function saveRegistro() {
  const payload = { ...form }
  if (isEdit.value) {
    router.put(`/registros/${form.id_pieza}`, payload, {
      onSuccess: () => {
        alert('Registro actualizado correctamente.')
        showModal.value = false
        router.reload({ only: ['registros'] })
      },
      onError: () => alert('Ocurrió un error al actualizar el registro.')
    })
  } else {
    router.post('/registros', payload, {
      onSuccess: () => {
        alert('Registro creado correctamente.')
        showModal.value = false
        router.reload({ only: ['registros'] })
      },
      onError: () => alert('Ocurrió un error al crear el registro.')
    })
  }
}

function deleteRegistro(id) {
  if (confirm('¿Estás seguro de eliminar este registro?')) {
    router.delete(`/registros/${id}`, {
      onSuccess: () => {
        alert('Registro eliminado correctamente.')
        router.reload({ only: ['registros'] })
      },
      onError: () => alert('Ocurrió un error al eliminar el registro.')
    })
  }
}
</script>

<template>
  <Head title="Registros" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Listado de Registros
      </h2>
    </template>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="mb-4">
        <button
          @click="openCreateModal"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Agregar nuevo
        </button>
      </div>

      <table class="min-w-full bg-white border border-gray-200 shadow rounded">
        <thead>
          <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">Pieza</th>
            <th class="px-4 py-2 border">Peso Teórico</th>
            <th class="px-4 py-2 border">Peso Real</th>
            <th class="px-4 py-2 border">Estado</th>
            <th class="px-4 py-2 border">Bloque</th>
            <th class="px-4 py-2 border">Fecha</th>
            <th class="px-4 py-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(registro, index) in props.registros"
            :key="registro.id_pieza"
            class="text-sm"
          >
            <td class="px-4 py-2 border">{{ index + 1 }}</td>
            <td class="px-4 py-2 border">{{ registro.pieza }}</td>
            <td class="px-4 py-2 border">{{ registro.peso_teorico }}</td>
            <td class="px-4 py-2 border">{{ registro.peso_real ?? '-' }}</td>
            <td class="px-4 py-2 border">{{ registro.estado }}</td>
            <td class="px-4 py-2 border">{{ registro.id_bloque }}</td>
            <td class="px-4 py-2 border">{{ registro.fecha_registro }}</td>
            <td class="px-4 py-2 border">
              <button
                @click="openEditModal(registro)"
                class="text-blue-600 hover:underline mr-2"
              >
                Editar
              </button>
              <button
                @click="deleteRegistro(registro.id_pieza)"
                class="text-red-600 hover:underline"
              >
                Eliminar
              </button>
            </td>
          </tr>
          <tr v-if="props.registros.length === 0">
            <td colspan="8" class="text-center py-4 text-gray-500">
              No hay registros
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4"
    >
      <div
        class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-y-auto max-h-[90vh]"
      >
        <h3 class="text-lg font-semibold mb-4">
          {{ isEdit ? 'Editar Registro' : 'Agregar Registro' }}
        </h3>
        <form @submit.prevent="saveRegistro" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Nombre de Pieza
            </label>
            <input
              v-model="form.pieza"
              type="text"
              maxlength="50"
              required
              class="w-full border border-gray-300 rounded px-3 py-2"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Peso Teórico
            </label>
            <input
              v-model="form.peso_teorico"
              type="number"
              step="0.01"
              required
              class="w-full border border-gray-300 rounded px-3 py-2"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Peso Real
            </label>
            <input
              v-model="form.peso_real"
              type="number"
              step="0.01"
              class="w-full border border-gray-300 rounded px-3 py-2"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Estado
            </label>
            <select
              v-model="form.estado"
              class="w-full border border-gray-300 rounded px-3 py-2"
            >
              <option value="Fabricado">Fabricado</option>
              <option value="Pendiente">Pendiente</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Bloque
            </label>
            <select
              v-model="form.id_bloque"
              class="w-full border border-gray-300 rounded px-3 py-2"
            >
              <option value="" disabled>Selecciona un bloque</option>
              <option
                v-for="bloque in props.bloques"
                :key="bloque.id_bloque"
                :value="bloque.id_bloque"
              >
                {{ bloque.nombre_bloque }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Fecha Registro
            </label>
            <input
              v-model="form.fecha_registro"
              type="date"
              required
              class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
              disabled
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Registrado por
            </label>
            <input
              v-model="form.registrado_por"
              type="text"
              maxlength="50"
              class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
              disabled
            />
          </div>

          <div class="flex justify-end space-x-2 pt-4">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              {{ isEdit ? 'Actualizar' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
