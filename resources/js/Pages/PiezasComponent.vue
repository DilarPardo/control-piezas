<script setup>
import { ref, reactive } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
    piezas: Array,
    proyectos: Array,
    bloques: Array,
    usuarioNombre: String
})

const showModal = ref(false)
const isEdit = ref(false)

const form = reactive({
    pieza: '',
    peso_teorico: '',
    peso_real: '',
    estado: 'Pendiente',
    id_proyecto: '',
    id_bloque: '',
    fecha_registro: '',
    registrado_por: ''
})

const errors = reactive({
    pieza: '',
    peso_teorico: '',
    id_proyecto: '',
    id_bloque: ''
})

function openCreateModal() {
    isEdit.value = false
    Object.assign(form, {
        pieza: '',
        peso_teorico: '',
        peso_real: '',
        estado: 'Pendiente',
        id_proyecto: '',
        id_bloque: '',
        fecha_registro: new Date().toISOString().slice(0, 10),
        registrado_por: props.usuarioNombre
    })
    Object.keys(errors).forEach(k => errors[k] = '')
    showModal.value = true
}

function openEditModal(pieza) {
    isEdit.value = true
    Object.assign(form, { ...pieza })
    Object.keys(errors).forEach(k => errors[k] = '')
    showModal.value = true
}

function validateForm() {
    let valid = true
    errors.pieza = ''
    errors.peso_teorico = ''
    errors.id_proyecto = ''
    errors.id_bloque = ''

    if (!form.pieza) {
        errors.pieza = 'El nombre de la pieza es obligatorio.'
        valid = false
    } 

    if (!form.peso_teorico) {
        errors.peso_teorico = 'El peso teórico es obligatorio.'
        valid = false
    }

    if (!form.id_proyecto) {
        errors.id_proyecto = 'Debes seleccionar un proyecto.'
        valid = false
    }

    if (!form.id_bloque) {
        errors.id_bloque = 'Debes seleccionar un bloque.'
        valid = false
    }

    return valid
}

function savePieza() {
    if (!validateForm()) return

    const payload = { ...form }
    const method = isEdit.value ? 'put' : 'post'
    const url = isEdit.value ? `/piezas/${form.id_pieza}` : '/piezas'

    router[method](url, payload, {
        onSuccess: () => {
            alert(isEdit.value ? 'Pieza actualizada correctamente.' : 'Pieza creada correctamente.')
            showModal.value = false
            router.reload({ only: ['piezas'] })
        },
        onError: () => alert('Ocurrió un error en el servidor.')
    })
}

function deletePieza(id) {
    if (confirm('¿Estás seguro de eliminar esta pieza?')) {
        router.delete(`/piezas/${id}`, {
            onSuccess: () => {
                alert('Pieza eliminada correctamente.')
                router.reload({ only: ['piezas'] })
            }
        })
    }
}
</script>

<template>
    <Head title="Piezas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Listado de Piezas</h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <button @click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Agregar nueva</button>
            </div>

            <table class="min-w-full bg-white border border-gray-200 shadow rounded">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Pieza</th>
                        <th class="px-4 py-2 border">Proyecto</th>
                        <th class="px-4 py-2 border">Bloque</th>
                        <th class="px-4 py-2 border">Estado</th>
                        <th class="px-4 py-2 border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(pieza, index) in props.piezas" :key="pieza.id_pieza" class="text-sm">
                        <td class="px-4 py-2 border">{{ index + 1 }}</td>
                        <td class="px-4 py-2 border">{{ pieza.pieza }}</td>
                        <td class="px-4 py-2 border">{{ pieza.proyecto?.nombre ?? 'Sin proyecto' }}</td>
                        <td class="px-4 py-2 border">{{ pieza.bloque?.nombre_bloque ?? 'Sin bloque' }}</td>
                        <td class="px-4 py-2 border">{{ pieza.estado }}</td>
                        <td class="px-4 py-2 border">
                            <button @click="openEditModal(pieza)" class="text-blue-600 hover:underline mr-2">Editar</button>
                            <button @click="deletePieza(pieza.id_pieza)" class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow w-full max-w-md max-h-[90vh] overflow-y-auto p-6">
                <h3 class="text-lg font-semibold mb-4">{{ isEdit ? 'Editar Pieza' : 'Agregar Pieza' }}</h3>
                <form @submit.prevent="savePieza">
                    <div class="mb-2">
                        <label class="block text-sm font-medium">Nombre Pieza</label>
                        <input v-model="form.pieza" type="text" 
                            :class="['w-full border rounded px-3 py-2', errors.pieza ? 'border-red-500' : 'border-gray-300']" />
                        <p v-if="errors.pieza" class="text-red-500 text-sm mt-1">{{ errors.pieza }}</p>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Peso Teórico</label>
                        <input v-model="form.peso_teorico" type="number" step="0.01"
                            :class="['w-full border rounded px-3 py-2', errors.peso_teorico ? 'border-red-500' : 'border-gray-300']" />
                        <p v-if="errors.peso_teorico" class="text-red-500 text-sm mt-1">{{ errors.peso_teorico }}</p>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Peso Real</label>
                        <input v-model="form.peso_real" type="number" step="0.01" class="w-full border rounded px-3 py-2" />
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Estado</label>
                        <select v-model="form.estado" class="w-full border rounded px-3 py-2">
                            <option value="Fabricado">Fabricado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Proyecto</label>
                        <select v-model="form.id_proyecto"
                            :class="['w-full border rounded px-3 py-2', errors.id_proyecto ? 'border-red-500' : 'border-gray-300']">
                            <option value="" disabled>Selecciona un proyecto</option>
                            <option v-for="p in props.proyectos" :key="p.id_proyecto" :value="p.id_proyecto">{{ p.nombre }}</option>
                        </select>
                        <p v-if="errors.id_proyecto" class="text-red-500 text-sm mt-1">{{ errors.id_proyecto }}</p>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Bloque</label>
                        <select v-model="form.id_bloque"
                            :class="['w-full border rounded px-3 py-2', errors.id_bloque ? 'border-red-500' : 'border-gray-300']">
                            <option value="" disabled>Selecciona un bloque</option>
                            <option v-for="b in props.bloques" :key="b.id_bloque" :value="b.id_bloque">{{ b.nombre_bloque }}</option>
                        </select>
                        <p v-if="errors.id_bloque" class="text-red-500 text-sm mt-1">{{ errors.id_bloque }}</p>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Fecha Registro</label>
                        <input
                            v-model="form.fecha_registro"
                            type="date"
                            required
                            class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
                            disabled
                        />
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium">Registrado por</label>
                        <input
                            v-model="form.registrado_por"
                            type="text"
                            class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
                            disabled
                        />
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button @click="showModal = false" type="button" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">{{ isEdit ? 'Actualizar' : 'Guardar' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
