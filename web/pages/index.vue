    <template>
        <SearchBar class="lg:mx-20 xl:mx-40"/>
        <!-- Les 3 boutons d'accueil -->
        <section class="my-10 mx-5 xl:mx-40 lg:mx-20 lg:h-96 lg:grid lg:grid-cols-[66%_33%] lg:gap-5">
            <NuxtLink :to="{ path: '/search', query: { brand: '1' } }">
            <div class="bg-indigo-600 min-h-52 lg:h-96 rounded-[20px] bg-cover bg-center bg-no-repeat p-5 flex justify-between flex-col" 
                style="background-image: url('/images/moutain-rider.jpg');">
                <h2 class="text-indigo-600 max-lg:text-2xl min-lg:text-5xl w-[50%]">Discover the Electra bikes</h2>
                <button class="bg-indigo-600 text-indigo-50 py-2 rounded-[20px] w-1/2 lg:w-1/3">
                    Discover
                </button>
            </div>
            </NuxtLink>
            <div class="grid max-lg:grid-cols-2 max-lg:mt-5 gap-5 lg:grid-rows-2 overflow-hidden">
                <NuxtLink to="/stores">
                <div class="bg-indigo-600 rounded-[20px] max-lg:aspect-square bg-cover bg-center bg-no-repeat p-5 h-full"
                style="background-image: url('/images/look-sky.png');">
                    <h2 class="text-indigo-600 text-2xl min-lg:text-3xl w-[50%]">Our Stores</h2>
                </div>
                </NuxtLink>
                <NuxtLink :to="{ path: '/search', query: { category: '7' } }">
                <div class="bg-indigo-600 rounded-[20px] max-lg:aspect-square bg-cover bg-center bg-no-repeat p-5 h-full"
                style="background-image: url('/images/grass-bike.jpg');">
                    <h2 class="text-indigo-600 text-2xl min-lg:text-3xl w-[50%]">Road Bikes</h2>
                </div>
                </NuxtLink>
            </div>
        </section>
        <!-- Des Produits mis en avant -->
        <section class="grid grid-cols-2 min-lg:grid-cols-5 gap-5 min-lg:mx-40 max-lg:mx-5">
            <!-- Mobile Card -->
            <div v-if="data && data.data" v-for="product in data.data">
                <MobileCard :product="product"/>
            </div>
            
        </section>
        <h2 class="text-2xl lg:text-4xl m-10 flex justify-center text-indigo-950">Our Stores</h2>
        <div class="mx-5 lg:mx-20 xl:mx-40 max-lg:aspect-square lg:h-[50vh] mb-10">
            <LMap
            ref="map"
            :zoom="zoom"
            :center="mapCenter"
            :use-global-leaflet="false">
                <LTileLayer
                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                    attribution="&amp;copy; <a href=&quot;https://www.openstreetmap.org/&quot;>OpenStreetMap</a> contributors"
                    layer-type="base"
                    name="OpenStreetMap"
                />
                <LMarker 
                v-for="store in storesWithCoordinates" 
                :key="store.store.store_id" 
                :lat-lng="[store.lat, store.lng]">
                    <LPopup>
                        <div>
                        <h3 class="font-bold">{{ store.store.store_name }}</h3>
                        <p>{{ store.store.street }}, {{ store.store.city }}</p>
                        <p>{{ store.store.state }} {{ store.store.zip_code }}</p>
                        <p>{{ store.store.phone }}</p>
                        </div>
                    </LPopup>
                </LMarker>
                <!-- La position du client -->
                <LMarker
                    key="user"
                    :lat-lng="[userPosition.lat, userPosition.lng]">
                    <LPopup>
                        <div>
                            <p>Votre position</p>
                        </div>
                    </LPopup>
                </LMarker>
            </LMap>
        </div>
    </template>


    <script setup>

    const zoom = ref(6);
    const mapCenter = ref([39.8283, -98.5795]); // Centre des États-Unis par défaut
    const { data, error } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks/page/1');
    const {data:storesData} = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores');

    //Géolocalisation de l'utilisateur
    const userPosition = ref({lat: 0, lng: 0});
    
    if (process.client && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userPosition.value = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude

                };
                // Par exemple, centrer la carte sur l'utilisateur :
                mapCenter.value = [userPosition.value.lat, userPosition.value.lng];
            },
            (error) => {
                console.warn('Geolocation error:', error);
            }
        );
    }


    // Stocker les magasins avec leurs coordonnées
    const storesWithCoordinates = ref([]);

    if(storesData.value){
        (async () => {
            for (const store of storesData.value) {
                const { data:coords } = await useLazyFetch(`https://nominatim.openstreetmap.org/search?q=${store.street} ${store.city}&format=json&limit=1`);
                storesWithCoordinates.value.push({
                    store: store,
                    lat: coords.value[0].lat,
                    lng: coords.value[0].lon
                })
            }
        })();
    }


    </script>