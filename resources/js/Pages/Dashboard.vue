<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    datosGraficos: Array
});

onMounted(() => {
    const ctx = document.getElementById('graficoPiezas');
    if (ctx) {
        const etiquetas = props.datosGraficos.map(item => item.proyecto);
        const pendientes = props.datosGraficos.map(item => item.pendientes);
        const fabricados = props.datosGraficos.map(item => item.fabricados);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [
                    {
                        label: 'Pendientes',
                        data: pendientes,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                    },
                    {
                        label: 'Fabricados',
                        data: fabricados,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    }
});
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
                    <canvas id="graficoPiezas" height="120"></canvas>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

