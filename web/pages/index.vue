<template>
    <SearchBar />
    <section class="mt-10">
        <div class="bg-indigo-600 min-h-52 mx-5 rounded-[20px] bg-cover bg-center bg-no-repeat p-5 flex justify-between flex-col" 
             style="background-image: url('/images/moutain-rider.jpg');">
             <h2 class="text-indigo-600 text-2xl w-[50%]">Discover the Electra bikes</h2>
             <button class="bg-indigo-600 text-indigo-50 py-2 rounded-[20px] w-[50%]">
                Discover
             </button>
        </div>
        <div class="grid grid-cols-2 mt-5 gap-5">
            <div class="bg-indigo-600 ml-5 rounded-[20px] aspect-square">

            </div>
            <div class="bg-indigo-600 mr-5 rounded-[20px] aspect-square">

            </div>
        </div>
    </section>
</template>

<style scoped>
    h2, button{
        font-family: NeuePower;
    }
</style>