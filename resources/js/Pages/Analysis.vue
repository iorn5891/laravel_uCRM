<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { reactive, onMounted } from 'vue';
import { getToday } from '@/Common';
import Chart from '@/Components/Chart.vue'
import ResultTable from '@/Components/ResultTable.vue'


const form = reactive({ 
    startDate: null,
    endDate: null,
    type: 'perDay',
    rfmPrms: [ 14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000,30000 ],
});
const data = reactive({});

onMounted(() => {
 form.startDate = getToday();
 form.endDate = getToday();
})

const getData = async () => {
    try{
        await axios.get('/api/analysis/', {
        params: {
            startDate: form.startDate,
            endDate: form.endDate,
            type: form.type,
            rfmPrms: form.rfmPrms,
        }
        }).then( res => {
            data.data = res.data.data;
            if(res.data.labels){ data.labels = res.data.labels }
            if(res.data.eachCount){ data.eachCount = res.data.eachCount }
            data.totals = res.data.totals;
            data.type = res.data.type;
        })
    } catch (e){
        console.log(e.message)
    } 
}
</script>

<template>
    <Head title="データ分析" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">データ分析</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    分析方法<br>
                    <input type="radio" v-model="form.type" value="perDay" checked><span class="m-2">日別</span>
                    <input type="radio" v-model="form.type" value="perMonth"><span class="m-2">月別</span>
                    <input type="radio" v-model="form.type" value="perYear"><span class="m-2">年別</span>
                    <input type="radio" v-model="form.type" value="decile"><span class="m-2">デシル分析</span>
                    <input type="radio" v-model="form.type" value="rfm"><span class="m-2">RFM分析</span>
                    <form @submit.prevent="getData">
                        From: <input type="date" name="startDate" v-model="form.startDate">
                        To: <input type="date" name="endDate" v-model="form.endDate">
                    <div v-if="form.type === 'rfm' ">
                        <table class="mx-auto ry-8">
                            <thead>
                                <tr>
                                    <th>ランク</th>
                                    <th>R (○日以内)</th>
                                    <th>F (○回以上)</th>
                                    <th>M (○円以上)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5</td>
                                    <td><input type="number" v-model="form.rfmPrms[0]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[4]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[8]"></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td><input type="number" v-model="form.rfmPrms[1]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[5]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[9]"></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><input type="number" v-model="form.rfmPrms[2]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[6]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[10]"></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><input type="number" v-model="form.rfmPrms[3]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[7]"></td>
                                    <td><input type="number" v-model="form.rfmPrms[11]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> 
                    <button class="mt-10 flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">分析する</button>
                    </form>

                    <div v-show="data.data">
                        <div v-if="data.type != 'rfm' "> 
                            <Chart :data="data"/>
                        </div>
                        <div v-if="data.type === 'rfm'" >
                            合計 {{ data.totals }} 人
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">ランク</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">R</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">M</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="rfm in data.eachCount" :key="rfm.rank">
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.rank }}</td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.r }}</td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.f }}</td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ rfm.m }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">rRank</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_5</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_4</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_3</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_2</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">f_1</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="rf in data.data" :key="rf.rRank">
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.rRank }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_5 }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_4 }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_3 }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_2 }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ rf.f_1 }}</td>
                                </tr> 
                            </tbody>
                            </table>
                        </div>
                        <ResultTable :data="data"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
