<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const countries = ref([]);
const errorMessage = ref('');
const isLoading = ref(false);
const controller = ref(null);

const displayedCountries = computed(() => countries.value.slice(0, 8));

const loadCountries = async () => {
    controller.value?.abort();
    const requestController = new AbortController();

    controller.value = requestController;
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/api/general/countries', {
            headers: {
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            signal: requestController.signal,
        });

        if (!response.ok) {
            throw new Error(`The countries request failed with status ${response.status}.`);
        }

        const payload = await response.json();

        if (!Array.isArray(payload.data)) {
            throw new Error('The countries response was not in the expected format.');
        }

        countries.value = payload.data;
    } catch (error) {
        if (error instanceof DOMException && error.name === 'AbortError') {
            return;
        }

        errorMessage.value = 'The API could not be reached. Check the server and try again.';
    } finally {
        if (controller.value === requestController) {
            isLoading.value = false;
        }
    }
};

onMounted(loadCountries);
onBeforeUnmount(() => controller.value?.abort());
</script>

<template>
    <main class="min-h-screen bg-slate-950 px-5 py-10 text-slate-100 sm:px-8 lg:px-12">
        <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl flex-col justify-between">
            <header class="flex items-center justify-between border-b border-white/10 pb-6">
                <a href="/" class="flex items-center gap-3" aria-label="Vue starter home">
                    <span class="grid size-10 place-items-center rounded-xl bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-400/20">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7.5h10.5m0 0L11 4m3.5 3.5L11 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M20 16.5H9.5m0 0L13 13m-3.5 3.5L13 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="text-sm font-semibold tracking-wide text-white">Laravel + Vue</span>
                </a>

                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-medium text-emerald-300">
                    <span class="size-1.5 rounded-full bg-emerald-300"></span>
                    Frontend connected
                </span>
            </header>

            <div class="grid flex-1 items-center gap-14 py-16 lg:grid-cols-[minmax(0,1fr)_minmax(22rem,0.8fr)]">
                <section>
                    <p class="mb-5 text-sm font-semibold uppercase tracking-[0.22em] text-emerald-300">Setup complete</p>
                    <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-white sm:text-6xl lg:text-7xl">
                        Vue is ready.
                    </h1>
                    <p class="mt-7 max-w-xl text-lg leading-8 text-slate-400">
                        The Laravel backend and Vue frontend now share one application, with the existing API ready to power the interface.
                    </p>

                    <div class="mt-10 flex flex-wrap gap-3 text-sm text-slate-300">
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Vue 3</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Vite 8</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Tailwind CSS 4</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Laravel API</span>
                    </div>
                </section>

                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-2xl shadow-black/30 backdrop-blur" aria-labelledby="countries-title">
                    <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.18em] text-slate-500">Live API data</p>
                            <h2 id="countries-title" class="mt-1 text-lg font-semibold text-white">Active countries</h2>
                        </div>
                        <span v-if="!isLoading && !errorMessage" class="rounded-full bg-white/5 px-3 py-1 text-xs text-slate-400">
                            {{ countries.length }} total
                        </span>
                    </div>

                    <div class="min-h-80 p-3" aria-live="polite">
                        <div v-if="isLoading" class="space-y-2" aria-label="Loading countries">
                            <div v-for="item in 6" :key="item" class="flex animate-pulse items-center gap-3 rounded-2xl px-3 py-2.5">
                                <span class="size-10 rounded-xl bg-white/10"></span>
                                <span class="h-4 w-32 rounded bg-white/10"></span>
                            </div>
                        </div>

                        <div v-else-if="errorMessage" class="grid min-h-72 place-items-center px-5 text-center">
                            <div>
                                <div class="mx-auto grid size-11 place-items-center rounded-2xl bg-rose-400/10 text-rose-300">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <p class="mt-4 text-sm leading-6 text-slate-400">{{ errorMessage }}</p>
                                <button type="button" class="mt-5 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-slate-950" @click="loadCountries">
                                    Try again
                                </button>
                            </div>
                        </div>

                        <div v-else-if="countries.length === 0" class="grid min-h-72 place-items-center px-5 text-center text-sm text-slate-400">
                            No active countries are available yet.
                        </div>

                        <ul v-else class="space-y-1">
                            <li v-for="country in displayedCountries" :key="country.id" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition hover:bg-white/5">
                                <span class="grid size-10 shrink-0 place-items-center rounded-xl border border-white/10 bg-slate-900 text-xs font-bold tracking-wider text-emerald-300">
                                    {{ country.attributes.iso2 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-100">{{ country.attributes.name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ country.attributes.official_name || country.attributes.iso3 }}</p>
                                </div>
                                <span class="text-xs tabular-nums text-slate-600">{{ country.attributes.numeric_code || '—' }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="border-t border-white/10 px-6 py-4 text-xs text-slate-500">
                        GET /api/general/countries
                    </div>
                </section>
            </div>

            <footer class="border-t border-white/10 py-6 text-sm text-slate-500">
                Ready for the next screen.
            </footer>
        </div>
    </main>
</template>
