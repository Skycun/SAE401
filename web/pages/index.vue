    <template>
        <SearchBar />
        <!-- Les 3 boutons d'accueil -->
        <section class="mt-10 mb-10">
            <NuxtLink :to="{ path: '/search', query: { brand: '1' } }">
            <div class="bg-indigo-600 min-h-52 mx-5 rounded-[20px] bg-cover bg-center bg-no-repeat p-5 flex justify-between flex-col" 
                style="background-image: url('/images/moutain-rider.jpg');">
                <h2 class="text-indigo-600 text-2xl w-[50%]">Discover the Electra bikes</h2>
                <button class="bg-indigo-600 text-indigo-50 py-2 rounded-[20px] w-[50%]">
                    Discover
                </button>
            </div>
            </NuxtLink>
            <div class="grid grid-cols-2 mt-5 gap-5">
                <NuxtLink to="/stores">
                <div class="bg-indigo-600 ml-5 rounded-[20px] aspect-square bg-cover bg-center bg-no-repeat p-5"
                style="background-image: url('/images/look-sky.png');">
                    <h2 class="text-indigo-600 text-2xl">Our Stores</h2>
                </div>
                </NuxtLink>
                <NuxtLink :to="{ path: '/search', query: { category: '7' } }">
                <div class="bg-indigo-600 mr-5 rounded-[20px] aspect-square bg-cover bg-center bg-no-repeat p-5"
                style="background-image: url('/images/grass-bike.jpg');">
                    <h2 class="text-indigo-600 text-2xl">Road Bikes</h2>
                </div>
                </NuxtLink>
            </div>
        </section>
        <!-- Des Produits mis en avant -->
        <section class="grid grid-cols-2 px-5 gap-5">
            <!-- Mobile Card -->
            <div v-if="data && data.data" v-for="product in data.data">
                <MobileCard :product="product"/>
            </div>
            
        </section>
        <h2 class="text-2xl m-10 flex justify-center text-indigo-950">Our Stores</h2>
        <div class="mx-5 rounded-[20px] aspect-square mb-10">
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
        {{ userPosition }}
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