<template>
    <SearchBar />
    <!-- Les 3 boutons d'accueil -->
    <section class="mt-10 mb-10">
        <div class="bg-indigo-600 min-h-52 mx-5 rounded-[20px] bg-cover bg-center bg-no-repeat p-5 flex justify-between flex-col" 
             style="background-image: url('/images/moutain-rider.jpg');">
             <h2 class="text-indigo-600 text-2xl w-[50%]">Discover the Electra bikes</h2>
             <button class="bg-indigo-600 text-indigo-50 py-2 rounded-[20px] w-[50%]">
                Discover
             </button>
        </div>
        <div class="grid grid-cols-2 mt-5 gap-5">
            <div class="bg-indigo-600 ml-5 rounded-[20px] aspect-square bg-cover bg-center bg-no-repeat p-5"
            style="background-image: url('/images/look-sky.png');">
                <h2 class="text-indigo-600 text-2xl">Our Stores</h2>
            </div>
            <div class="bg-indigo-600 mr-5 rounded-[20px] aspect-square bg-cover bg-center bg-no-repeat p-5"
            style="background-image: url('/images/grass-bike.jpg');">
                <h2 class="text-indigo-600 text-2xl">Road Bikes</h2>
            </div>
        </div>
    </section>
    <!-- Des Produits mis en avant -->
    <section class="grid grid-cols-2 px-5 gap-5">
        <!-- Mobile Card -->
         <div v-for="product in data.data">
            <MobileCard :product="product"/>
         </div>
    </section>
    <h2 class="text-2xl m-10 flex justify-center">Our Stores</h2>
    <div class="mx-5 rounded-[20px] aspect-square mb-10">
        <LMap
        ref="map"
        :zoom="zoom"
        :center="[33.96, -117.82]"
        :use-global-leaflet="false">
            <LTileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution="&amp;copy; <a href=&quot;https://www.openstreetmap.org/&quot;>OpenStreetMap</a> contributors"
                layer-type="base"
                name="OpenStreetMap"
            />
            <LMarker 
            v-for="store in storesWithCoordinates" 
            :key="store.store_id" 
            :lat-lng="[store.lat, store.lng]">
                <LPopup>
                    <div>
                    <h3 class="font-bold">{{ store.store_name }}</h3>
                    <p>{{ store.street }}, {{ store.city }}</p>
                    <p>{{ store.state }} {{ store.zip_code }}</p>
                    <p>{{ store.phone }}</p>
                    </div>
                </LPopup>
            </LMarker>
        </LMap>
    </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';

const zoom = ref(6);
const mapCenter = ref([39.8283, -98.5795]); // Centre des États-Unis par défaut
const {data} = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks/page/1');
const {data:storesData} = await useFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores');

// Stocker les magasins avec leurs coordonnées
const storesWithCoordinates = ref([]);

// Récupérer les coordonnées pour un magasin
const getStoreCoordinates = async (store) => {
  try {
    const queryParams = new URLSearchParams({
      street: store.street || '',
      city: store.city || '',
      state: store.state || '',
      postalcode: store.zip_code || '',
      format: 'jsonv2',
      limit: 1
    });
    
    // Attendre 1 seconde entre les requêtes pour respecter la limite de débit de l'API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    const response = await fetch(`https://nominatim.openstreetmap.org/search.php?${queryParams}`);
    const data = await response.json();
    
    if (data && data.length > 0) {
      return {
        ...store,
        lat: parseFloat(data[0].lat),
        lng: parseFloat(data[0].lon)
      };
    } else {
      console.warn(`No coordinates found for store: ${store.store_name}`);
      return {
        ...store,
        lat: 39.8283 + (Math.random() - 0.5) * 10, // Coordonnées aléatoires si non trouvées
        lng: -98.5795 + (Math.random() - 0.5) * 20
      };
    }
  } catch (error) {
    console.error(`Error getting coordinates for ${store.store_name}:`, error);
    return {
      ...store,
      lat: 39.8283 + (Math.random() - 0.5) * 10,
      lng: -98.5795 + (Math.random() - 0.5) * 20
    };
  }
};

onMounted(async () => {
  if (storesData.value) {
    // Traiter les magasins par lots pour éviter de surcharger l'API
    const processStores = async () => {
      const results = [];
      
      // Traiter les magasins un par un avec un délai entre chaque requête
      for (const store of storesData.value) {
        const storeWithCoords = await getStoreCoordinates(store);
        results.push(storeWithCoords);
        
        // Mettre à jour le tableau au fur et à mesure pour une expérience utilisateur plus réactive
        storesWithCoordinates.value = [...results];
      }
      
      // Calculer le centre de la carte basé sur les coordonnées obtenues
      if (results.length > 0) {
        const latSum = results.reduce((sum, store) => sum + store.lat, 0);
        const lngSum = results.reduce((sum, store) => sum + store.lng, 0);
        mapCenter.value = [latSum / results.length, lngSum / results.length];
      }
    };
    
    processStores();
  }
});
</script>