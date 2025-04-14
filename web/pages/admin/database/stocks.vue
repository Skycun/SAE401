<template>
    <h2 class="flex justify-center text-indigo-950 mt-10 text-3xl">Stocks</h2>
    <div class="m-5 bg-white rounded-[20px] p-5 ">
        <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Action</h2>
        <USelectMenu label="Select your action" v-model="action" placeholder="Select an action" class="w-full mb-2" :items="[
            {
                label: 'Add',
                value: 'add'
            },
            {
                label: 'Edit',
                value: 'edit'
            },
            {
                label: 'Delete',
                value: 'delete'
            }
        ]"/>
    </div>
    <div v-if="action">
        <div v-if="action.value !='add'" class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Select stock to manage</h2>
            <UFormField label="Store" required>
                <USelectMenu label="Select a store" v-model="selectedStore" :loading="loadingStores" placeholder="Select a store" class="w-full mb-2" @change="fetchStoreStocks" :items="selectStores"/>
            </UFormField>
            <div v-if="selectedStore && selectedStore.value">
                <UFormField label="Product Stock" required>
                    <USelectMenu label="Select a product stock" v-model="selectedStock" :loading="loadingStocks" placeholder="Select a product stock" class="w-full mb-2" @change="fetchSelectedData" :items="selectStocks"/>
                </UFormField>
                <div v-if="action.value == 'delete' && selectedStock && selectedStock.value != null">
                    <Button class="p-3 mt-5 bg-red-600" @click="deleteStock">Delete</Button>
                </div>
            </div>
        </div>
        <div v-if="action.value == 'add'" class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Add a stock</h2>
            <UFormField label="Store" required>
                <USelectMenu label="Select a store" v-model="modelData.store_id" :loading="loadingStores" placeholder="Select a store" class="w-full mb-2" :items="selectStores"/>
            </UFormField>
            <UFormField label="Product" required>
                <USelectMenu label="Select a product" v-model="modelData.product_id" :loading="loadingProducts" placeholder="Select a product" class="w-full mb-2" :items="selectProducts"/>
            </UFormField>
            <UFormField label="Quantity" required>
                <UInput type="number" label="Quantity" placeholder="Enter the stock quantity" v-model.number="modelData.quantity" class="w-full mb-2"/>
            </UFormField>
            <Button class="p-3 mt-5" @click="addStock">Add</Button>
        </div>
        <div v-if="action.value == 'edit' && selectedStock && selectedStock.value != null" class="m-5 bg-white rounded-[20px] p-5 ">
            <h2 class="text-indigo-950 text-xl flex justify-center items-center mb-5">Edit a stock</h2>
            <UFormField label="Store" disabled>
                <UInput label="Store" :model-value="fetchedSelectedData.store?.store_name || ''" disabled class="w-full mb-2"/>
            </UFormField>
            <UFormField label="Product" disabled>
                <UInput label="Product" :model-value="fetchedSelectedData.product?.product_name || ''" disabled class="w-full mb-2"/>
            </UFormField>
            <UFormField label="Quantity" required>
                <UInput type="number" label="Quantity" placeholder="Enter the stock quantity" v-model.number="fetchedSelectedData.quantity" class="w-full mb-2"/>
            </UFormField>
            <Button class="p-3 mt-5" @click="editStock">Edit</Button>
        </div>
    </div>
</template>

<script setup>
const action = ref("");
const toast = useToast();
const loading = ref(false);

// Stores data
const stores = ref([]);
const selectStores = ref([]);
const loadingStores = ref(false);
const selectedStore = ref(null);

// Products data
const products = ref([]);
const selectProducts = ref([]);
const loadingProducts = ref(false);

// Stocks data
const stocks = ref([]);
const selectStocks = ref([]);
const loadingStocks = ref(false);
const selectedStock = ref(null);
const fetchedSelectedData = ref({});

const modelData = ref({
    store_id: null,
    product_id: null,
    quantity: 0
});

// Fetch initial data
fetchStores();
fetchProducts();

