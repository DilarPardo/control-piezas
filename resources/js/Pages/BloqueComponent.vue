<script setup>
import { ref, reactive } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
    bloques: Array,
    proyectos: Array
})

// Modal y formulario
const showModal = ref(false)
const isEdit = ref(false)
const form = reactive({
    id_bloque: null,
    nombre_bloque: '',
    id_proyecto: ''
})
const errors = reactive({
    nombre_bloque: '',
    id_proyecto: ''
})

function resetForm() {
    form.id_bloque = null
    form.nombre_bloque = ''
    form.id_proyecto = ''
    errors.nombre_bloque = ''
    errors.id_proyecto = ''
}

function openCreateModal() {
    isEdit.value = false
    resetForm()
    showModal.value = true
}

function openEditModal(bloque) {
    isEdit.value = true
    form.id_bloque = bloque.id_bloque
    form.nombre_bloque = bloque.nombre_bloque
    form.id_proyecto = bloque.id_proyecto
    errors.nombre_bloque = ''
    errors.id_proyecto = ''
    showModal.value = true
}

function validateForm() {
    let valid = true
    errors.nombre_bloque = ''
    errors.id_proyecto = ''

    if (!form.nombre_bloque.trim()) {
        errors.nombre_bloque = 'El nombre del bloque es obligatorio.'
        valid = false
    } else if (form.nombre_bloque.length > 10) {
        errors.nombre_bloque = 'El nombre no debe exceder 10 caracteres.'
        valid = false
    }

    if (!form.id_proyecto) {
        errors.id_proyecto = 'Debe seleccionar un proyecto.'
        valid = false
    }

    return valid
}

function saveBloque() {
    if (!validateForm()) return

    const payload = {
        nombre_bloque: form.nombre_bloque,
        id_proyecto: form.id_proyecto
    }

    const successCallback = () => {
        alert(isEdit.value ? 'Bloque actualizado correctamente.' : 'Bloque creado correctamente.')
        showModal.value = false
        router.reload({ only: ['bloques'] })
    }

    const errorCallback = () => {
        alert('Ocurrió un error al guardar el bloque.')
    }

    if (isEdit.value) {
        router.put(`/bloques/${form.id_bloque}`, payload, {
            onSuccess: successCallback,
            onError: errorCallback
        })
    } else {
        router.post('/bloques', payload, {
            onSuccess: successCallback,
            onError: errorCallback
        })
    }
}

function deleteBloque(id) {
    if (confirm('¿Estás seguro de eliminar este bloque?')) {
        router.delete(`/bloques/${id}`, {
            onSuccess: () => {
                alert('Bloque eliminado correctamente.')
                router.reload({ only: ['bloques'] })
            },
            onError: () => {
                alert('Ocurrió un error al eliminar el bloque.')
            }
        })
    }
}
</script>

<template>
    <Head title="Bloques" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listado de Bloques</h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <button @click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Agregar nuevo
                </button>
            </div>

            <table class="min-w-full bg-white border border-gray-200 shadow rounded">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Nombre Bloque</th>
                        <th class="px-4 py-2 border">Proyecto</th>
                        <th class="px-4 py-2 border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(bloque, index) in props.bloques" :key="bloque.id_bloque" class="text-sm">
                        <td class="px-4 py-2 border">{{ index + 1 }}</td>
                        <td class="px-4 py-2 border">{{ bloque.nombre_bloque }}</td>
                        <td class="px-4 py-2 border">{{ bloque.proyecto?.nombre ?? 'Sin proyecto' }}</td>
                        <td class="px-4 py-2 border">
                            <button @click="openEditModal(bloque)" class="text-blue-600 hover:underline mr-2">Editar</button>
                            <button @click="deleteBloque(bloque.id_bloque)" class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                    <tr v-if="props.bloques.length === 0">
                        <td colspan="4" class="text-center py-4 text-gray-500">No hay bloques registrados</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">
                    {{ isEdit ? 'Editar Bloque' : 'Agregar Bloque' }}
                </h3>
                <form @submit.prevent="saveBloque">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del bloque</label>
                        <input
                            v-model="form.nombre_bloque"
                            type="text"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            :class="{ 'border-red-500': errors.nombre_bloque }"
                            maxlength="10"
                        />
                        <p v-if="errors.nombre_bloque" class="text-red-600 text-sm mt-1">{{ errors.nombre_bloque }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto</label>
                        <select
                            v-model="form.id_proyecto"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            :class="{ 'border-red-500': errors.id_proyecto }"
                        >
                            <option value="" disabled>Selecciona un proyecto</option>
                            <option v-for="proyecto in props.proyectos" :key="proyecto.id_proyecto" :value="proyecto.id_proyecto">
                                {{ proyecto.nombre }}
                            </option>
                        </select>
                        <p v-if="errors.id_proyecto" class="text-red-600 text-sm mt-1">{{ errors.id_proyecto }}</p>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ isEdit ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
