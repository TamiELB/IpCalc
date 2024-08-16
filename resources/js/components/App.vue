<template>
  <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gray-100">
    <div class="flex space-x-4 mb-4">

      <input 
        v-model="inputValue"
        type="text" 
        placeholder="Enter something..." 
        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1"
      />

      <button 
        @click="fetchData"
        class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        Submit
      </button>
    </div>

    <!-- Table -->
    <table class="w-full max-w-md border-collapse border border-gray-200">
      <thead>
        <tr>
          <th class="border border-gray-300 px-4 py-2 text-left">Label</th>
          <th class="border border-gray-300 px-4 py-2">Value</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="data">
          <td class="border border-gray-300 px-4 py-2">Network</td>
          <td class="border border-gray-300 px-4 py-2 text-center text-blue-500">{{ data.network }}</td>
        </tr>
        <tr v-if="data">
          <td class="border border-gray-300 px-4 py-2">First Address</td>
          <td class="border border-gray-300 px-4 py-2 text-center text-blue-500">{{ data.first }}</td>
        </tr>
        <tr v-if="data">
          <td class="border border-gray-300 px-4 py-2">Last Address</td>
          <td class="border border-gray-300 px-4 py-2 text-center text-blue-500">{{ data.last }}</td>
        </tr>
        <tr v-if="data">
          <td class="border border-gray-300 px-4 py-2">Total Hosts</td>
          <td class="border border-gray-300 px-4 py-2 text-center text-blue-500">{{ data.hosts }}</td>
        </tr>
        <!-- Optionally show a message if no data is available -->
        <tr v-if="!data">
          <td colspan="2" class="border border-gray-300 px-4 py-2 text-center">No data available</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Create refs for input value and fetched data
const inputValue = ref('');
const data = ref(null);

// Function to fetch data from API
const fetchData = async () => {
  // Define the URL for the API endpoint
  const url = `/api/IpCalculation?IP=${encodeURIComponent(inputValue.value)}`;

  try {
    const response = await fetch(url);

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    // Parse the JSON from the response
    const result = await response.json();
    console.log(result);

    // Store the result if needed
    data.value = result;
  } catch (err) {
    // Handle any errors that occur during the fetch
    console.error('Failed to fetch data', err);
  }
};
</script>