async function fetchStores() {
    loadingStores.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stores', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingStores.value = false;
    
    if (data.value) {
        stores.value = data.value;
        selectStores.value = data.value.map((item) => {
            return {
                label: item.store_name,
                value: item.store_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load stores",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchProducts() {
    loadingProducts.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/products', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingProducts.value = false;
    
    if (data.value) {
        products.value = data.value;
        selectProducts.value = data.value.map((item) => {
            return {
                label: `${item.product_name} - ${item.list_price}$`,
                value: item.product_id
            }
        });
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load products",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function fetchStoreStocks() {
    if (!selectedStore.value || !selectedStore.value.value) return;
    
    loadingStocks.value = true;
    const { data } = await useLazyFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks?action=getFromStore&store_id=${selectedStore.value.value}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loadingStocks.value = false;
    
    if (data.value) {
        stocks.value = data.value;
        selectStocks.value = data.value.map((item) => {
            // Add null check before accessing product properties
            if (!item.product) {
                console.error('Missing product data for stock:', item);
                // Store product_id if available in the item itself
                return {
                    label: `Product ID: ${item.product_id || 'Unknown'} - Quantity: ${item.quantity || 0}`,
                    value: item.stock_id,
                    product_id: item.product_id || null
                }
            }
            return {
                label: `${item.product.product_name} - Quantity: ${item.quantity}`,
                value: item.stock_id,
                product_id: item.product.product_id
            }
        });
        
        // Reset selected stock
        selectedStock.value = null;
        fetchedSelectedData.value = {};
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load stocks for this store",
            color: 'error',
            icon: 'bx:x'
        });
    }
}
async function fetchSelectedData() {
    if (!selectedStock.value || !selectedStock.value.value) return;
    
    loading.value = true;
    const { data } = await useLazyFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks?action=get&id=${selectedStock.value.value}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    loading.value = false;
    
    if (data.value) {
        // Store the response
        fetchedSelectedData.value = data.value;
        
        // If product data is missing, find it from the stocks list
        if (!fetchedSelectedData.value.product) {
            console.log("Product data missing from API response, attempting to find it");
            
            // Find the matching stock in our already loaded stocks
            const matchingStock = stocks.value.find(stock => 
                stock.stock_id === fetchedSelectedData.value.stock_id);
            
            if (matchingStock && matchingStock.product) {
                // Copy the product data from our list
                fetchedSelectedData.value.product = matchingStock.product;
                console.log("Found product data from stocks list:", matchingStock.product);
            } else {
                // If we still don't have product data, fetch product_id through another method
                console.log("Attempting to find product_id through alternative method");
                // Get product_id from the stocks list item where the label matches our selection
                const selectedStockItem = selectStocks.value.find(item => 
                    item.value === selectedStock.value.value);
                
                if (selectedStockItem) {
                    // Extract product ID using regex from label: "Product Name - Quantity: X"
                    // This is a fallback and might not be reliable
                    console.log("Selected stock label:", selectedStockItem.label);
                }
            }
        }
    } else {
        toast.add({
            title: 'Error',
            description: "Failed to load stock details",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function addStock() {
    // Validation
    if (!modelData.value.store_id || !modelData.value.product_id || modelData.value.quantity < 0) {
        toast.add({
            title: 'Error',
            description: "All fields are required and quantity must be positive",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    loading.value = true;
    const { data } = await useLazyFetch('https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            store_id: parseInt(modelData.value.store_id.value),
            product_id: parseInt(modelData.value.product_id.value),
            quantity: parseInt(modelData.value.quantity)
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Stock added successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Reset form
        modelData.value = {
            store_id: null,
            product_id: null,
            quantity: 0
        };
        
        // If the store is the currently selected one, refresh its stocks
        if (selectedStore.value && modelData.value.store_id && 
            selectedStore.value.value === modelData.value.store_id.value) {
            fetchStoreStocks();
        }
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to add stock",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function editStock() {
    if (!selectedStock.value || !selectedStock.value.value) return;
    
    // Validation
    if (fetchedSelectedData.value.quantity < 0) {
        toast.add({
            title: 'Error',
            description: "Quantity must be positive",
            color: 'error',
            icon: 'bx:x'
        });
        return;
    }

    // Get product_id from different sources
    let product_id;
    
    // First try: from fetchedSelectedData
    if (fetchedSelectedData.value.product?.product_id) {
        product_id = fetchedSelectedData.value.product.product_id;
    } 
    // Second try: directly from response if available
    else if (fetchedSelectedData.value.product_id) {
        product_id = fetchedSelectedData.value.product_id;
    }
    // Third try: from the stocks array
    else {
        const matchingStock = stocks.value.find(stock => 
            stock.stock_id === fetchedSelectedData.value.stock_id);
        if (matchingStock && (matchingStock.product_id || matchingStock.product?.product_id)) {
            product_id = matchingStock.product_id || matchingStock.product.product_id;
        } else {
            toast.add({
                title: 'Error',
                description: "Cannot determine product ID. Please reload and try again.",
                color: 'error',
                icon: 'bx:x'
            });
            return;
        }
    }

    loading.value = true;
    const { data } = await useLazyFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        },
        body: {
            id: selectedStock.value.value,
            store_id: fetchedSelectedData.value.store.store_id,
            product_id: product_id,
            quantity: parseInt(fetchedSelectedData.value.quantity)
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Stock updated successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Refresh the store's stocks
        fetchStoreStocks();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to update stock",
            color: 'error',
            icon: 'bx:x'
        });
    }
}

async function deleteStock() {
    if (!selectedStock.value || !selectedStock.value.value) return;
    
    loading.value = true;
    const { data } = await useLazyFetch(`https://mirrorsoul.alwaysdata.net/sae401/API/API/stocks/${selectedStock.value.value}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Api': 'e8f1997c763'
        }
    });
    loading.value = false;
    
    if (data.value && !data.value.error) {
        toast.add({
            title: 'Success',
            description: "Stock deleted successfully",
            color: 'success',
            icon: 'bx:check'
        });
        
        // Reset selection and refresh stocks
        selectedStock.value = null;
        fetchedSelectedData.value = {};
        fetchStoreStocks();
    } else {
        toast.add({
            title: 'Error',
            description: data.value?.error || "Failed to delete stock",
            color: 'error',
            icon: 'bx:x'
        });
    }
}
</script>