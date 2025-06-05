<script setup>
import { ref, reactive, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    proyectos: Array,
    flash: String
})

const page = usePage()

const showModal = ref(false)
const isEdit = ref(false)
const form = reactive({
    id_proyecto: null,
    nombre: ''
})

watch(() => page.props.flash?.message, (message) => {
    if (message) {
        alert(message)
    }
})

// Abrir modal para crear
function openCreateModal() {
    isEdit.value = false
    form.id_proyecto = null
    form.nombre = ''
    showModal.value = true
}

// Abrir modal para editar
function openEditModal(proyecto) {
    isEdit.value = true
    form.id_proyecto = proyecto.id_proyecto
    form.nombre = proyecto.nombre
    showModal.value = true
}

// Guardar proyecto (crear o editar)
function saveProyecto() {
    if (isEdit.value) {
        router.put(`/proyectos/${form.id_proyecto}`, {
            nombre: form.nombre
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            }
        })
    } else {
        router.post('/proyectos', {
            nombre: form.nombre
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            }
        })
    }
}

// Eliminar proyecto
function deleteProyecto(id) {
    if (confirm('¿Estás seguro de eliminar este proyecto?')) {
        router.delete(`/proyectos/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                // el flash ya lo muestra el watcher
            }
        })
    }
}
</script>

<template>
    <Head title="Proyectos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listado de Proyectos</h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <button @click="openCreateModal"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Agregar nuevo
                </button>
            </div>

            <table class="min-w-full bg-white border border-gray-200 shadow rounded">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Nombre</th>
                        <th class="px-4 py-2 border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(proyecto, index) in props.proyectos" :key="proyecto.id_proyecto" class="text-sm">
                        <td class="px-4 py-2 border">{{ index + 1 }}</td>
                        <td class="px-4 py-2 border">{{ proyecto.nombre }}</td>
                        <td class="px-4 py-2 border">
                            <button @click="openEditModal(proyecto)"
                                class="text-blue-600 hover:underline mr-2">Editar</button>
                            <button @click="deleteProyecto(proyecto.id_proyecto)"
                                class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                    <tr v-if="props.proyectos.length === 0">
                        <td colspan="3" class="text-center py-4 text-gray-500">No hay proyectos registrados</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">
                    {{ isEdit ? 'Editar Proyecto' : 'Agregar Proyecto' }}
                </h3>
                <form @submit.prevent="saveProyecto">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del proyecto</label>
                        <input v-model="form.nombre" type="text"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
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
