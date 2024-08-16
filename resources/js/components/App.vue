<template>
    <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gray-100">
      <!-- Container for input field and button -->
      <div class="flex space-x-4 mb-4">
        <!-- Input Field -->
        <input 
          v-model="inputValue"
          type="text" 
          placeholder="Enter something..." 
          class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1"
        />
        <!-- Button -->
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
            <th class="border border-gray-300 px-4 py-2">Column 1</th>
            <th class="border border-gray-300 px-4 py-2">Column 2</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="border border-gray-300 px-4 py-2">Row 1, Cell 1</td>
            <td class="border border-gray-300 px-4 py-2">Row 1, Cell 2</td>
          </tr>
          <tr>
            <td class="border border-gray-300 px-4 py-2">Row 2, Cell 1</td>
            <td class="border border-gray-300 px-4 py-2">Row 2, Cell 2</td>
          </tr>
          <tr>
            <td class="border border-gray-300 px-4 py-2">Row 3, Cell 1</td>
            <td class="border border-gray-300 px-4 py-2">Row 3, Cell 2</td>
          </tr>
          <tr>
            <td class="border border-gray-300 px-4 py-2">Row 4, Cell 1</td>
            <td class="border border-gray-300 px-4 py-2">Row 4, Cell 2</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  // Create a ref for the input value
  const inputValue = ref('');
  const data = ref(null);
  
 const fetchData = async () => {
  // Define the URL for the API endpoint and use template literals or string concatenation
  const url = `/api/IpCalculation?IP=${encodeURIComponent(inputValue.value)}`;

  try {
    // Send a GET request with the constructed URL
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
      error.value = 'Failed to fetch data';
      console.error(err);
    }
};

  </script>
  