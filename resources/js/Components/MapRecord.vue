<script setup>
    import { Link } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const props = defineProps({
        record: Object,
        cpmrecord: Object,
        vq3record: Object,
        physics: String,
        oldtop: {
            type: Boolean,
            default: false
        }
    });

    const bestrecordCountry = computed(() => {
        let country = props.record.user?.country ?? props.record.country;

        return (country == 'XX') ? '_404' : country;
    });

    const timeDiff =  computed(() => {
        if (! props.record.besttime === -1) {
            return null;
        }

        return Math.abs(props.record.besttime - props.record.time)
    });

    const getRoute = computed(() => {
        if (props.record.user) {
            return route('profile.index', props.record.user.id);
        }

        if (props.record.mdd_id) {
            return route('profile.mdd', props.record.mdd_id);
        }

        return '#'
    })

</script>

<template>
    <div>
        <div class="flex justify-between rounded-md px-2 py-1 items-center" :class="{'bg-blackop-30 opacity-70': record.oldtop}" :title="record.oldtop ? 'Old Top Record' : ''">
            <div class="mr-4 flex items-center">
                <div class="font-bold mr-3 text-white text-lg w-11" :class="{'text-orange-400': record.oldtop}">{{ record.rank }}</div>
                <img class="h-10 w-10 rounded-full object-cover" :src="record.user?.profile_photo_path ? '/storage/' + record.user?.profile_photo_path : '/images/null.jpg'" :alt="record.user?.name ?? record.name">
                
                <div class="ml-4">
                    <Link class="flex rounded-md" :href="getRoute">
                        <div class="flex justify-between items-center">
                            <div>
                                <img :src="`/images/flags/${bestrecordCountry}.png`" class="w-5 inline mr-2" onerror="this.src='/images/flags/_404.png'" :title="bestrecordCountry">
                                <Link class="font-bold text-white" :href="getRoute" v-html="q3tohtml(record.user?.name ?? record.name)"></Link>
                            </div>
                        </div>
                    </Link>
    
                    <div class="text-gray-400 text-xs mt-2" :title="record.date_set" :class="{'text-orange-400': record.oldtop}"> {{ timeSince(record.date_set) }} ago</div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-300" :class="{'text-orange-400': record.oldtop}">{{  formatTime(record.time) }}</div>
                    <div class="text-xs text-red-500" v-if="timeDiff">- {{  formatTime(timeDiff) }}</div>
                </div>

                <div class="ml-5">
                    <div class="text-white rounded-full text-xs px-2 py-0.5 uppercase font-bold" :class="{'bg-green-700': record.gametype.includes('cpm'), 'bg-blue-600': !record.gametype.includes('cpm')}">
                        <div>{{ physics }}</div>
                    </div>

                    <!-- <div class="rounded-full text-xs px-2 py-0.5 uppercase font-bold border-2 border-gray-500 text-gray-500 mt-1">
                        <div>{{ record.mode }}</div>
                    </div> -->
                </div>
            </div>
        </div>
    
        <hr class="my-1 text-gray-700 border-gray-700 bg-gray-700">
    </div>
</template>