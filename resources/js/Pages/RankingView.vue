<script setup>
    import { Head, router, Link, usePage } from '@inertiajs/vue3';
    import Rating from '@/Components/Rating.vue';
    import Pagination from '@/Components/Basic/Pagination.vue';
    import { watchEffect, ref, onMounted, onUnmounted } from 'vue';

    const props = defineProps({
        vq3Ratings: Object,
        cpmRatings: Object,
        myVq3Rating: Object,
        myCpmRating: Object,
    });

    const screenWidth = ref(window.innerWidth);
    const isRotating = ref(false);
    const interval = ref(null);
    const page = usePage();

    const startInterval = () => {
        if (interval.value == null) {
            interval.value = setInterval(updatePage, 30000);
        }
    }

    const stopInterval = () => {
        clearInterval(interval.value);
        interval.value = null;
    }

    const updatePage = () => {
        if (isRotating.value) {
            return;
        }

        isRotating.value = true;

        router.reload()

        setTimeout(() => {
            isRotating.value = false;
        }, 1500);
    }

    const resizeScreen = () => {
        screenWidth.value = window.innerWidth
    }

    onMounted(() => {
        window.addEventListener("resize", resizeScreen);
        startInterval();

        // Log the value of playerRatings prop
        // console.log(props.playerRatings);
    });

    onUnmounted(() => {
        window.removeEventListener("resize", resizeScreen);
        stopInterval();
    });
</script>

<template>
    <div>
        <Head title="Ranking" />

        <div class="max-w-8xl mx-auto pt-6 px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center flex-wrap">
                <h2 class="font-semibold text-3xl text-gray-200 leading-tight">
                    Ranking
                </h2>
            </div>
            <div class="flex mt-3">
                <div class="text-sm text-blue-400 flex items-center mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>

                    <div class="ml-2 mr-5">{{ vq3Ratings.total }} VQ3 Ratings</div>
                </div>

                <div class="text-sm text-blue-400 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>

                    <div class="ml-2 mr-5">{{ cpmRatings.total }} CPM Ratings</div>
                </div>
            </div>
        </div>

        <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">



            <div class="md:flex justify-center mb-5">
                <div class="rounded-md p-3 flex-1 bg-grayop-700 flex flex-col mr-1 justify-center">
                    <div v-if="myVq3Rating">
                        <MapRecord physics="VQ3" :record="myVq3Rating" />
                    </div>

                    <div v-else class="flex items-center justify-center text-gray-500">
                        <div v-if="page.props?.auth?.user">You have no VQ3 rating in this category</div>
                        <div v-else>You need to be logged in to see your rating</div>
                    </div>
                </div>

                <div class="rounded-md p-3 flex-1 bg-grayop-700 flex flex-col ml-1 mt-5 md:mt-0 justify-center">
                    <div v-if="myCpmRating">
                        <MapRecord physics="CPM" :record="myCpmRating" />
                    </div>

                    <div v-else class="flex items-center justify-center text-gray-500 items-center">
                        <div v-if="page.props?.auth?.user">You have no CPM rating in this category</div>
                        <div v-else>You need to be logged in to see your rating</div>
                    </div>
                </div>
            </div>


            <div class="md:flex justify-center">
                <div class="rounded-md p-3 flex-1 bg-grayop-700 flex flex-col mr-1">
                    <div v-if="vq3Ratings.total > 0">
                        <Rating v-for="rating in vq3Ratings.data" :key="rating.id" :rating="rating" />

                        <div class="flex justify-center" v-if="vq3Ratings.total > vq3Ratings.per_page">
                            <Pagination pageName="vq3Page" :last_page="vq3Ratings.last_page" :current_page="vq3Ratings.current_page" :link="vq3Ratings.first_page_url" />
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-center mt-20 text-gray-500 text-lg">
                        <div>
                            <div class="flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>There are no VQ3 Ratings</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-md p-3 flex-1 bg-grayop-700 flex flex-col ml-1 mt-5 md:mt-0">
                    <div v-if="cpmRatings.total > 0">
                        <Rating v-for="rating in cpmRatings.data" :key="rating.id" :rating="rating" />

                        <div class="flex justify-center" v-if="cpmRatings.total > cpmRatings.per_page">
                            <Pagination pageName="cpmPage" :last_page="cpmRatings.last_page" :current_page="cpmRatings.current_page" :link="cpmRatings.first_page_url" />
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-center mt-20 text-gray-500 text-lg">
                        <div>
                            <div class="flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>There are no CPM Ratings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
